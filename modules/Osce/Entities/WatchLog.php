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
    protected   $fillable 	=	[ 'watch_id', 'action','context','create_user_id'];
    public      $search    =   [];

    /**
     * 腕表和学生的关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Jiangzhiheng
     */
    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\student', 'id', 'student_id');
    }


   public function historyRecord($data,$student_id,$exam_id,$exam_screen_id){
//       $time=time();
//       $examQue=new ExamQueue();
//       $examQue->createExamQueue($exam_id, $student_id,$time,$exam_screen_id);
         if($data['context']){
             $data['context']=serialize($data['context']);
         }
          WatchLog::insert([
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
       WatchLog::insert([
           'watch_id' => $data['watch_id'],
           'action' => $data['action'],
           'context' => $data['context'],
           'student_id' => $data['student_id']
       ]);
   }
}