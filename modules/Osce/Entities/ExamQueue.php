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

    protected $statuValues  =   [
        1   =>  '正在考试',
        2   =>  '未进行考试',
        3   =>  '考试完毕',
    ];

    public function student(){
        return $this->hasMany('\Modules\Osce\Entities\student','id','student_id');
    }

    public function getStudent($mode,$exam_id){
        $exam   =Exam::find($exam_id);
        if($mode==1){
            return   $this->getWaitRoom($exam);

        }elseif($mode==2){
           return $this->getWaitStation($exam);
        }

    }


    //获取候考教室
    protected function getWaitRoom($exam){
        $examFlowRoomList   =   ExamFlowRoom::where('exam_id','=',$exam->id)->  paginate(config('osce.page_size'));
        $data=[];
        foreach($examFlowRoomList as $examFlowRoom)
        {
            $roomName=$examFlowRoom->room->name;
            $students    =   $examFlowRoom->queueStudent()->where('exam_id','=',$exam->id)->take(config('osce.num'))->get();
          foreach($students as $examQueue){
              foreach($examQueue->student as $student){
//                  $student->roomName=$roomName;
                  $data[$roomName][]=$student;
              }
          }
        }

        return $data;
    }

    //获取候考考站
    protected function getWaitStation($exam){
       $examFlowStationList  =ExamFlowStation::where('exam_id','=',$exam->id)  ->paginate(config('osce.page_size'));
        $data=[];
       foreach ($examFlowStationList as $examFlowStation){
           $stationName=$examFlowStation->station->name;
           $students=$examFlowStation ->queueStation()->where('exam_id','=',$exam->id)->take(config('osce.num'))->get();
           foreach($students as $ExamQueue ){
               foreach($ExamQueue->student as $student){
//                   $student->stationName=$stationName;
                   $data[$stationName][]=$student;
               }
           }
       }
        return $data;
    }


    //获取考试详情
    public  function  StudentExamInfo($watchStudent){
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $data=ExamQueue::leftJoin('room',function($join){
            $join ->on('room.id','=','exam_queue.room_id');
        })->leftJoin('station',function($join){
            $join ->on('station.id','=','exam_queue.station_id');
        })->leftJoin('student',function($join){
            $join ->on('student.id','=','exam_queue.student_id');
        })->where($this->table.'.student_id','=',$watchStudent)
            ->whereRaw("UNIX_TIMESTAMP(exam_queue.begin_dt) > UNIX_TIMESTAMP('$todayStart')
         AND UNIX_TIMESTAMP(exam_queue.end_dt) < UNIX_TIMESTAMP('$todayEnd')")
            ->select([
                'room.name as room_name',
                'student.name as name',
                'exam_queue.begin_dt as begin_dt',
                'exam_queue.end_dt as end_dt',
            ])->get()->toArray();

        return $data;
    }

    public function getPagination(){
        return $this->paginate(config('msc.page_size'));
    }

    public function examineeByRoomId($room_id)
    {

    }
}