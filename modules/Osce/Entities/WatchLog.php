<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 15:43
 */
namespace Modules\Osce\Entities;
use Modules\Osce\Entities\ExamQueue;
class WatchLog extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'watch_log';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'watch_id', 'action','context','create_user_id','student_id'];
    public      $search    =   [];

    /**
     * 腕表和学生的关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Jiangzhiheng
     */
    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    public function watch()
    {
        return $this->hasOne('\Modules\Osce\Entities\Watch', 'id', 'watch_id');
    }

   public function historyRecord($data,$student_id,$exam_id,$exam_screen_id){
       $time=time();
       $examQue=new ExamQueue();
       $examQue->createExamQueue($exam_id, $student_id,$time,$exam_screen_id);
         if($data['context']){
             $data['context']=serialize($data['context']);
         }
          WatchLog::create([
              'watch_id' => $data['watch_id'],
              'action' => $data['action'],
              'context' => $data['context'],
              'student_id' => $data['student_id']
          ]);
   }

   public function unwrapRecord($data){
       if($data['context']){
           $data['context']=serialize($data['context']);
       }
       WatchLog::create([
           'watch_id' => $data['watch_id'],
           'action' => $data['action'],
           'context' => $data['context'],
           'student_id' => $data['student_id']
       ]);
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
            $examQueue->on('exam_queue.exam_screening_id','=','exam_screening.id');
        })->groupby('watch_log.student_id')->select('watch.id','watch.nfc_code','student.name','exam_queue.status')->get();


        return $builder;
    }

    //查看考生及与其绑定的腕表的详细信息
    public function getExamineeBoundWatchDetails($equipmentId){
        $builder = $this->where('watch.nfc_code','=',$equipmentId)->where('watch_log.action','=','绑定')->leftjoin('watch',function($watch){
            $watch->on('watch.id','=','watch_log.watch_id');
        })->leftjoin('student',function($student){
            $student->on('watch_log.student_id','=','student.id');
        })->leftjoin('exam_queue',function($examQueue){
            $examQueue->on('exam_queue.student_id','=','student.id');
        })->select(
            'student.name',
            'student.idcard',
            'student.exam_sequence',
            'exam_queue.status',
            'watch.code',
            'watch.name as equipment_name',
            'watch.factory',
            'watch.sp',
            'watch_log.student_id'
        )->first();

        return $builder;
    }
}