<?php

namespace Modules\Osce\Http\Controllers\Doorplate;


use Illuminate\Http\Request;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\PadLogin\PadLogin;
use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Http\Controllers\Api\LoginPullDownController;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\PadLogin\Time;

class IndexController extends CommonController
{

    /**
     * 门牌启动入口
     * @method GET
     * @url /osce/doorplate/doorplate-start
     * @access public
     *
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function doorStart(){


        $padLog=new PadLoginRepository(new PadLogin());
        $examList=$padLog->examList(new Time());

        $roomList=[];
        if(!is_null($examList)){
            $roomList=$padLog->roomList(key($examList));
        }

        return view('osce::doorplate.login ', [
            'examList'      =>$examList,
            'roomList'     =>$roomList
        ]);
    }

    /**
     * 当前考试信息
     * @method GET
     * @url /osce/doorplate/today-exam
     * @access public
     * @param examId 考试id
     * @param roomId 考试对应的考站id
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMsg(Request $request)
    {
        $this->validate($request,[
            'examId' =>'required',
            'roomId' =>'required',
        ]);
        $screen=new ExamScreening();
        $exam_id= $request->get('examId');
        $room_id=$request->get('roomId');
        $exam=Exam::where("id",$exam_id)->first();
      try{
         if($exam->status==2){
             throw new \Exception('该考试已结束');
         }
        $screenObject=$screen->getExamingScreening($exam_id);
        if(!is_null($screenObject)){//获取当前场次
            $screenId=$screenObject->id;
        }else{
            $screenObject=$screen->getNearestScreening($exam_id);
            if(is_null($screenObject)){
                throw new \Exception('今天没有正在进行的考试场次');
            }
            $screenId=$screenObject->id;
        }

        $ExamDraft=new ExamDraft();
        $data=$ExamDraft->getExamMsg($exam_id,$room_id,$screenId);//room下考站
        $cont = [];
        if(!$data->isEmpty()) {

            $this->combinData($data, $cont);//整合数据
            $cont['exam_name'] = $exam->name;
            $request['room_id'] = $room_id;
            $request['exam_id'] = $exam_id;
            $request['data'] = count($data);
            $request['screen_id'] = $screenId;
        }else{
            throw new \Exception('该房间下暂时没有考试信息');
        }
        return view('osce::doorplate.doorplate_msg ', [
            'data'      =>$data,'msg'=>$cont,
            'current'=>json_decode($this->getExaminee($request)),
            'next'=>json_decode($this->getNextExaminee($request)),
            'status'=>$this->getStatusStatus($request),
            'room_id'=>$room_id,'exam_id'=>$exam_id,
            'screen_id'=>$screenId
        ]);
        } catch (\Exception $ex) {
           return redirect()->back()->withErrors($ex->getMessage());
        }
    }
    /**
     * 前端数据整合
     * @method GET
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function combinData(&$data,&$cont){
        $p_mins=0;$s_mins=0;
        if(!$data->isEmpty()){
            foreach($data as $key=>$val){
                $cont['name']=$val->name;
                $station=$val->station;
                if($station->type==3){
                    $paper = ExamPaper::where('id', $station->paper_id)->first();
                    $p_mins = $paper->length;
                }else{
                    $subject=$val->subject;
                    $s_mins =$subject->mins;
                    $data[$key]['name']=$subject->title;
                }
                $cont['mins']=$p_mins>$s_mins?$p_mins:$s_mins;
            }
        }
    }

    /**
     * 当前组满足华西
     * @method GET
     * @url /osce/doorplate/current-set
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExaminee(Request $request){
        $this->validate($request,[
            'exam_id' =>'required',
            'room_id' =>'required',
            'data' =>'required',
            'screen_id' =>'required',
        ]);
        $room_id=$request->get('room_id');
        $exam_id=$request->get('exam_id');
        $stations=$request->get('data');
        $exam_screening_id=$request->get('screen_id');
        $exam=Exam::where("id",$exam_id)->first();
        try {
            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::examineeByRoomId($room_id, $exam_id, $this->takeArr($stations), $exam_screening_id);
            } elseif ($exam->sequence_mode == 2) {

            } else {
                throw new \Exception('考试模式不存在！', -703);
            }
            return json_encode($examQueue);
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 下一组满足华西
     * @method GET
     * @url /osce/doorplate/next-set
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getNextExaminee(Request $request){
        $this->validate($request,[
            'exam_id' =>'required',
            'room_id' =>'required',
            'data' =>'required',
            'screen_id' =>'required',
        ]);
        $room_id=$request->get('room_id');
        $exam_id=$request->get('exam_id');
        $stations=$request->get('data');
        $exam_screening_id=$request->get('screen_id');
        $exam=Exam::where("id",$exam_id)->first();
        try {
        if ($exam->sequence_mode == 1) {
            $examQueue = ExamQueue::nextExamineeByRoomId($room_id, $exam_id, $this->takeArr($stations), $exam_screening_id);
        } elseif ($exam->sequence_mode == 2) {

        } else {
            throw new \Exception('考试模式不存在！', -703);
        }

        return json_encode($examQueue);
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 获取房间状态
     * @method GET
     * @url /osce/doorplate/door-status
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStatusStatus(Request $request){
        $this->validate($request,[
            'exam_id' =>'required',
            'room_id' =>'required',
            'screen_id' =>'required',
        ]);
        $room_id=$request->get('room_id');
        $exam_id=$request->get('exam_id');
        $exam_screening_id=$request->get('screen_id');
        $ExamDraft=new ExamDraft();
        try{
            $data=$ExamDraft->getExamMsg($exam_id,$room_id,$exam_screening_id);//room下考站
            if(!$data->isEmpty()) {
                $ExamStationStatus = new ExamStationStatus();
                $status = $ExamStationStatus->getExamMsg($exam_id, $exam_screening_id, $data);//1都准备好 2 有考试 3考完
                $endList = ExamQueue::where('room_id', $room_id)->where('exam_screening_id', $exam_screening_id)
                    ->where('exam_id', $exam_id)->where('status', '<', 3)->get();
                if (is_null($endList)) {//该场次下该房间学生已考完
                    $status = 3;
                }
                return $status;
            }else{
                throw new \Exception('对应考场下没有对应的考站！');
            }
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 随机数组
     * @method GET
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function takeArr($num){
        $arr=[];
        for($num;$num>0;$num--){
            $arr[]=$num;
        }
        return $arr;
    }
}