<?php
/**
 * Created by PhpStorm.
 * Date: 2017/8/310013
 * Time: 13:36
 */
namespace Modules\Osce\Http\Controllers\Api\Pad;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\SubjectCases;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Illuminate\Support\Facades\Cache;
use DB;

//header("Access-Control-Allow-Origin: *");
class PadphoneController extends  CommonController{
    /**
     *老师登录进系统显示学生列表
     */
    public function getStulist(Request $request)
    {
        $this->validate($request, [
            'userid' => 'required|integer'
        ]);
        //取老师的id
        $userid = $request->get('userid');

        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status', 1)->first();
        if(empty($exam)){
            return response()->json(
                $this->success_data([], 0, '考试未开始或已结束！')
            );
        }
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id', $exam_id)->where('status', 1)->first();
        $exam_screening_id = $examscreening->id;
        //显示多少个学生
        $shownum = ExamPlan::where('exam_id', $exam_id)->where('exam_screening_id', $exam_screening_id)->max('serialnumber');
        $list = [];
        //根据老师和考试对应的信息查对应的考站
        $stages = StationTeacher::where('exam_id', $exam_id)->groupBy('exam_screening_id')->get();
        $jdarr =collect($stages)->pluck('exam_screening_id')->all();
        $stagecount = count($jdarr);

        if($stagecount==1){
            $stationteacher = StationTeacher::where('exam_id', $exam_id)->where('user_id', $userid)->first();
        }else{
            $stationteacher = StationTeacher::where('exam_id', $exam_id)->where('exam_screening_id', $exam_screening_id)->where('user_id', $userid)->first();
        }


        $station_id = $stationteacher->station_id;
        //通过考站查对应的房间号
        $room = RoomStation::where('station_id', $station_id)->first();
        $room_id = $room->room_id;
        //得出学生列表

        $queue = Cache::get('userid_'.$userid.'exam_id_'.$exam_id.'exam_screening_id_'.$exam_screening_id,0);
        $list = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
            ->select('exam_plan.status','exam_plan.id as planid', 'student.id as stuid', 'student.avator','student.idcard', 'student.code', 'student.exam_sequence','exam_plan.student_id as pstuid', 'student.name as stuname')
            ->where('exam_plan.exam_id', $exam_id)
            ->where('exam_plan.exam_screening_id', $exam_screening_id)
            ->where('exam_plan.room_id', $room_id)
            //->where('exam_plan.status', 0)
            ->orderBy('exam_plan.begin_dt', 'asc')
            ->take($shownum)
            ->skip($queue*$shownum)
            ->get();
        //dd($list,$statusArr =collect($list)->pluck('status')->all(),!in_array(0,$statusArr) && !in_array(1,$statusArr));
        if (!empty($list)) {
            $statusArr =collect($list)->pluck('status')->all();
            if(!in_array(0,$statusArr) && !in_array(1,$statusArr)){
                $queue=$queue+1;
                Cache::put('userid_'.$userid.'exam_id_'.$exam_id.'exam_screening_id_'.$exam_screening_id,$queue,36000);
                $list = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
                    ->select('exam_plan.status','exam_plan.id as planid', 'student.id as stuid', 'student.avator','student.idcard', 'student.code', 'student.exam_sequence','exam_plan.student_id as pstuid', 'student.name as stuname')
                    ->where('exam_plan.exam_id', $exam_id)
                    ->where('exam_plan.exam_screening_id', $exam_screening_id)
                    ->where('exam_plan.room_id', $room_id)
                    //->where('exam_plan.status', 0)
                    ->orderBy('exam_plan.begin_dt', 'asc')
                    ->take($shownum)
                    ->skip($queue*$shownum)
                    ->get();
            }
        }
        $list2 = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
            ->select('exam_plan.id as planid', 'exam_plan.begin_dt','student.id as stuid', 'student.avator', 'student.idcard', 'student.code',  'student.exam_sequence','exam_plan.student_id as pstuid', 'student.name as stuname')
            ->where('exam_plan.exam_id', $exam_id)
            ->where('exam_plan.exam_screening_id', $exam_screening_id)
            ->where('exam_plan.room_id', $room_id)
            ->where('exam_plan.status','<',2)
            ->orderBy('exam_plan.begin_dt', 'asc')
            ->first();

            $data["list"] = $list;
            $data["nowstu"] = $list2;

            return response()->json(
                $this->success_data($data, 1, 'success')
            );
    }



    /**
      设置当前考生为缺考
     */
    public function stopNowstu(Request $request){
        try{
            $this->validate($request,[
                'planid'   => 'required|integer',
                'userid'   => 'required|integer'

            ]);
            $planid = $request->get('planid');
            $userid = $request->get('userid');
            ExamPlan::where('id',$planid)->update(['status' => 3]);
            //取exam表中exam status状态为1的 得到id
            $exam = Exam::where('status',1)->first();
            $exam_id = $exam->id;
            //通过得到的exam_id查exam_screeing
            $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
            //要不要结束父考试标志
            $bz = 1;
            //提醒考官上午或者下午的考试结束啦
            $sbz = 1;

            $res = ExamPlan::where('exam_id',$exam_id)->where('exam_screening_id',$examscreening->id)->where('status','<',2)->first();
            if(empty($res)){
                ExamScreening::where('id',$examscreening->id)->update(['status' => 2]);
                //清除缓存
                //Cache::forget('userid_'.$userid.'exam_id_'.$exam_id.'exam_screening_id_'.$examscreening->id);
                \Cache::flush();
                //当前考次结束，开启下一场考试，不是本次考试不会开启成功
                $gid = $examscreening->id+1;
                ExamScreening::where('id',$gid)->where('exam_id',$exam_id)->update(['status' => 1]);
                $sbz = 0;
            }else{
                $bz = 0;
            }

            //所有考次都结束啦，结束父考试
            if($bz==1){
                Exam::where('id',$exam_id)->update(['status' => 2]);
            }
            $list = [];
            $list['msg'] = '设置缺考成功';
            $list['sbz'] = $sbz;
            return response()->json(
                $this->success_data($list,1,'success')
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
                'planid'   => 'required|integer'
            ]);
            $planid = $request->get('planid');
            ExamPlan::where('id',$planid)->update(['status' => 1]);
            $stime = date('Y-m-d H:i:s');
            $list = ['begin_dt'=>$stime];
            return response()->json(
                $this->success_data($list,1,'success')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }

    /**
    提交评分结束当前考试
     */
    public function finishNowstu(Request $request){
        try{
            $this->validate($request,[
                'planid'   => 'required|integer',
                'userid'   => 'required|integer'
            ]);
            $planid = $request->get('planid');
            $userid = $request->get('userid');
            ExamPlan::where('id',$planid)->update(['status' => 2]);
            //取exam表中exam status状态为1的 得到id
            $exam = Exam::where('status',1)->first();
            $exam_id = $exam->id;
            //通过得到的exam_id查exam_screeing
            $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
            //要不要结束父考试标志
            $bz = 1;
            //提醒考官上午或者下午的考试结束啦
            $sbz = 1;

            $res = ExamPlan::where('exam_id',$exam_id)->where('exam_screening_id',$examscreening->id)->where('status','<',2)->first();
            if(empty($res)){
                ExamScreening::where('id',$examscreening->id)->update(['status' => 2]);
                //清除缓存
                Cache::forget('userid_'.$userid.'exam_id_'.$exam_id.'exam_screening_id_'.$examscreening->id);
                //当前考次结束，开启下一场考试，不是本次考试不会开启成功
                $gid = $examscreening->id+1;
                ExamScreening::where('id',$gid)->where('exam_id',$exam_id)->update(['status' => 1]);
                $sbz = 0;
                $kanexam = ExamScreening::where('exam_id',$exam_id)->where('status','<',2)->first();
                if(!empty($kanexam)){
                    $bz = 0;
                }
            }else{
                $bz = 0;
            }

            //所有考次都结束啦，结束父考试
            if($bz==1){
                Exam::where('id',$exam_id)->update(['status' => 2]);
            }

            $list['msg'] = "评分完成";
            //$sbz为0提示考官本次考试结束
            $list['sbz'] = $sbz;
            return response()->json(
                $this->success_data($list,1,'success')
            );

        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }


    /**
     * 老师登陆进来要考的考试项目，还有对应的病例内容
     */
    public function getTeacherSubject(Request $request){
        $this->validate($request, [
            'userid' => 'required|integer',
        ]);
        //取老师的id
        $userid = $request->get('userid');

        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status',1)->first();
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
        $exam_screening_id = $examscreening->id;
        //根据老师和考试对应的信息查对应的考站
        //$stationteacher = StationTeacher::where('exam_id',$exam_id)->where('exam_screening_id',$exam_screening_id)->where('user_id',$userid)->first();
        $stationteacher = StationTeacher::where('exam_id',$exam_id)->where('user_id',$userid)->first();
        $station_id = $stationteacher->station_id;
        //通过考站查对应的房间号
        $room = RoomStation::where('station_id',$station_id)->first();
        $room_id = $room->room_id;

        //根据老师和考试对应的信息查对应的考站
        $stages = StationTeacher::where('exam_id', $exam_id)->groupBy('exam_screening_id')->get();
        $jdarr =collect($stages)->pluck('exam_screening_id')->all();
        $stagecount = count($jdarr);

        if($stagecount==1){
            $data = StationTeacher::leftjoin('station', 'station_teacher.station_id', '=', 'station.id')
                ->leftjoin('subject', 'station.subject_id', '=', 'subject.id')
                ->leftjoin('teacher', 'station_teacher.user_id', '=', 'teacher.id')
                ->select('station_teacher.station_id','station.name','subject.title as subject_title','subject.mins','subject.id as subject_id','teacher.name as teacher_name')
                ->where('station_teacher.exam_id',$exam_id)
                ->where('station_teacher.user_id',$userid)
                ->first();
        }else{
            $data = StationTeacher::leftjoin('station', 'station_teacher.station_id', '=', 'station.id')
                ->leftjoin('subject', 'station.subject_id', '=', 'subject.id')
                ->leftjoin('teacher', 'station_teacher.user_id', '=', 'teacher.id')
                ->select('station_teacher.station_id','station.name','subject.title as subject_title','subject.mins','subject.id as subject_id','teacher.name as teacher_name')
                ->where('station_teacher.exam_id',$exam_id)
                ->where('station_teacher.exam_screening_id',$exam_screening_id)
                ->where('station_teacher.user_id',$userid)
                ->first();
        }

         //查一下对应的考试课目
        $sid = $data->subject_id;
        $subjectcase = SubjectCases::where('subject_id',$sid)->first();
        $case_id = $subjectcase->case_id;
        //根据考试颗目获得考试内容
        $case = CaseModel::where('id',$case_id)->first();
        $list['name'] = $data->name;
        $list['mins'] = $data->mins;
        $list['subject_title'] = $data->subject_title;
        $list['casename'] = $case->name;
        $list['casedsc'] = $case->description;
        $list['subject_id'] = $sid;
        $list['room_id'] = $room_id;
        $list['station_id'] = $station_id;
        $list['exam_id'] = $exam_id;


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
            'pstuid'   => 'required|integer',
        ]);
        $pstuid = $request->get('pstuid');
        //取exam表中exam status状态为1的 得到id
        $exam = Exam::where('status',1)->first();
        $exam_id = $exam->id;
        //通过得到的exam_id查exam_screeing
        $examscreening = ExamScreening::where('exam_id',$exam_id)->where('status',1)->first();
        $exam_screening_id = $examscreening->id;

        $list = ExamPlan::leftjoin('student', 'exam_plan.student_id', '=', 'student.id')
            ->leftjoin('room','exam_plan.room_id','=','room.id')
            ->select('exam_plan.id as planid','room.name as room_name','student.user_id as stuid','student.name as stuname')
            ->where('exam_plan.exam_id',$exam_id)
            ->where('exam_plan.exam_screening_id',$exam_screening_id)
            ->where('exam_plan.student_id',$pstuid)
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