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

class Station extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'mins', 'type', 'subject_id', 'description', 'code'];

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
            if(!empty($name)){
                $builder = $builder->where($this->table.'.name', 'like', '%'.$name.'%');
            }

            //开始查询
            $builder = $builder->select([
                $this->table.'.id',
                $this->table.'.name',
                $this->table.'.code',
                $this->table.'.type',
                $this->table.'.description',
                $this->table.'.subject_id',
                $this->table.'.mins',
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
        try {
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
            list($stationData, $vcrId, $caseId, $roomId) = $formData;
            //将station表的数据插入station表
            $result = $this->create($stationData);

            //获得插入后的id
            $station_id = $result->id;
            if (!$result) {
                throw new \Exception('将数据写入考站失败！');
            }
            //将考场摄像头的关联数据写入关联表中
            $stationVcrData = [
                'vcr_id'=>$vcrId,
                'station_id' => $station_id
            ];
            $result = StationVcr::create($stationVcrData);
            if ($result === false) {
                throw new \Exception('将数据写入考场摄像头失败！');
            }

            //更改摄像机表中摄像机的绑定状态
            $vcr = Vcr::findOrFail($vcrId);
            $vcr->used = 1;  //变更状态,但是不一定是0
            $result = $vcr->save();
            if ($result === false) {
                throw new \Exception('摄像机绑定失败！');
            }

            //添加考站病历表的状态
            $stationCaseData = [
                'case_id'=>$caseId,
                'station_id' => $station_id
            ];
            $result = StationCase::create($stationCaseData);
            if ($result === false) {
                throw new \Exception('添加病历表失败');
            }

            //将房间相关插入关联表
            $StationRoomData = [
                'room_id' => $roomId,
                'station_id' => $station_id
            ];
            $result = RoomStation::create($StationRoomData);
            if (!$result) {
                throw new \Exception('关联房间时出错，请重试！');
            }

            $connection->commit();
            return true;
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
            $this->table . '.mins as mins',
            $this->table . '.create_user_id as create_user_id',
            $this->table . '.subject_id as subject_id',
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
        try {
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            $examFlowStation = ExamFlowStation::where('station_id',$id)->first();
            if(!empty($examFlowStation)){
                throw new \Exception('此考站已关联到考试流程中，不能做修改、保存操作！请点取消键返回！');
            }
            list($stationData, $vcrId, $caseId,$roomId) = $formData;
            //将原来的摄像机的状态回位
            //通过传入的考站的id找到原来的摄像机
            $originalVcrObj = StationVcr::where('station_id', '=', $id)->select('vcr_id')->first();
            if (empty($originalVcrObj)) {
                throw new \Exception('没有找到原来设定的摄像机');
            }

            $result = Vcr::findOrFail($originalVcrObj->vcr_id);
            //修改其状态,将其状态重置
            $result->used = 0; //可能是1，也可能是其他值
            //保存
            $result = $result->save();
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改摄像机状态失败');
            }

            //修改station表
            $result = $this->where($this->table . '.id', '=', $id)->update($stationData);
            //获得修改后的id
//            $station_id = $result;
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改考站信息失败');
            }

            //更改与本站相关的考站_摄像头关联
            $obj = StationVcr::where('station_id', '=', $id)->first();
            //修改其状态
            $obj->vcr_id = $vcrId;
            if (!($obj->save())) {
                $connection->rollBack();
                throw new \Exception('更改考站摄像头关联失败');
            }

            //更改摄像机表中摄像机的状态
            $vcr = Vcr::findOrFail($vcrId);  //找到选择的摄像机
            $vcr ->used = 1;  //变更状态,但是不一定是0
            if (!$result = $vcr->save()) {
                $connection->rollBack();
                throw new \Exception('更改摄像机状态失败');
            }

            //改变考站病历表的状态
            $stationCaseData = [
                'case_id'=>$caseId,
            ];
            $result = StationCase::where('station_id','=',$id)->update($stationCaseData);
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改病例关联失败');
            }

            //改变考站房间的状态
            $stationRoomData = [
                'room_id' => $roomId,
            ];
            $result = RoomStation::where('station_id','=',$id)->update($stationRoomData);
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改房间关联失败');
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
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