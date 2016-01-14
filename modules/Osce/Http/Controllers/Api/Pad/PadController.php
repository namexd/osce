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
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;

class PadController extends  CommonController{
    /**
     *根据场所ID获取摄像机列表
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

            $id=$request->get('id');
            $data=RoomVcr::where('room_id',$id)->select()->get();
            return response()->json(
                $this->success_data($data,1,'success')
            );
       }

    /**
     *根据摄像机ID 获取摄像机设置信息
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
     *根据考场ID和考试ID获取考场和考站的摄像头列表
     * @method GET
     * @url api/1.0/private/osce/pad/student-vcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        room_id        考场ID(必须的)
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
//             $examVcrs=RoomVcr::where('room_id',$room_id)->select()->get();
             $examModel=new ExamRoom();
             $stationVcrs=$examModel->getStionVcr($exam_id,$room_id);
             $data=array(
//                 'exam_vcr'  =>$examVcrs,
                 'station_vcr'  =>$stationVcrs,
             );
             return response()->json(
                 $this->success_data($data,1,'success')
             );
       }

    /**
     *根据时间段和摄像机ID 获取标记点列表
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
                 'startTime'   =>'required',
                 'endTime'   =>'required',
            ]);
            $vcr_id=$request->get('vcr_id');
            $startTime=$request->get('startTime');
            $endTime=$request->get('endTime');
           try{
               $stationVcr=new StationVcr();
               $vcrs=$stationVcr->getTime($vcr_id,$startTime,$endTime);
               return response()->json(
                   $this->success_data($vcrs,1,'success')
               );
           }catch (\Exception $ex){
               return response()->json(
                   $this->fail($ex)
               );
           }

       }
}