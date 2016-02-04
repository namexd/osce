<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
class Exam extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['code', 'name', 'begin_dt', 'end_dt', 'status', 'total', 'create_user_id', 'description', 'sequence_cate', 'sequence_mode', 'rules', 'address'];


    protected $statuValues  =   [
        0   =>  '未开考',
        1   =>  '正在考试',
        2   =>  '考试结束',
    ];
    /**
     * 考试与考站的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function examStation()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Station','exam_flow_station','exam_id','station_id');
    }

    /**
     * 考试场次关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examScreening()
    {
        return $this    ->  hasMany('\Modules\Osce\Entities\ExamScreening','exam_id','id');
    }

    /**
     *  考生关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function students(){
        return $this    ->  hasMany('\Modules\Osce\Entities\Student','exam_id','id');
    }

    /**
     * 考试流程节点关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function flows(){
        return $this    ->  hasMany('\Modules\Osce\Entities\ExamFlow','exam_id','id');
    }
    /**
     * 展示考试列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showExamList($formData='')
    {
        try {
            //不寻找已经被软删除的数据
            $builder = $this;//->where('status' , '<>' , 0);

            if($formData){
               $builder=$builder->where('name','like',$formData['exam_name'].'%');
            }

            //寻找相似的字段
            $builder = $builder->select([
                'id',
                'name',
                'begin_dt',
                'end_dt',
                'description',
                'total'
            ])->orderBy('begin_dt', 'desc');

            return $builder->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除的方法
     * @param $id
     * @return bool
     */
    public function deleteData($id)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            //获得当前exam的实例
            $examObj = $this->findOrFail($id);


            //进入模型逻辑
            //删除与考场相关的流程
            $flowIds = ExamFlow::where('exam_id',$id)->select('flow_id')->get()->pluck('flow_id'); //获得流程的id
            $examScreening = ExamScreening::where('exam_id',$id);
            $examScreeningObj = $examScreening->select('id')->get();
            $examScreeningIds = $examScreeningObj->pluck('id');

            //如果该考试已经完成，那么就不能让他们删除
            if (!ExamResult::whereIn('exam_screening_id',$examScreeningIds)->get()->isEmpty()) {
                throw new \Exception('该考试已经考完，不能删除！');
            }

            if (!Invite::whereIn('exam_screening_id',$examScreeningIds)->get()->isEmpty()) {
                throw new \Exception('已经为sp老师发送邀请，不能删除！');
            }

            //删除考试考场学生表
            foreach ($examScreeningObj as $item) {
                if (!ExamScreeningStudent::where('exam_screening_id',$item->id)->get()->isEmpty()) {
                    if (!ExamScreeningStudent::where('exam_screening_id',$item->id)->delete()) {
                        throw new \Exception('删除考试考场学生关系表失败，请重试！');
                    }
                }
            }

            //删除考试考场关联表
            $examScreenings = $examScreening-> get();
            if (!$examScreenings->isEmpty()) {
                foreach ($examScreenings as $v) {
                    if (!$v->delete()) {
                        throw new \Exception('删除考试考场关系表失败，请重试！');
                    }
                }

            }

            //删除考试考场关联
            if (!ExamRoom::where('exam_id',$id)->get()->isEmpty()) {
                if (!ExamRoom::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }

            //删除考试考站关联
            if (!ExamStation::where('exam_id',$id)->get()->isEmpty()) {
                if (!ExamStation::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考站关联失败，请重试！');
                }
            }


            //删除考试流程关联
            if (!ExamFlow::where('exam_id',$id)->get()->isEmpty()) {
                if (!ExamFlow::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试流程关联失败，请重试！');
                }
            }



            //通过考试流程-考站关系表得到考站信息
            if ($examObj->sequence_mode == 1) {
                //删除考试考场流程关联
                if (!ExamFlowRoom::where('exam_id',$id)->get()->isEmpty()) {
                    if (!ExamFlowRoom::where('exam_id',$id)->delete()) {
                        throw new \Exception('删除考试考场流程关联失败，请重试！');
                    }
                }
            } elseif ($examObj->sequence_mode == 2) {
                $station = ExamFlowStation::whereIn('flow_id',$flowIds);
                $stationIds = $station->select('station_id')->get();
                if (!$stationIds->isEmpty()) {
                    //删除考试流程-考站关系表信息
                    if (!$station->delete()) {
                        throw new \Exception('删除考试考站流程关联失败，请重试！');
                    }

                    //通过考站id找到对应的考站-老师关系表
                    if (!StationTeacher::where('exam_id',$id)->get()->isEmpty()) {
                        if (!StationTeacher::where('exam_id',$id)->delete()) {
                            throw new \Exception('弃用考站老师关联失败，请重试！');
                        }
                    }

//                    foreach ($stationIds as $stationId) {
//                        if (!StationTeacher::where('station_id',$stationId->station_id)->get()->isEmpty()) {
//                            if (!StationTeacher::where('station_id',$stationId->station_id)->delete()) {
//                                throw new \Exception('删除考站老师关联失败，请重试！');
//                            }
//                        }
//                    }
                }
            }


            //删除考试对应的资讯通知
            $informInfo = InformInfo::where('exam_id', $id)->get();
            if(count($informInfo) !=0){
                foreach ($informInfo as $item) {
                    if(!$item->delete()){
                        throw new \Exception('删除考试对应的资讯通知失败，请重试！');
                    }
                }
            }
            //删除考试本体
            if (!$result = $this->where('id',$id)->delete()) {
                throw new \Exception('删除考试失败，请重试！');
            }

            //如果有flow的话，就删除
            if (count($flowIds) != 0) {
                foreach ($flowIds as $flowId) {
                    if (!Flows::where('id',$flowId)->delete()) {
                        throw new \Exception('删除流程失败，请重试！');
                    }
                }
            }
            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     *
     * @access public
     *
     * @param array $examData 考试基本信息
     * * string        name        参数中文名(必须的)
     * * string    create_user_id       参数中文名(必须的)
     * @param array $examScreeningData 场次信息
     * * string        exam_id          参数中文名(必须的)
     * * string        begin_dt         参数中文名(必须的)
     * * string        end_dt           参数中文名(必须的)
     * * string     create_user_id      参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addExam(array $examData,array $examScreeningData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //将exam表的数据插入exam表
            if(!$result = $this->create($examData))
            {
                throw new \Exception('创建考试基本信息失败');
            }

            //将考试对应的考次关联数据写入考试场次表中
            foreach($examScreeningData as $key => $value){
                $value['exam_id']    =   $result->id;
                if(!$examScreening = ExamScreening::create($value))
                {
                    throw new \Exception('创建考试场次信息失败');
                }
            }
            $connection->commit();
            return $result;

        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 保存编辑考试 数据
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     * * string        begin_dt       开始时间(必须的)
     * * string        end_dt         结束时间(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editExam($exam_id, array $examData, array $examScreeningData)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //更新考试信息
            $exam   =   $this->find($exam_id);
            if($exam->sequence_mode!=$examData['sequence_mode'])
            {
                //如果排考模式变化 删除 已有 教师关联 和 排考计划
                if(StationTeacher::where('exam_id','=',$exam_id)->delete()===false)
                {
                    throw new \Exception('重置作废数据失败');
                }
                if(ExamPlan::where('exam_id','=',$exam_id)->delete()===false)
                {
                    throw new \Exception('重置作废数据失败');
                }
                if(ExamFlowRoom::where('exam_id','=',$exam_id)->delete()===false)
                {
                    throw new \Exception('重置作废数据失败');
                }
                if(ExamFlowStation::where('exam_id','=',$exam_id)->delete()===false)
                {
                    throw new \Exception('重置作废数据失败');
                }
                if(ExamFlow::where('exam_id','=',$exam_id)->delete()===false)
                {
                    throw new \Exception('重置作废数据失败');
                }
            }
            foreach($examData as $field=>$item)
            {
                $exam->$field   =   $item;
            }
            if(!$exam->save())
            {
                throw new \Exception('修改考试信息失败!');
            }

            $examScreening_ids = [];
            //判断输入的时间是否有误
            foreach ($examScreeningData as $value) {
                //不存在id,为新添加的数据
                if (!isset($value['id'])) {
                    $value['exam_id'] = $exam_id;

                    if (!$result = ExamScreening::create($value)) {
                        throw new \Exception('添加考试场次信息失败');
                    }
                    array_push($examScreening_ids, $result->id);
                } else {
                    array_push($examScreening_ids, $value['id']);
                    $examScreening = new ExamScreening();

                    if (!$result = $examScreening->updateData($value['id'], $value)) {
                        throw new \Exception('更新考试场次信息失败');
                    }
                }
            }
            //查询是否有要删除的考试场次
            $result = ExamScreening::where('exam_id', '=', $exam_id)->whereNotIn('id', $examScreening_ids)->get();
            if (count($result) != 0) {
                foreach ($result as $value) {
                    if (!$res = ExamScreening::where('id', '=', $value['id'])->delete()) {
                        throw new \Exception('删除考试场次信息失败');
                    }
                }
            }

            $connection->commit();
            return true;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    //考生查询

    public function getList($formData=''){
         $builder=$this->Join('student','student.exam_id','=','exam.id');
        if($formData['exam_name']){
            $builder=$builder->where('exam.name','like','%'.$formData['exam_name'].'');
         }
        if($formData['student_name']){
            $builder=$builder->where('student.name','like','%'.$formData['student_name'].'');
        }

        $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.code as code',
            'student.idcard as idCard',
            'student.mobile as mobile',
            'student.user_id as user_id',
        ]);

        $builder->orderBy('exam.begin_dt','DESC');
        return $builder->paginate(config('msc.page_size'));
    }
//查询今日考试
    public function getTodayList($status=''){
          $time=time();
          $builder=$this->select(DB::raw(
              implode(',',[
                  $this->table.'.id as id',
                  $this->table.'.name as exam_name',
                  $this->table.'.begin_dt as begin_dt',
                  $this->table.'.end_dt as end_dt',
                  $this->table.'.description as description',
                  $this->table.'.status as status',
              ])
            )
          );
        $builder=$builder->whereRaw(
             'unix_timestamp(date(exam.begin_dt)) < ?',
             [
                 $time
             ]
         );
        $builder=$builder->whereRaw(
             'unix_timestamp('.$this->table.'.end_dt) > ?',
             [
                 $time
             ]
         );
        if($status){
          $builder=$builder->where('status','=',1)->take(1);
        }
        $data=$builder->get();

        return $data;
    }

    public function getExamRoomData($exam_id)
    {
        try {
            return $this->leftJoin( 'exam_room',
                function ($join) {
                    $join->on($this->table . '.id' , '=' , 'exam_room.exam_id');
                })->leftJoin ( 'exam_flow_room',
                function ($join) {
                    $join->on('exam_flow_room.room_id' , '=' , 'exam_room.room_id');
                })->leftJoin( 'room',
                function ($join) {
                    $join->on('room.id' , '=' , 'exam_room.room_id');
                })->where($this->table.'.id', '=', $exam_id)
                ->select([
                    'room.id',
                    'room.name',
                    'exam_flow_room.serialnumber as serialnumber',
                    'exam_flow_room.flow_id as flow_id'
                ])->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //获取候考教室列表
    public function getWriteRoom($exam_id){
       $time=time();
       try {
           $builder = $this->Join('exam_room', 'exam.id', '=', 'exam_room.exam_id');
           $builder = $builder->Join('room', 'room.id', '=', 'exam_room.room_id');
           $builder = $builder->where('exam.id', $exam_id);
           $builder = $builder->whereRaw(
               'unix_timestamp(' . $this->table . '.begin_dt) > ?',
               [
                   $time
               ]
           );
       }
       catch(\Exception $ex)
       {
            throw new $ex;
       }
    }

    /**
     * 查询当天所有考试
     */
    public function selectExamToday($time = '')
    {
        if ($time == '') {
            $time = time();     //默认为今天
        }
        $today = strtotime(date('Y-m-d', $time));    //当天凌晨

        $result = $this->whereRaw('unix_timestamp(date_format(begin_dt, "%Y-%m-%d")) = ?
                                or unix_timestamp(date_format(end_dt, "%Y-%m-%d")) = ?
                                or (unix_timestamp(date_format(begin_dt, "%Y-%m-%d")) < ?
                                    and unix_timestamp(date_format(end_dt, "%Y-%m-%d")) > ?)', [$today, $today, $today, $today])
            ->get();

        return $result;
    }

    //获取当前学生的所有考试
    public function  Examname($examIds){
        return $this->whereIn('id',$examIds)->get();
    }

    /**
     * @param string $examId
     * @param string $subjectId
     * @return mixed
     * @author Jiangzhiheng
     */
    public function CourseControllerIndex($examId = "", $subjectId = "")
    {
        //获取使用过的考站
        $stationIds = ExamResult::leftJoin('exam_screening','exam_screening.id','=','exam_result.exam_screening_id')
//            ->where('exam_screening.exam_id','=',$examId)
            ->groupBy('exam_result.station_id')
            ->get()
            ->pluck('station_id')
            ->toArray();
        $builder = StationTeacher::leftJoin('station','station.id','=','station_teacher.station_id')
            ->Join('exam_result',
                function($join){
                $join->on('exam_result.station_id','=','station_teacher.station_id');
            })
            ->Join('exam',
                function($join){
                    $join->on('exam.id','=','station_teacher.exam_id');
                })
            ->Join('subject','subject.id','=','station.subject_id');


        if ($examId != "") {
            $builder = $builder->where('exam.id','=',$examId);
        }

        if ($subjectId != "") {
            $builder = $builder->where('subject.id','=',$subjectId);
        }

        $builder = $builder->select(
            'exam.name as exam_name',
            'exam.id as exam_id',
            'exam.begin_dt as exam_begin_dt',
            'subject.id as subject_id',
            'subject.title as subject_name',
            'station.id as station_id'
        )
            ->where('exam.status','<>',0)
            ->whereNotNull('station_teacher.user_id')
            ->whereIn('station.id',$stationIds)
//            ->distinct()
            ->groupBy('subject.id')
            ->paginate(config('osce.page_size'));

        return $builder;
    }

    /**查询与监考老师相关的考试
     * @param string $userId
     * @return mixed
     * @author zhouqiang
     */
      public function getInvigilateTeacher($userId){
          return $this->leftJoin( 'station_teacher',
              function ($join) {
                  $join->on($this->table . '.id' , '=' , 'station_teacher.exam_id');
              })
              ->where('station_teacher.user_id','=',$userId)
              ->select([
                  'exam.name as exam_name',
                  'station_teacher.exam_id as exam_id',
                  'station_teacher.station_id as station_id',
              ])
              ->get();
      }
}