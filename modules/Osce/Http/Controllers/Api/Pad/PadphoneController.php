<?php
/**
 * Created by PhpStorm.
 * User: fandian
 * Date: 2017/8/310013
 * Time: 13:36
 */
namespace Modules\Osce\Http\Controllers\Api\Pad;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\SubjectCases;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Repositories\WatchReminderRepositories;

class PadphoneController extends  CommonController{
    /**
     *老师登录进系统显示学生列表
     */
    public function getStulist(){
        //取老师的id
        $userid=1;

        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status',1)->first();
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
        $examscreening_id = $examscreening->id;
        //显示多少个学生
        $shownum = ExamPlan::where('exam_id',$exam_id)->where('examscreening_id',$examscreening_id)->max('serialnumber');
        $list=[];
        //根据老师和考试对应的信息查对应的考站
        $stationteacher = StationTeacher::where('exam_id',$exam_id)->where('examscreening_id',$examscreening_id)->where('user_id',$userid)->first();
        $station_id = $stationteacher->station_id;
        //通过考站查对应的房间号
        $room = Room::where('station_id',$station_id)->first();
        $room_id = $room->room_id;
        //得出学生列表
        $connection = DB::connection($this->connection);

        $list = $connection->table('exam_plan')
            ->leftjoin('student', 'exam_plan.user_id', '=', 'student.user_id')
            ->select('exam_plan.id as planid','student.user_id as stuid','student.name as stuname')
               ->where('exam_plan.exam_id',$exam_id)
               ->where('exam_plan.examscreening_id',$examscreening_id)
               ->where('exam_plan.room_id',$room_id)
               ->where('exam_plan.status',0)
               ->orderBy('exam_plan.begin_dt','asc')
               ->take($shownum)
               ->get();

        if(empty($list)){
            $list =[];
        }
            return response()->json(
                $this->success_data($list,1,'success')
            );
       }

    /**
        获得前当前考生
     */
       public function getNowstu(Request $request){
           //取老师的id
           $userid=1;

           //取exam表中exam status状态为1的 得到id
           $exam = Exam::where('status',1)->first();
           $exam_id = $exam->id;
           //通过得到的exam_id查exam_screeing
           $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
           $examscreening_id = $examscreening->id;
           //根据老师和考试对应的信息查对应的考站
           $stationteacher = StationTeacher::where('exam_id',$exam_id)->where('examscreening_id',$examscreening_id)->where('user_id',$userid)->first();
           $station_id = $stationteacher->station_id;
           //通过考站查对应的房间号
           $room = Room::where('station_id',$station_id)->first();
           $room_id = $room->room_id;
           $connection = DB::connection($this->connection);
           $list = $connection->table('exam_plan')
               ->leftjoin('student', 'exam_plan.user_id', '=', 'student.user_id')
               ->select('exam_plan.id as planid','student.user_id as stuid','student.name as stuname')
               ->where('exam_plan.exam_id',$exam_id)
               ->where('exam_plan.examscreening_id',$examscreening_id)
               ->where('exam_plan.room_id',$room_id)
               ->where('exam_plan.status',0)
               ->orderBy('exam_plan.begin_dt','asc')
               ->take(1)
               ->get();
           if(empty($list)){
               $list =[];
           }
           return response()->json(
               $this->success_data($list,1,'success')
           );

       }

    /**
      设置当前考生为缺考
     */
    public function stopNowstu(Request $request){
        try{
            $this->validate($request,[
                'planid'   => 'required|integer',
            ]);
            $planid = $request->get('planid');
            ExamPlan::where('id',$planid)->update(['status' => 3]);
            return response()->json(
                $this->success_data('设置缺考成功',1,'success')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }

    /**
    设置当前考生为开始考试
     */
    public function startNowstu(Request $request){
        try{
            $this->validate($request,[
                'planid'   => 'required|integer',
            ]);
            $planid = $request->get('planid');
            ExamPlan::where('id',$planid)->update(['status' => 1]);
            return response()->json(
                $this->success_data('设置开始考试成功',1,'success')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }

    /**
    提交评份结束当前考试
     */
    public function finishNowstu(Request $request){
        try{
            $this->validate($request,[
                'planid'   => 'required|integer',
            ]);
            $planid = $request->get('planid');
            ExamPlan::where('id',$planid)->update(['status' => 2]);
            return response()->json(
                $this->success_data('成功结束',1,'success')
            );

            //取exam表中exam status状态为1的 得到id
            $exam = Exam::where('status',1)->first();
            $exam_id = $exam->id;
            //通过得到的exam_id查exam_screeing
            $examscreening = ExamScreening::where('exam_id',$exam_id)->get();
            $bz = 1;
            foreach($examscreening as $edata){
                $res = ExamPlan::where('exam_id',$exam_id)->where('examscreening_id',$edata->id)->where('status','<',2)->first();
                if(empty($res)){
                    ExamScreening::where('id',$edata->id)->update(['status' => 2]);
                    $gid = $edata->id+1;
                    ExamScreening::where('id',$gid)->update(['status' => 1]);

                }else{
                    $bz = 0;
                }
            }
            if($bz==1){
                Exam::where('id',$exam_id)->update(['status' => 2]);
            }

        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }


    /**
     * 老师登陆进来要考的考试项目，还有对应的病例内容
     */
    public function getTeacherSubject(Request $request){
        //取老师的id
        $userid=1;

        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status',1)->first();
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
        $examscreening_id = $examscreening->id;
        //根据老师和考试对应的信息查对应的考站
        $stationteacher = StationTeacher::where('exam_id',$exam_id)->where('examscreening_id',$examscreening_id)->where('user_id',$userid)->first();
        $station_id = $stationteacher->station_id;
        //通过考站查对应的房间号
        $room = Room::where('station_id',$station_id)->first();
        $room_id = $room->room_id;

        $connection = DB::connection($this->connection);
        $data = $connection->table('station_teacher')
            ->leftjoin('station', 'station_teacher.station_id', '=', 'station.id')
            ->leftjoin('subject', 'station.subject_id', '=', 'subject.id')
            ->leftjoin('teacher', 'station_teacher.user_id', '=', 'teacher.id')
            ->select('station_teacher.station_id','station.name','subject.title as subject_title', 'subject.id as subject_id','teacher.name as teacher_name')
            ->where('station_teacher.exam_id',$exam_id)
            ->where('station_teacher.examscreening_id',$examscreening_id)
            ->where('station_teacher.user_id',$userid)
            ->first();
         //查一下对应的考试课目
        $sid = $data->subject_id;
        $subjectcase = SubjectCases::where('subject_id',$sid)->first();
        $case_id = $subjectcase->case_id;
        //根据考试颗目获得考试内容
        $case = CaseModel::where('case_id',$case_id)->frist();
        $list['casename'] = $case->name;
        $list['casedsc'] = $case->description;
        $list['subject_id'] = $sid;
        $list['room_id'] = $room_id;
        $list['station_id'] = $station_id;


        return response()->json(
            $this->success_data($list,1,'success')
        );
    }

    /**
     *学生考完显示下组去那里
     */
    public function getStugo(Request $request)
    {

        $this->validate($request,[
            'stuid'   => 'required|integer',
        ]);
        $stuid = $request->get('stuid');
        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status',1)->first();
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
        $examscreening_id = $examscreening->id;

        $connection = DB::connection($this->connection);
        $list = $connection->table('exam_plan')
            ->leftjoin('student', 'exam_plan.user_id', '=', 'student.user_id')
            ->leftjoin('room','exam_plan.room_id','=','room.id')
            ->select('exam_plan.id as planid','room.name as room_name','student.user_id as stuid','student.name as stuname')
            ->where('exam_plan.exam_id',$exam_id)
            ->where('exam_plan.examscreening_id',$examscreening_id)
            ->where('exam_plan.user_id',$stuid)
            ->where('exam_plan.status',0)
            ->orderBy('exam_plan.begin_dt','asc')
            ->take(1)
            ->get();
        if(empty($list)){
            $list =[];
        }
        return response()->json(
            $this->success_data($list,1,'success')
        );
    }



}