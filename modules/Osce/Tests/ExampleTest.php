<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SendReminderSms;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;


use Modules\Osce\Entities\ExamAbsent;

use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;

use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;

class ExampleTest extends TestCase
{

    public function getBoundWatch(array $datas){
        $code=$datas['code'];
        $id_card=$datas['id_card'];
        $exam_id=$datas['exam_id'];

        $id=Watch::where('code',$code)->select('id')->first()->id;//获取腕表id
        $student_id=Student::where('idcard',$id_card)->where('exam_id',$exam_id)->select()->first();//查询学生是否参加当前考试
        if(!$student_id){
            return \Response::json(array('code' => 3));//没有参加当前考试
        }
        $student_id=$student_id->id;//获取学生id
        $planId=ExamPlan::where('student_id',$student_id)->where('exam_id',$exam_id)->select('id')->first();//查询是否安排考试
        if(!$planId ){
            return \Response::json(array('code' =>4));//未安排当前考试
        }
        $students=$this->getStudentList($datas);

        $idcards=[];
        $students=json_decode($students->content());
        foreach($students->data as $item){
            $idcards[]=$item->idcard;
        }
        if(!in_array($id_card,$idcards)){
            return \Response::json(array('code'=>5));//未在考试队列 请等待
        }
        $examStatus=Exam::where('status','=',1)->select()->first();


        //修改场次状态
        $examScreeningModel =   new ExamScreening();
        $examScreening  =   $examScreeningModel ->  getExamingScreening($exam_id);
        if(is_null($examScreening))
        {
            $examScreening      =   $examScreeningModel  ->  getNearestScreening($exam_id);
            $examScreening      ->  status  =   1;
            if(!$examScreening      ->  save())
            {
                throw new \Exception('场次开考失败！');
            }
        }

        if($examStatus){
            if($examStatus->id!=$exam_id){
                return \Response::json(array('code'=>6));
            }
        }
        $screen_id=ExamOrder::where('exam_id',$exam_id)->where('student_id',$student_id)->select('exam_screening_id')->first();//获取考试场次Id
        $exam_screen_id=$screen_id->exam_screening_id;
        $result = Watch::where('id', $id)->update(['status' => 1]);//修改腕表状态
        if ($result) {
            $action = '绑定';
            $updated_at =date('Y-m-d H:i:s',time());
            $data = array(
                'watch_id' => $id,
                'action' => $action,
                'context' => array('time' => $updated_at, 'status' => 1),
                'student_id' => $student_id
            );
            $watchModel = new WatchLog();
            $watchModel->historyRecord($data,$student_id,$exam_id,$exam_screen_id);//插入使用记录
            $ExamScreeingStudentId=ExamScreeningStudent::where('watch_id' ,'=',$id)->where('student_id','=',$student_id)->where('exam_screening_id','=',$exam_screen_id)->first();
            if($ExamScreeingStudentId){
                ExamScreeningStudent::where('watch_id' ,'=',$id)->where('student_id','=',$student_id)->update(['is_end'=>0]);
            }else{
                ExamScreeningStudent::create(['watch_id' => $id,'student_id'=>$student_id,'signin_dt'=>$updated_at,'exam_screening_id'=>$exam_screen_id,'is_signin'=>1]);//签到

            }
            ExamOrder::where('exam_id',$exam_id)->where('student_id',$student_id)->update(['status'=>1]);//更改考生状态
            Exam::where('id',$exam_id)->update(['status'=>1]);//更改考试状态
            return \Response::json(array('code' => 1));
        } else {
            return \Response::json(array('code' => 0));
        }

    }


    public function getStudentList(array $datas)
    {
        $exam_id = $datas['exam_id'];
        //$screen_id = ExamScreening::where('exam_id', $exam_id)->where('status', 1)->orderBy('begin_dt')->first();
        $examScreeningModel =   new ExamScreening();
        $examScreening      =   $examScreeningModel ->  getExamingScreening($exam_id);
        if(is_null($examScreening))
        {
            $examScreening  =   $examScreeningModel->getNearestScreening($exam_id);
        }

        if (!$examScreening) {
            return \Response::json(array('code' => 2));
        }
        $screen_id = $examScreening->id;
        $studentModel = new Student();
        try {
            $mode=Exam::where('id',$exam_id)->select('sequence_mode')->first()->sequence_mode;
            //$mode 为1 ，表示以考场分组， 为2，表示以考站分组 //TODO zhoufuxiang
            if($mode==1){
                $rooms=ExamFlowRoom::where('exam_id',$exam_id)->select('room_id')->get();
                $stations=RoomStation::whereIn('room_id',$rooms)->select('station_id')->get();

            } else{
                $stations = ExamFlowStation::where('exam_id', $exam_id)->select('station_id')->get();
            }
            $countStation=[];
            foreach($stations as $item){
                $countStation[]=$item->station_id;
            }
            $countStation=array_unique($countStation);
            $batch=config('osce.batch_num');//默认为2
            $countStation=count($countStation)*$batch;//可以绑定的学生数量 考站数乘以倍数
            $list = $studentModel->getStudentQueue($exam_id, $screen_id,$countStation);//获取考生队列
            $data=[];
            foreach($list as $itm){
                $data[]=[
                    'name' => $itm->name,
                    'idcard' => $itm->idcard,
                    'code' => $itm->code,
                    'exam_screening_id' => $itm->exam_screening_id,
                ];
            }
            $count = count($list);
//            return \Response::json(array('code' => -1));
            return response()->json(
                $this->success_data($data, 1, 'count:'.$count)
            );
        } catch (\Exception $ex) {
            return \Response::json(array('code' => -1));
        }
    }

    public function testSendSms(){

        $datas=[
          'code' =>'15321450065',
          'id_card' =>'511002199008250392',
          'exam_id' =>'231',
        ];
        $stet=$this-> getBoundWatch($datas);
        dd($stet);
//
//        $response =   $this->action('get','\Modules\Osce\Http\Controllers\Api\InvigilatePadController@getStartExam','',$data);
//        $view=$response->getContent();
//        dd($view);




//        echo 'test......';
//
//        //Common::sendSms('13980757127','注册验证码:'.time());
    }

}
