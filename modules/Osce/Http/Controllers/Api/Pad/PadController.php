<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/13 0013
 * Time: 9:36
 */

namespace Modules\Osce\Http\Controllers\Api\Pad;


use Illuminate\Http\Request;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Http\Controllers\CommonController;

class PadController extends  CommonController{
    /**
     *根据场所ID获取摄像机列表
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
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
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
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
           $data=Vcr::where('id',$id)->select();
           return response()->json(
               $this->success_data($data,1,'success')
           );
       }

    /**
     *根据考场ID和考试ID获取考场和考站的摄像头列表
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
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
                'station_id' => 'required|integer',
                'room_id'    => 'required|integer'
             ]);
             $stationId=$request->get('station_id');
             $room_id=$request->get('room_id');
             $stationVcrs=StationVcr::where('station_id',$stationId)->select()->get();
             $rooms=RoomVcr::where('room_id',$room_id)->select()->get();
             $data=array(
                 'station_vcr'  =>$stationVcrs,
                 'room_vcr'  =>$rooms,
             );
             return response()->json(
                 $this->success_data($data,1,'success')
             );
       }


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
}