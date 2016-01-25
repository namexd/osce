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
     * * int        id        场所ID
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
                'id'  =>'required|integer'
            ]);

//            $id=$request->get('id');
//            $data=RoomVcr::where('room_id',$id)->select()->get();
//
//            $list=[];
//            foreach($data as $item){
//               $list[]=[
//                   $item->getVcr
//               ];
//            }
           $list[0]=[
               'name' =>'摄像机',
               'ip' =>'192.168.2.202',
               'username' =>'摄像机',
               'password' =>'123456',
               'port' =>'88',
               'channel' =>'测试',
               'description' =>'测试内容1',
               'status' =>'1',
           ];
           $list[1]=[
               'name' =>'摄像机1',
               'ip' =>'192.168.2.203',
               'username' =>'摄像机1',
               'password' =>'1234567',
               'port' =>'90',
               'channel' =>'测试1',
               'description' =>'测试内容21',
               'status' =>'2',
           ];
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
     * * int        id        摄像机ID(必须的)
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
               'id'  =>'required|integer'
           ]);

           $id=$request->get('id');
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
             $examModel=new ExamRoom();
             $stationVcrs=$examModel->getStionVcr($room_id,$exam_id);
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
     * * int        vcr_id           摄像机ID(必须的)
     * * datetime   startTime        开始标记点(必须的)
     * * datetime   EndTime          结束标记点(必须的)
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
                 'vcr_id' =>'required|integer',
                 'time'   =>'required',
            ]);
            $vcr_id=$request->get('vcr_id');
            $time=$request->get('time');
           try{
               $vcrs=Vcr::where('vcer_id',$vcr_id)->where('time','<',$time)->select()->get();
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
     * 根据考试id获取候考场所列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id        考试id(必须的)
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStatus(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required|integer',
        ]);

        try {
            //通过考生的腕表id来找到对应的队列id
            $uid = $request->input('uid');

            //找到对应的方法找到queue实例
            $queue = ExamQueue::findQueueIdByUid($uid);

            //修改状态
            $queue->status = 3;
            if (!$queue->save()) {
                throw new \Exception('状态修改失败！请重试');
            }
            return response()->json($this->success_data(['修改成功！']));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }
}