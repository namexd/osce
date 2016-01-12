<?php

namespace Modules\Osce\Http\Controllers\WinApp;


use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;


class IndexController extends CommonController
{


    /**
     *检测是否绑定
     * @method GET 接口
     * @url exam/watch-status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表Id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchStatus(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $status=Watch::where('watch_id',$id)->select('status')->first()->status;
        if($status==1){
            return response()->json(
                $this->success_rows(1,'已绑定')
            );
        }elseif($status==0){
            return response()->json(
                $this->success_rows(0,'未绑定')
            );
        }else{
            return response()->json(
                $this->success_rows(2,'腕表损坏或者正在维修')
            );
        }

    }

    /**
     *绑定腕表
     * @method GET 接口
     * @url exam/bound-watch
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getBoundWatch(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $result=Watch::where('id',$id)->update(['status'=>1]);
        if($result){
            $action='绑定';
            $student_id=ExamScreeningStudent::where('watch_id',$id)->select()->orderBy('signin_dt','DESC')->first()->student_id;
            $updated_at=ExamScreeningStudent::where('watch_id',$id)->select()->orderBy('updated_at','DESC')->first()->updated_at;
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$updated_at,'status'=>1),
                    'student_id'     =>$student_id,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
                return response()->json(
                    $this->success_rows(1,'绑定成功')
                );
        }else{
            return response()->json(
                $this->success_rows(0,'绑定失败')
            );
        }
    }

    /**
     *解除绑定腕表
     * @method GET 接口
     * @url exam/unwrap-watch
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUnwrapWatch(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $result=Watch::where('watch_id',$id)->update(['status'=>0]);
        if($result){
            $action='解绑';
            $student_id=ExamScreeningStudent::where('watch_id',$id)->select('student_id')->orderBy('updated_at','DESC')->first()->student_id;
            $updated_at=ExamScreeningStudent::where('watch_id',$id)->select('updated_at','DESC')->first()->updated_at;
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$updated_at,'status'=>0),
                    'student_id'     =>$student_id,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
                return response()->json(
                    $this->success_rows(1,'解绑成功')
                );
        }else{
            return response()->json(
                $this->success_rows(0,'解绑失败')
            );
        }
    }

    /**
     *检测学生状态
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
    public function getStudentDetails(Request $request){
        $this->validate($request,[
            'id_card' => 'required'
        ]);

        $idCard=$request->get('id_card');

        $code=Student::where('id_card',$idCard)->select('code')->first()->code;

        if(!$code){
           return response()->json(
               $this->success_rows(2,'未找到学生相关信息')
           );
        }
        $student_id=Student::where('id_card',$idCard)->seclct('id')->first()->id;

        $watch_id=ExamScreeningStudent::where('student_id',$student_id)->select()->first()->watch_id;
        if($watch_id){
            $status=Watch::where('watch_id',$watch_id)->select('status')->first()->status;
            if($status==1){
                return response()->json(
                    $this->success_rows(1,'已绑定腕表')
                );
            }
        }
         return response()->json(
                 $this->success_data($code,0,'未绑定腕表')
                );
    }
}