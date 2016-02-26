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
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;

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
     *根据考场ID和考试ID获取考场和考站的摄像头列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/student-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id          考场ID(必须的)
     * * int        exam_id           考试ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
       public function getStudentVcr(Request $request){
             $this->validate($request,[
                'room_id' => 'required|integer',
                'exam_id'    => 'required|integer'
             ]);
             $room_id=$request->get('room_id');
             $exam_id=$request->get('exam_id');
             $stationModel=new StationVcr();
             $stationVcrs=$stationModel->getStionVcr($room_id,$exam_id);
             return response()->json(
                 $this->success_data($stationVcrs,1,'success')
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
                 'station_vcr_id'     =>'required|integer',
                 'exam_id'            =>'required',
                 'begin_dt'           =>'sometimes',
                 'end_dt'             =>'sometimes',
            ]);
            $stationVcrId=$request->get('station_vcr_id');
            $beginDt=$request->get('begin_dt');
            $examId=$request->get('exam_id');
            $endDt=$request->get('end_dt');
           try{
               $stationVideoModel=new StationVideo();
               $vcrs=$stationVideoModel->getTiming($stationVcrId,$beginDt,$examId,$endDt);
               return response()->json(
                   $this->success_data($vcrs,1,'success')
               );
           }catch (\Exception $ex){
               return response()->json(
                   $this->fail($ex)
               );
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
              $pagination=$examQueue->getPagination();
              $students = $examQueue->getStudent($mode, $exam_id);
              return response()->json(
                  $this->success_rows(1, 'success', $pagination->total(), config('msc.page_size'), $pagination->currentPage(), $students)
              );
          }catch( \Exception $ex){
              return response()->json(
                  $this->fail($ex)
              );
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
              $examList=ExamRoom::where('exam_id',$request->get('exam_id'))->select()->get();
              $rooms=[];
              foreach($examList as $examRoom){
                $rooms[]=$examRoom->room;
              }
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
     * 考试在后修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Jiangzhiheng
     */
    public function getChangeStatus(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer',
        ]);

        try {
            //获取当前的服务器时间
            $date = date('Y-m-d H:i:s');
            //通过考生的腕表id来找到对应的队列id
            $studentId = $request->input('student_id');

            //找到对应的方法找到queue实例
            $queue = ExamQueue::findQueueIdByStudentId($studentId);
            //todo 判断学生考试是否结束了
            if($queue->status==3){
                throw new \Exception('该考生时间已到，考试已结束',2100);
            }
            //修改状态
            $queue->status = 3;
            $queue->end_dt = $date;

            if (!$queue->save()) {
                throw new \Exception('状态修改失败！请重试',2000);
            }
            return response()->json($this->success_data([$date,$queue->exam_screening_id]));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }

}