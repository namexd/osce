<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/13 0013
 * Time: 9:36
 */

namespace Modules\Osce\Http\Controllers\Api\Pad;


use Illuminate\Http\Request;

use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;

class PadController extends  CommonController{
    /**
     *根据场所ID获取摄像机列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/room-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id        场所ID
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
       public function getRoomVcr(Request $request){
            $this->validate($request,[
                'room_id'  =>'required|integer'
            ]);

            $id=$request->get('room_id');
            $data=RoomVcr::where('room_id',$id)->select()->get();

            $list=[];
            foreach($data as $item){
               $list[]=[
                   $item->getVcr
               ];
            }

            return response()->json(
                $this->success_data($list,1,'success')
            );
       }

    /**
     *根据摄像机ID 获取摄像机设置信息(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        vcr_id        摄像机ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
       public function getVcr(Request $request){
           $this->validate($request,[
               'vcr_id'  =>'required|integer'
           ]);

           $id=$request->get('vcr_id');
           $data=Vcr::find($id);
           return response()->json(
               $this->success_data($data,1,'success')
           );
       }

    /**
     *根据考场ID和考试ID获取 考场和考站的 摄像头列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/student-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id          考场ID(必须的)
     * * int        exam_id          考试ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentVcr(Request $request){
        try{
            $this->validate($request,[
                'room_id'   => 'required|integer',
                'exam_id'   => 'required|integer'
            ]);
            $room_id = $request->get('room_id');
            $exam_id = $request->get('exam_id');

            $stationModel = new StationVcr();
            $stationVcrs  = $stationModel->getStationVcr($exam_id,$room_id);

            return response()->json(
                $this->success_data($stationVcrs,1,'success')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }


    /**
     * 根据考场ID和考试ID获取 考站列表、考站对应的摄像机信息 (接口)
     * @api GET /osce/pad/stations-vcrs
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStationsVcrs(Request $request){
        $this->validate($request,[
            'room_id'   => 'required|integer',
            'exam_id'   => 'required|integer'
        ]);
        $room_id = $request->get('room_id');
        $exam_id = $request->get('exam_id');

        $stationVcr = new StationVcr();
        $datas   = $stationVcr->getStationVcr($exam_id,$room_id);



        return response()->json(
            $this->success_data($datas,1,'success')
        );
    }

    /**
     * 根据考场ID、考试ID和teacher_id获取考站的摄像头信息(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/teacher-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}    2016-3-9
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getTeacherVcr(Request $request)
    {
        $this->validate($request,[
            'exam_id'       => 'required|integer',
            'teacher_id'    => 'required|integer',
            'room_id'       => 'required|integer',
        ]);
        //获取参数
        $exam_id    = $request->get('exam_id');
        $room_id    = $request->get('room_id');
        $teacher_id = $request->get('teacher_id');
        $stationTea = new StationTeacher();
        $vcrInfo    = $stationTea->getVcrInfo($exam_id, $teacher_id, $room_id);

        return response()->json(
            $this->success_data($vcrInfo, 1, 'success')
        );
    }

    /**
     *根据时间段和摄像机ID 获取标记点列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/timing-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        station_vcr_id            考站-摄像机关联表id(必须的)
     * * int        exam_id                   考试Id(必须的)
     * *
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getTimingList(Request $request){
        $this->validate($request,[
            'vcr_id'    =>'required|integer',
            'exam_id'   =>'required',
            'begin_dt'  =>'sometimes',
            'end_dt'    =>'sometimes',
        ]);
        $vcrId  = $request->get('vcr_id');
        $examId = $request->get('exam_id');
        $beginDt= $request->get('begin_dt');
        $endDt  = $request->get('end_dt');

        try{
            $stationVideoModel = new StationVideo();
            $vcrs = $stationVideoModel->getTiming($vcrId,$beginDt,$examId,$endDt);
            //获取标记点列表
            $videoLabels = $stationVideoModel->getVideoLabels($examId, $vcrId, $beginDt, $endDt);
            //组合返回数据
            $data = [
                'vcrs'          => $vcrs,
                'videoLabels'   => $videoLabels
            ];

            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        }catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }
    }

    /**
     *候考提醒(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/wait-student
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id        考试ID
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWaitStudent(Request $request){
        $this->validate($request,[
            'exam_id' =>'required|integer'
        ]);
        $exam_id=$request->get('exam_id');
        $mode=Exam::where('id',$exam_id)->select('sequence_mode')->first()->sequence_mode;
        $time=time();
        $examQueue=new ExamQueue();
        try {
            $pagination= $examQueue->getPagination();
            $students  = $examQueue->getStudent($mode, $exam_id);

            return response()->json(
                $this->success_rows(1, 'success', $pagination->total(), config('msc.page_size'), $pagination->currentPage(), $students)
            );

        }catch( \Exception $ex){
            return response()->json($this->fail($ex));
        };
    }


    /**
     *根据考试id获取考试场所列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/exam-room
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id        考试id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamRoom(Request $request){
        $this->validate($request,[
          'exam_id'  =>'required|integer'
        ]);

        $exam_id = $request->get('exam_id');
        $exam   = Exam::where('id','=',$exam_id)->select(['id','name','sequence_mode'])->first();
        $rooms  = $this->getRoomDatas($exam);           //根据考试获取对应的所有考场
        $rooms  = array_values(array_unique($rooms));    //去重，并取值（键排序）

        return response()->json(
            $this->success_data($rooms,1,'success')
        );
    }

    /**
     * 根据考试id获取候考场所列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/wait-room
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id        考试id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
       public function getWaitRoom(Request $request){
             $this->validate($request,[
                 'exam_id'   =>'required|integer'
             ]);
             $examModel = new ExamRoom();
             $exam_id=$request->get('exam_id');
         try{
             $examRoomList=$examModel->getWaitRoom($exam_id);

             return response()->json(
                 $this->success_data($examRoomList,1,'success')
             );
           }catch( \Exception $ex){
                   return response()->json(
                       $this->fail($ex)
                   );
               };
       }


    /**
     * 考试在后修改状态(结束考试请求)
     * url \osce\pad\change-status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Jiangzhiheng
     */
    public function getChangeStatus(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer',
            'station_id' => 'sometimes|integer',
            'user_id' => 'required|integer'
        ]);

//        try {
            //获取当前的服务器时间
            $date = date('Y-m-d H:i:s');
            //通过考生的腕表id来找到对应的队列id
            $studentId = $request->input('student_id');
            $stationId = $request->input('station_id', null);
            $teacherId = $request->input('user_id');

            $queue = ExamQueue::endStudentQueueExam($studentId, $stationId, $teacherId);
            //将该条信息的首位置零
//            $queue->stick = 0;
//            if (!$queue->save()) {
//                throw new \Exception('结束考试失败', -10);
//            }

            //考试结束后，调用向腕表推送消息的方法
            $examScreeningStudentModel = new ExamScreeningStudent();
            $examScreeningStudentData = $examScreeningStudentModel->where('exam_screening_id','=',$queue->exam_screening_id)
                ->where('student_id','=',$queue->student_id)->first();

            $watchModel = new Watch();
            $watchData = $watchModel->where('id','=',$examScreeningStudentData->watch_id)->first();
            $studentWatchController = new StudentWatchController();
            $request['nfc_code'] = $watchData->code;
            $studentWatchController->getStudentExamReminder($request,$stationId);

            return response()->json($this->success_data(['end_time'=>$date,'exam_screening_id'=>$queue->exam_screening_id,'student_id'=>$studentId],1,'结束考试成功'));

//        } catch (\Exception $ex) {
//            \Log::alert('EndError', [$ex->getFile(), $ex->getLine(), $ex->getMessage()]);
//            return response()->json($this->fail($ex));
//        }
    }

    /**
     * 获取当前正在进行的所有考试 (接口)
     * @method GET
     * @url    /osce/pad/doing-exams
     * @access public
     *
     * @return object
     *
     * @version 2.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}    2016-3-21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDoingExams(Request $request)
    {
        //获取正在进行中的考试列表
        $examList    = Exam::where('status','=', 1)->select(['id','name'])->get();

        return response()->json(
            $this->success_data($examList, 1, 'success')
        );
    }

    /**
     * 获取所有 历史考试(已经考完) (接口)
     * @method GET
     * @url    /osce/pad/done-exams
     * @access public
     *
     * @return object
     *
     * @version 2.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}    2016-3-23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDoneExams(Request $request)
    {
      $page = $request->get('pagesize',1);
        //获取已经考完的所有考试列表
        $examList = Exam::where('status','=', 2)->select(['id','name', 'begin_dt', 'end_dt'])->paginate(10);
//        return response()->json(
//            $this->success_rows(1, 'success', $noticeList['total'], config('osce.page_size'), $page, $list)
//        );
//        return response()->json(
//            $this->success_rows(1, 'success', $pagination->total(), $pagesize = config('msc.page_size'),
//                $pagination->currentPage(), $data)
//        );
        //返回数据
        return $this->success_rows(1,'获取成功',

            $examList->lastPage(),
            $examList->perPage(),
            $examList->currentPage(),
            $examList->toArray()['data']
        );
    }

    /**
     *历史回放，获取所有已经考完的考试对应的摄像头列表(接口)
     * @method GET
     * @url     /osce/pad/all-vcrs-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id          考场ID
     * * int        exam_id          考试ID
     *
     * @return ${response}
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME} 2016-3-25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAllVcrsList(Request $request)
    {
        $this->validate($request,[
            'room_id'   => 'sometimes|integer',
            'exam_id'   => 'sometimes|integer'
        ]);
        $room_id = $request->get('room_id');
        $exam_id = $request->get('exam_id');
        $vcrModel = new  Vcr();
        if(!empty($exam_id) && !empty($room_id)){
            $vcrIds  = $vcrModel->getVcrIds($room_id,$exam_id);

        }elseif (!empty($room_id)){
            $vcrIds = $vcrModel->getVcrIdsToRoom($room_id);

        }elseif (!empty($exam_id)){
            $vcrIds = $vcrModel->getVcrIdsToExam($exam_id);

        }else{
            $vcrIds = $vcrModel->getVcrIdsToAllExam();
        }
        //分页获取摄像机信息
        $vcrs = Vcr::whereIn('id', $vcrIds)->paginate(10);

        //返回分页数据
        return $this->success_rows(1,'获取成功',
            $vcrs->lastPage(),      $vcrs->perPage(),
            $vcrs->currentPage(),   $vcrs->toArray()['data']
        );
    }

    /**
     *历史回放，获取所有已经考完的考试对应的摄像头列表(接口)
     * @method GET
     * @url     /osce/pad/all-rooms
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id          考场ID
     * * int        exam_id          考试ID
     *
     * @return ${response}
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME} 2016-3-25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAllRooms(Request $request){
        $this->validate($request,[
            'exam_id'  =>'sometimes|integer'
        ]);

        $exam_id = $request->get('exam_id');
        $roomIds   = [];
        if(empty($exam_id)){
            //未选考试，列出所有考试对应的所有考场
            $examList = Exam::where('status','=', 2)->select(['id','name','sequence_mode'])->get();
            if(count($examList) != 0){
                foreach ($examList as $exam) {
                    $roomId  = $this->getRoomDatas($exam, true);      //根据考试获取对应的所有考场
                    $roomIds = array_merge($roomIds, $roomId);
                }
            }

        }else{
            $exam   = Exam::where('id','=',$exam_id)->select(['id','name','sequence_mode'])->first();
            $roomIds  = $this->getRoomDatas($exam, true);   //根据考试获取对应的所有考场
        }
        $roomIds = array_values(array_unique($roomIds));    //去重，并取值（键排序）
        $rooms = Room::whereIn('id', $roomIds)->select(['id', 'name'])->paginate(10);

        //返回分页数据
        return $this->success_rows(1,'获取成功',
            $rooms->lastPage(),      $rooms->perPage(),
            $rooms->currentPage(),   $rooms->toArray()['data']
        );

    }
    /**
     * 根据考试获取对应的所有考场
     * TODO:Zhoufuxiang 2016-3-23
     * @return object
     */
    public function getRoomDatas($exam, $status = false){
        $rooms   = [];
        $roomIds = [];
        if($exam->sequence_mode == 2){
            //根据考试获取 对应考站
            $examStation = ExamStation::where('exam_id','=',$exam->id)->get();
            if(count($examStation)){
                foreach ($examStation as $item) {
                    //获取考站对应的考场
                    $roomStation = RoomStation::where('station_id','=',$item->station_id)->first();
                    $rooms[] = $roomStation->room;
                    $roomIds[] = $roomStation->room_id;
                }
            }
        }else{
            $examRooms = ExamRoom::where('exam_id','=',$exam->id)->get();
            foreach($examRooms as $examRoom){
                $rooms[] = $examRoom->room;
                $roomIds[] = $examRoom->room_id;
            }
        }
        $rooms = array_unique($rooms);
        $roomIds = array_unique($roomIds);

        if($status){
            return $roomIds;
        }else{
            return $rooms;
        }
    }

    /**
     * 根据考试和考场获取对应的所有摄像机
     * TODO:Zhoufuxiang 2016-3-24
     * @return object
     */
    public function getVcrsDatas($exam_id, $room_id){
        $vcrs = [];
        $examStation = ExamStation::where('exam_id','=',$exam_id)->get();
        if(count($examStation)){
            foreach ($examStation as $item) {
                $roomVcr = StationVcr::where('station_id',$item->station_id)->first();
                $vcrs[] = $roomVcr->vcr;
            }
        }
        //根据考场获取摄像头
        $roomVcr = RoomVcr::where('room_id',$room_id)->get();
        foreach($roomVcr as $item){
            $vcrs[] = $item->getVcr;
        }

        return $vcrs;
    }

}