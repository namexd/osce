<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 15:19
 */
namespace Modules\Osce\Entities;

use DB;
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
        1   =>  '姝ｅ湪鑰冭瘯',
        2   =>  '鏈繘琛岃?冭瘯',
        3   =>  '鑰冭瘯瀹屾瘯',
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


    //鑾峰彇鍊欒?冩暀瀹?
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

    //鑾峰彇鍊欒?冭?冪珯
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


    //鑾峰彇鑰冭瘯璇︽儏
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
                'exam_queue.room_id as room_id',
                'exam_queue.station_id as station_id',
                'exam_queue.status as status',
                'exam_queue.id as id',

            ])->get();

        return $data;
    }

    public function getPagination(){
        return $this->paginate(config('msc.page_size'));
    }

    /**
     * 鏍规嵁room_id鏉ヨ幏鍙栧搴旂殑鑰冪敓鍒楄〃
     * @param $room_id
     * @return
     * @throws \Exception
     * @author Jiangzhiheng
     */
    static public function examineeByRoomId($room_id)
    {
        try {
            return ExamQueue::leftJoin('student', 'student.exam_id', '=', 'exam_queue.exam_id')
                ->where('room_id', $room_id)
                ->where('status', 2)
                ->select([
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.user_id as student_user_id',
                    'student.idcard as student_idcard',
                    'student.mobile as student_mobile',
                    'student.code as student_code',
                    'student.avator as student_avator',
                    'student.description as student_description',
                ])
                ->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    //学生考试成绩推送

}