<?php

namespace Modules\Osce\Http\Controllers\Doorplate;


use Illuminate\Http\Request;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\PadLogin\PadLogin;
use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Http\Controllers\Api\LoginPullDownController;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\PadLogin\Time;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\Billboard\BillboardRepository;
use Modules\Osce\Repositories\Common;

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
    public function doorStart()
    {
        $padLog   = new PadLoginRepository(new PadLogin());
        $examList = $padLog->examList(new Time());

        $roomList = [];
        if(count($examList)){
            $roomList = $padLog->roomList($examList[0]['exam_id']);
        }

        return view('osce::doorplate.login ', [
            'examList'      => $examList,
            'roomList'      => $roomList
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
    public function getExamMsg(Request $request ,BillboardRepository $billboardRepository)
    {
        $this->validate($request,[
            'examId' =>'required',
            'roomId' =>'required',
        ]);
        $exam_id = $request->get('examId');
        $room_id = $request->get('roomId');

        try{
            $exam = Exam::doingExam($exam_id);
            if($exam->status == 2){
                throw new \Exception('该考试已结束');
            }
            //获取当前场次id
            $screen  = Common::getExamScreening($exam_id);
            $ExamDraft  = new ExamDraft();
            $data       = $ExamDraft->getExamMsg($exam_id, $room_id, $screen->id);  //room下考站

            $cont = [];
            if(!$data->isEmpty())
            {
                $this->combinData($data, $cont);    //整合数据
                $cont['exam_name']  = $exam->name;
                $request['room_id'] = $room_id;
                $request['exam_id'] = $exam_id;
            }else{
                throw new \Exception('本场次该房间下暂时没有考试信息');
            };

            // 获取到当前组 TODO: Zhoufuxiang 2016-06-12
            $current   = $this->getExaminee($request);
            // 获取到下一组
            $nextGroup = $this->getNextExaminee($request);

            if(empty($current) || count($current)==0){
                $RoomName = '';
            }else{
                $studentId = $current->first()->student_id;
                //获取下一个考场
                $nextRoom =  $billboardRepository->getRoomData($exam_id, $studentId, $room_id);
                if(is_null($nextRoom)){
                    $RoomName = '';
                }else{
                    $RoomName = $nextRoom->room_name;
                }
            }

            return view('osce::doorplate.doorplate_msg ', [
                'data'      => $data,
                'msg'       => $cont,
                'current'   => $current,        //当前组
                'next'      => $nextGroup,      //下一组
                'status'    => $this->getStatusStatus($request),
                'room_id'   => $room_id,
                'exam_id'   => $exam_id,
                'screen_id' => $screen->id,
                'room_name' => Room::where('id',$room_id)->first()->name,
                'nextRoom'  => $RoomName
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
    protected function combinData(&$data, &$cont)
    {
        $p_mins=0;      $s_mins=0;
        if(!$data->isEmpty())
        {
            foreach($data as $key=>$val)
            {
                $cont['name'] = $val->name;
                $station    = $val->station;
                //理论考站，获取试卷考试时长
                if($station->type==3)
                {
                    $paper  = ExamPaper::where('id', $station->paper_id)->first();
                    $p_mins = $paper->length;
                }else
                {
                    $subject= $val->subject;
                    $s_mins = $subject->mins;
                    $data[$key]['name'] = $subject->title;
                }
                $cont['mins'] = ($p_mins > $s_mins) ? $p_mins : $s_mins;
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
    public function getExaminee2(Request $request)
    {
        $this->validate($request,[
            'exam_id'   =>'required',
            'room_id'   =>'required',
            'data'      =>'required',
            'screen_id' =>'required',
        ]);
        $room_id  = $request->get('room_id');
        $exam_id  = $request->get('exam_id');
        $stations = $request->get('data');
        $exam_screening_id = $request->get('screen_id');
        $exam   = Exam::where("id", $exam_id)->first();
        try {
            if ($exam->sequence_mode == 1) {
                //先拿到考场下面所有的考站对应的老师
                $examQueue = ExamQueue::examineeByRoomId($room_id, $exam_id, $this->takeArr($stations), $exam_screening_id);
            } elseif ($exam->sequence_mode == 2) {  //考站模式
                $ExamDraft = new ExamDraft();
                $data      = $ExamDraft->getExamMsg($exam_id, $room_id, $exam_screening_id); //room下考站
                $examQueue = collect();
                if(!$data->isEmpty()) {
                    foreach ($data as $v) {
                        $examQueue->push(ExamQueue::examineeByStationId($v->station_id, $exam_id, $exam_screening_id));
                    }
                }
            } else {
                throw new \Exception('考试模式不存在！', -703);
            }
            return $examQueue;

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 获取当前组考生队列（满足华西）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Collection
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-06-12 14:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExaminee(Request $request)
    {
        $this->validate($request,[
            'exam_id'   =>'required',
            'room_id'   =>'required',
        ]);
        $room_id  = $request->get('room_id');
        $exam_id  = $request->get('exam_id');

        try{
//            $ExamQueue = new ExamQueue();
//            $examQueue = $ExamQueue->getExamineeByRoom($exam_id, $room_id, $stations);

            //获取当前组 缓存key
            $currKey   = 'current_room_id' . $room_id .'_exam_id'.$exam_id;
            //从缓存中取出 当前组考生队列
            $examQueue = \Cache::get($currKey);
            if(count($examQueue) == 0){
               if($this->ExamineRoomCache($exam_id,$room_id)){
                   $examQueue = \Cache::get($currKey);
               }
            }
            \Log::info('电子门牌获取到的当前组',[$examQueue]);
            return $examQueue;

        }catch (\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }




    /**
     * /检查缓存是否为空
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2016-6-23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private  function ExamineRoomCache($examId,$roomId)
    {
        //获取当前组 缓存key
        $currKey   = 'current_room_id' . $roomId .'_exam_id'.$examId;

        //获取下一组 缓存key
        $nextKey   = 'next_room_id' . $roomId .'_exam_id'.$examId;

        //从缓存中取出 下一组考生队列
        $nextQueue = \Cache::get($nextKey);
        //从缓存中取出 当前组考生队列
        $examQueue = \Cache::get($currKey);

        //2、获取场次
        $examScreening   = Common::getExamScreening($examId);
        $examScreeningId = intval($examScreening->id);       //获取场次ID

        if(count($nextQueue)==0 && count($examQueue)==0){
            //检查房间队列,是否,还有,没有,结束的.
            $roomQueue = ExamQueue::where('exam_id','=',$examId)
                ->where('room_id','=',$roomId)
                ->whereIn('status',[0,1,2])->first();
            if(!is_null($roomQueue)){
                Common:: updateAllCache($examId,$examScreeningId);
            }
        }else{
            Common:: updateAllCache($examId,$examScreeningId);
        }
        return true;

    }

    /**
     * 获取下一组考生队列（满足华西）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Collection
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-06-13 14:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getNextExaminee(Request $request)
    {
        $this->validate($request,[
            'exam_id'   =>'required',
            'room_id'   =>'required',
        ]);
        $room_id  = $request->get('room_id');
        $exam_id  = $request->get('exam_id');

        try{
//            $ExamQueue = new ExamQueue();
//            $examQueue = $ExamQueue->getNextExamineeByRoom($exam_id, $room_id, $stations);

            //获取下一组 缓存key
            $nextKey   = 'next_room_id' . $room_id .'_exam_id'.$exam_id;
            //从缓存中取出 下一组考生队列
            $nextQueue = \Cache::get($nextKey);
            if(count($nextQueue)==0){
                if($this->ExamineRoomCache($exam_id,$room_id)){
                    $nextQueue = \Cache::get($nextKey);
                }
            }
            
            \Log::info('电子门牌获取到的下一组',[$nextQueue]);
            return $nextQueue;

        }catch (\Exception $ex)
        {
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
    public function getNextExaminee2(Request $request){
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
        } elseif ($exam->sequence_mode == 2) {//考站模式
            $ExamDraft=new ExamDraft();
            $data=$ExamDraft->getExamMsg($exam_id,$room_id,$exam_screening_id);//room下考站
            $examQueue = collect();
            if(!$data->isEmpty()) {
                foreach ($data as $v) {
                    $examQueue->push(ExamQueue::nextExamineeByStationId($v->station_id, $exam_id, $exam_screening_id));
                }
            }
        } else {
            throw new \Exception('考试模式不存在！', -703);
        }

        return $examQueue;
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
    public function getStatusStatus(Request $request)
    {
        $this->validate($request,[
            'exam_id'   => 'required',
            'room_id'   => 'required',
        ]);
        $room_id    = $request->get('room_id');
        $exam_id    = $request->get('exam_id');
        try{
            //重新获取场次
            $examScreening = Common::getExamScreening($exam_id);
            $screen_id = $examScreening->id;
            $ExamDraft = new ExamDraft();
            $data   = $ExamDraft->getExamMsg($exam_id, $room_id, $screen_id);   //room下考站
            if($data->isEmpty())
            {
                throw new \Exception('对应考场下没有对应的考站！');
            }

            $ExamStationStatus = new ExamStationStatus();
            //1都准备好 2 有考试 3考完 4都未准备(还没轮到开始)
            $status = $ExamStationStatus->getExamMsg($exam_id, $screen_id, $data);
            //获取该房间该场次下安排的所有学生
            $planList = ExamPlan::where('room_id', $room_id)->where('exam_screening_id', $screen_id)
                                ->where('exam_id', $exam_id)->get()->pluck('student_id')->toArray();
            //该场次下对应结束考试的学生数量
            $endList = ExamScreeningStudent::where('exam_screening_id', $screen_id)
                                           ->whereIN('student_id', $planList)
                                           ->where('is_end', 1)->count();
            //该场次下该房间学生已考完
            if (count($planList) == $endList) {
                $status = 3;
            }
            return $status;

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