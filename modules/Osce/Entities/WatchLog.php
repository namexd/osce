<?php
/**
 * Created by PhpStorm.
 * User: fandian
 * Date: 2016/1/11 0011
 * Time: 15:43
 */
namespace Modules\Osce\Entities;

use Modules\Osce\Entities\ExamQueue;

class WatchLog extends CommonModel{
    protected $connection	= 'osce_mis';
    protected $table 		= 'watch_log';
    public    $incrementing	= true;
    public    $timestamps   = true;
    protected $fillable 	= [ 'watch_id', 'action','context','create_user_id','student_id'];
    public    $search       = [];

    /**
     * 腕表和学生的关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author ZouYuChao
     */
    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    public function watch()
    {
        return $this->hasOne('\Modules\Osce\Entities\Watch', 'id', 'watch_id');
    }

    /**
     * 添加腕表使用记录
     * @param $data
     * @param $student_id
     * @param $exam_id
     * @param $exam_screen_id
     * @throws \Exception
     */
    public function historyRecord($data,$student_id,$exam_id,$exam_screen_id)
    {
        $nowTime = time();
        $examQue = new ExamQueue();
        //创建考试队列
        $examQue ->createExamQueue($exam_id, $student_id, $nowTime, $exam_screen_id);
        
        //将context 序列化
        if($data['context']){
            $data['context'] = serialize($data['context']);
        }
        //创建腕表使用记录
        WatchLog::create([
            'watch_id'   => $data['watch_id'],
            'action'     => $data['action'],
            'context'    => $data['context'],
            'student_id' => $data['student_id']
        ]);
    }

    /**
     * 创建腕表解绑记录
     * @param $data
     */
   public function unwrapRecord($data){
       if($data['context']){
           $data['context']=serialize($data['context']);
       }
       $result = WatchLog::create([
           'watch_id'   => $data['watch_id'],
           'action'     => $data['action'],
           'context'    => $data['context'],
           'student_id' => $data['student_id']
       ]);
       return $result;
   }

   public function getList($code='',$studentName='',$beginDt='',$endDt=''){

       $beginDt=strtotime($beginDt);
       $endDt=strtotime($endDt);

       $builder=$this->leftJoin ('watch',
           function ($join) {
               $join->on('watch.id' , '=' , 'watch_log.watch_id');
           })->leftJoin( 'student',
           function ($join) {
               $join->on('student.id' , '=' , 'watch_log.student_id');
           });

       if($code){
           $builder=$builder->where('watch.code','like','%'.$code.'%');
       }

       if($studentName){
           $builder=$builder->where('student.name','like','%'.$studentName.'%');
       }

       if($beginDt){
           $builder=$builder->whereRaw(
               'unix_timestamp(' . $this->table . '.created_at) > ?',
               [
                   $beginDt
               ]
           );
       }

       if($endDt){
           $builder=$builder->WhereRaw(
               'unix_timestamp(' . $this->table . '.updated_at) < ?',
               [
                   $endDt
               ]
           );
       }

       $builder=$builder  ->select([
               'student.name as name',
               'watch.code as code',
               'watch_log.context as context',
           ])->paginate(config('osce.page_size'));
       return $builder;
   }


    //显示所有已绑定但未解绑人员的
    public function getBoundWatchInfos(){
        $builder = $this->where('action','=','绑定')->where('watch.status','=',1)->leftjoin('watch',function($watch){
            $watch->on('watch.id','=','watch_log.watch_id');
        })->leftjoin('student',function($student){
            $student->on('student.id','=','watch_log.student_id');
        })->leftjoin('exam_queue',function($examQueue){
            $examQueue->on('exam_queue.exam_screening_id','=','student.id');
        })->groupby('watch_log.student_id')->select('watch.id','watch.nfc_code','student.name','exam_queue.status')->get();


        return $builder;
    }

    //查看考生及与其绑定的腕表的详细信息
    public function getExamineeBoundWatchDetails($equipmentId){
        $builder = $this->where('watch.code','=',$equipmentId)->where('watch_log.action','=','绑定')->leftjoin('watch',function($watch){
            $watch->on('watch.id','=','watch_log.watch_id');
        })->leftjoin('student',function($student){
            $student->on('watch_log.student_id','=','student.id');
        })->leftjoin('exam_queue',function($examQueue){
            $examQueue->on('exam_queue.student_id','=','student.id');
        })->select(
            'student.name',
            'student.idcard',
            'student.exam_sequence',
            'student.photo',
            'exam_queue.status',
            'watch.code as nfc_code',
            'watch.nfc_code as code',
            'watch.name as equipment_name',
            'watch.factory',
            'watch.sp',
            'watch_log.student_id'
        )->orderBy('watch_log.created_at','desc')->first();

        return $builder;
    }

    /**
     * 考场模式下，查询队列、腕表信息
     * @param $exam_id
     * @param $screening_id
     * @param $room_id
     * @return array
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-14 14:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getRoomStudentWatchs($exam_id, $screening_id, $room_id)
    {
        try{
            // 考场排 多个学生
            $studentIds = ExamQueue::where('exam_id', '=', $exam_id)
                ->where('exam_screening_id', '=', $screening_id)
                ->where('room_id', '=', $room_id)
                ->where('status', '<', 3)           // 确保可以多次点击（【0:绑定腕表,1:抽签,2:正在考试,3:结束考试,4:缺考】）
                ->select(['student_id'])->get();   // 获取学生ID数组

            \Log::alert('老师准备时拿到的学生信息', [$studentIds]);
            if ($studentIds->isEmpty())
            {
                \Log::alert('未查到相应考试队列信息', [$studentIds, 'screeningId' => $screening_id, 'room_id'=> $room_id]);

                throw new \Exception('未查到相应考试队列信息', -2);
            }
            $studentIds = $studentIds->pluck('student_id')->toArray();
            //查询学生绑定的腕表信息
            $watches = WatchLog::leftJoin('watch', 'watch_log.watch_id', '=', 'watch.id')
                                ->whereIn('watch_log.student_id', $studentIds)
                                ->where('watch.status', '=', 1)
                                ->select(['watch.code'])->get();

            if ($watches->isEmpty()) {
                throw new \Exception('未查到相应腕表信息', -3);
            }

            return $watches;

        }catch (\Exception $ex)
        {
            throw $ex;
        }
    }

    /**
     * 考站模式下，查询队列、腕表信息
     * @param $examId
     * @param $screening_id
     * @param $stationId
     * @return mixed
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-14 14:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStationStudentWatch($examId, $screening_id, $stationId)
    {
        try{
            // 考站排 一个学生
            $examQueues = ExamQueue::where('exam_id', '=', $examId)
                    ->where('exam_screening_id', '=', $screening_id)
                    ->where('station_id', '=', $stationId)
                    ->where('status', '<', 3)               // 确保可以多次点击【0:绑定腕表,1:抽签,2:正在考试,3:结束考试,4:缺考】
                    ->orderBy('begin_dt', 'asc')
                    ->select(['student_id'])->first();

            if(is_null($examQueues))
            {
                \Log::alert('未查到相应考试队列信息',[$examQueues, 'screening_id'=>$screening_id, 'station_id'=>$stationId]);
                throw new \Exception('未查到相应考试队列信息', -2);
            }

            $watch = WatchLog::leftJoin('watch', 'watch.id', '=', 'watch_log.watch_id')
                    ->where('watch_log.student_id', '=', $examQueues->student_id)
                    ->where('watch.status', '=', 1)
                    ->select(['watch.code'])->first();

            if(is_null($watch)) {
                throw new \Exception('未查到相应腕表信息', -3);
            }

            /*************腕表推送*************/

            return $watch;

        }catch (\Exception $ex){
            throw $ex;
        }
    }


}