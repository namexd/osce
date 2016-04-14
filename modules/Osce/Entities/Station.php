<?php
/**
 * 考站
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 10:31
 */

namespace Modules\Osce\Entities;

use DB;
use Modules\Osce\Repositories\Common;

class Station extends CommonModel
{

    protected $connection   = 'osce_mis';
    protected $table        = 'station';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['name', 'type', 'subject_id', 'paper_id', 'description', 'code'];

    /**
     * 考站与老师的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stationTeacher()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Teacher','station_teacher','station_id','teacher_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function room()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Room','room_station','station_id','room_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function roomStation(){
        return $this->hasOne('\Modules\Osce\Entities\RoomStation','station_id','id');
    }

    /**
     * 与摄像机_考站表的关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vcrStation()
    {
        return $this->belongsToMany('Modules\Osce\Entities\Vcr', 'station_vcr', 'station_id', 'vcr_id');
    }

    public function subject()
    {
        return $this->hasOne('Modules\Osce\Entities\Subject' , 'id' , 'subject_id');
    }
    /**
     * 获得station列表
     * @param array $order
     * @return mixed
     * @throws \Exception
     */
    public function showList(array $stationIdArray = [],  $ajax = false, $name ='')
    {
        try {
            $builder = $this;

            //如果传入了stationArray，就排除里面的内容
            if ($stationIdArray != []) {
                $builder = $builder->whereNotIn($this->table.'.id',$stationIdArray);
            }
            if($name != ''){
                $builder = $builder->where($this->table.'.name', 'like', '%\\' . $name.'%');
            }

            //开始查询
            $builder = $builder->select([
                $this->table.'.id',
                $this->table.'.name',
                $this->table.'.code',
                $this->table.'.type',
                $this->table.'.description',
                $this->table.'.subject_id',
                'subject.title'
            ])
                ->leftJoin ('subject', function ($join) {
                    $join->on('subject.id' , '=' , $this->table.'.subject_id');
                })
                ->orderBy($this->table.'.created_at', 'desc');

            if ($ajax === true) {
                return $builder->get();
            }

            return $builder->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 将数据插入各表,创建考室
     * @param $formData 0为$placeData,1为$vcrId,2为$caseId
     * @return bool
     * @throws \Exceptio
     * @throws \Exception
     */
    public function addStation($formData)
    {
        //开启事务
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            list($stationData, $vcrId, $roomId) = $formData;
            //将station表的数据插入station表
            $stationResult = $this->create($stationData);

            if (!$stationResult) {
                throw new \Exception('将数据写入考站失败！');
            }
            //获得插入后的id
            $station_id = $stationResult->id;

            //处理考站与摄像机的关联
            $this->handleStationVcr($vcrId, $station_id);
            //处理 考站与对应考场的关系
            $this->handleRoomStation($roomId, $station_id);

            $connection->commit();
            return $stationResult;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }


    /**
     * 为编辑着陆页准备的回显数据
     * @param $id
     * @return mixed
     */
    public function rollMsg($id)
    {
        $builder = $this->leftJoin('station_vcr',
            function ($join) use ($id){
                $join->on('station_vcr.station_id', '=', $this->table . '.id');

            }
        )->leftJoin('station_case',
            function ($join) use ($id){
                $join->on('station_case.station_id', '=', $this->table . '.id');

            }
        )->leftJoin('room_station',
            function ($join) use ($id) {
                $join->on('room_station.station_id' , '=', $this->table . '.id');

            }
        )->where($this->table . '.id', '=' ,$id)->where($this->table . '.id', '=' ,$id)
         ->where($this->table . '.id', '=' ,$id);

        $builder->select([
            $this->table . '.id as id',
            $this->table . '.name as name',
            $this->table . '.type as type',
            $this->table . '.create_user_id as create_user_id',
            $this->table . '.subject_id as subject_id',
            $this->table . '.paper_id as paper_id',
            $this->table . '.description as description',
            $this->table . '.code as code',
            'station_vcr.vcr_id as vcr_id',
            'station_case.case_id as case_id',
            'room_station.room_id as room_id'
        ]);

        return $builder->first();
    }

    /**
     * 更新的方法
     * @param $formData
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function updateStation($formData, $id)
    {
        //开启事务
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {
            //判断当前考站是否已关联到考试流程中
            $examFlowStation = ExamFlowStation::where('station_id','=',$id)->first();
            if (!is_null($examFlowStation)){
                throw new \Exception('此考站已关联到考试流程中，不能做修改、保存操作！请点取消键返回！');
            }

            list($stationData, $vcrId, $roomId) = $formData;

            //修改 考站与对应摄像机的关系
            $this->handleStationVcr($vcrId, $id);
            //修改 考站与对应考场的关系
            $this->handleRoomStation($roomId, $id);

            //修改station表，   //获得修改后的信息
            $result = $this->where('id', '=', $id)->update($stationData);
            if (!$result) {
                throw new \Exception('更改考站信息失败');
            }

            $connection->commit();
            return $result;

        } catch (\Exception $ex) {

            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 修改 考站与对应摄像机的关系
     * @param $vcrId
     * @param $id
     *
     * @version   3.4
     * @author Zhoufuxiang 2016-04-14
     * @return bool| object
     * @throws \Exception
     */
    private function handleStationVcr($vcrId, $station_id)
    {
        //通过传入的考站的id找到原来的摄像机，以及关联关系
        $StationVcr = StationVcr::where('station_id', '=', $station_id)->first();
        if (is_null($StationVcr) && $vcrId != 0)
        {
            //更改摄像机表中摄像机的状态
            $vcr = Vcr::findOrFail($vcrId);  //找到选择的摄像机
            $vcr ->used = 1;  //变更状态,但是不一定是0
            if (!$vcr->save()) {
                throw new \Exception('更改摄像机状态失败');
            }
            $stationVcrData = [
                'vcr_id'     => $vcrId,
                'station_id' => $station_id
            ];
            //在stationVcr里新增一条数据
            if (!$result = StationVcr::create($stationVcrData)) {
                throw new \Exception('创建摄像机关联失败！');
            };

        } elseif (!is_null($StationVcr)) {

            //修改其状态,将其状态重置， 将原来的摄像机的状态回位
            $Vcr = Vcr::findOrFail($StationVcr->vcr_id);
            $Vcr->used = 0;              //可能是1，也可能是其他值
            if (!$Vcr->save()) {
                throw new \Exception('更改摄像机状态失败');
            }

            if ($vcrId != 0) {
                //更改摄像机表中摄像机的状态
                $vcr = Vcr::findOrFail($vcrId);     //找到选择的摄像机
                $vcr ->used = 1;                    //变更状态,但是不一定是0
                if (!$vcr->save()) {
                    throw new \Exception('更改摄像机状态失败');
                }

                //更改与本站相关的考站_摄像头关联
                $StationVcr->vcr_id = $vcrId;
                if (!($result = $StationVcr->save())) {
                    throw new \Exception('更改考站摄像头关联失败');
                }

            }else{
                //删除考站与原有摄像机的关联
                if (!$result = $StationVcr->delete()){
                    throw new \Exception('更改考站摄像头关联失败');
                }
            }
        }

        return true;
    }


    /**
     * 修改 考站对应考场关系
     * @param $roomId
     * @param $id
     *
     * @version   3.4
     * @author Zhoufuxiang 2016-04-13
     * @return bool
     * @throws \Exception
     */
    private function handleRoomStation($roomId, $station_id)
    {
        //获取原来的 考站与考场的关联
        $RoomStation = RoomStation::where('station_id','=',$station_id)->first();
        if (is_null($RoomStation) && !empty($roomId))
        {
            //将房间相关插入关联表
            $StationRoomData = [
                'room_id'    => $roomId,
                'station_id' => $station_id
            ];
            $result = RoomStation::create($StationRoomData);
            if (!$result) {
                throw new \Exception('关联房间时出错，请重试！');
            }

        } elseif (!is_null($RoomStation)){

            if(empty($roomId)){
                //删除考站 原来对应的考场关系
                if(!$result = $RoomStation->delete()){
                    throw new \Exception('删除房间关联失败');
                }

            }else{

                $RoomStation->room_id = $roomId;
                if (!$result = $RoomStation->save()) {
                    throw new \Exception('更改房间关联失败');
                }
            }
        }

        return true;
    }

    /**
     * 删除考站的方法
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteData($id)
    {
        try {
            //判断在关联表中是否有数据
            $examFlowStation = ExamFlowStation::where('station_id',$id)->first();
            if(!empty($examFlowStation)){
                throw new \Exception('不能删除此考站，因为与其他条目相关联');
            }
            $stationCase = StationCase::where('station_id', $id)->select('id')->first();
            if(!empty($stationCase)){
                StationCase::where('station_id', $id)->delete();
            }
            $stationVcr = StationVcr::where('station_id', $id)->select('id','vcr_id')->first();
            if(!empty($stationVcr)){
                //更改摄像头的状态
                $vcr = Vcr::findOrFail($stationVcr->vcr_id);
                $vcr->used = 0;
                if (!$vcr->save()) {
                    throw new \Exception('修改摄像头状态失败！');
                }

                StationVcr::where('station_id', $id)->delete();
            }

            $stationTeacher = StationTeacher::where('station_id', $id)->first();
            if(!empty($stationTeacher)){
                StationTeacher::where('station_id', $id)->delete();
            }
            $roomStation = RoomStation::where('station_id', $id)->select('id')->first();
            if(!empty($roomStation)){
                RoomStation::where('station_id', $id)->delete();
            }
            return $this->where($this->table.'.id', $id)->delete();

        } catch (\Exception $ex) {   //23000是指的有外键约束，说明与其他条目相关联
            if($ex->getCode() == 23000){
                throw new \Exception('不能删除此考站，因为与其他条目相关联！');
            }else{
                throw $ex;
            }
        }
    }

    /**
     *
     * @param $examId
     * @author Jiangzhiheng
     */
    public function stationEcho($examId)
    {
        return $this->leftJoin('exam_flow_station','exam_flow_station.station_id','=',$this->table.'.id')
            ->select([
                'exam_flow_station.serialnumber as serialnumber',
                'exam_flow_station.station_id as station_id',
                $this->table . '.name as station_name',
                $this->table . '.type as station_type'
            ])
            ->where('exam_flow_station.exam_id','=',$examId)
            ->get();
    }

    public function stationTeacherList($exam_id)
    {
        return $this
            -> leftJoin('exam_station','exam_station.station_id','=',$this->table.'.id')
            -> leftJoin('station_teacher', 'station_teacher.station_id','=','exam_station.station_id')
            -> leftJoin('teacher','teacher.id','=','station_teacher.user_id')
            -> where('exam_station.exam_id' , $exam_id)
            -> where('station_teacher.exam_id' , $exam_id)
            -> select([
                $this->table . '.id as station_id',
                $this->table . '.name as station_name',
                $this->table . '.type as station_type',
                $this->table . '.code as station_code',
                'teacher.id as teacher_id',
                'teacher.name as teacher_name',
                'teacher.type as teacher_type',
                'teacher.status as teacher_status',
            ])
            -> get();
    }



}