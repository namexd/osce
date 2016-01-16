<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 15:19
 */
namespace Modules\Osce\Entities;

class ExamQueue extends  CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_queue';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];
    public $search = [];

    public function student(){
        return $this->hasMany('\Modules\Osce\Entities\student','id','student_id');
    }

    public function getStudent($mode,$exam_id){
        $exam   =Exam::find($exam_id);
        if($mode==1){
            return   $this->getWriteRoom($exam);

        }elseif($mode==2){
           return $this->getWriteStation($exam);
        }

    }


    //当考试排序模式为1的时候
    protected function getWriteRoom($exam){
        $examFlowRoomList   =   ExamFlowRoom::where('exam_id','=',$exam->id)->  paginate(config('osce.page_size'));
        $data=[];
        foreach($examFlowRoomList as $examFlowRoom)
        {
            $students    =   $examFlowRoom->queueStudent()->where('exam_id','=',$exam->id)->take(config('osce.num'))->get();
          foreach($students as $examQueue){
              foreach($examQueue->student as $student){
                  $data[$examQueue->room_id][]=$student;
              }
          }
        }
        return $data;
    }

    //当考试排序模式为2的时候
    protected function getWriteStation($exam){
       $examFlowStationList  =ExamFlowStation::where('exam_id','=',$exam->id)  ->paginate(config('osce.page_size'));
        $data=[];
       foreach ($examFlowStationList as $examFlowStation){

           $students=$examFlowStation ->queueStation()->where('exam_id','=',$exam->id)->take(config('osce.num'))->get();
           foreach($students as $ExamQueue ){
               foreach($ExamQueue->student as $student){
                   $data[$ExamQueue->room_id][$ExamQueue->station_id]=$student;
               }
           }
       }
        return $data;
    }
}