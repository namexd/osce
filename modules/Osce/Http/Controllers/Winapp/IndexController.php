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
        $IsEnd=ExamScreeningStudent::where('watch_id',$id)->select('is_end')->first()->is_end;
        if($IsEnd==1){
            return response()->json(
                $this->success_rows(1,'已绑定')
            );
        }
        if($IsEnd==0){
            return response()->json(
                $this->success_rows(0,'未绑定')
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
        $action='绑定';
        $userId=ExamScreeningStudent::where('watch_id',$id)->select()->first()->student_id;
        $result=ExamScreeningStudent::where('watch_id',$id)->update(['is_end'=>1]);
        if($result){
            $signinDt=ExamScreeningStudent::where('watch_id',$id)->select()->first()->signin_dt;
            $result=Watch::where('id',$id)->update(['status'=>1]);
            if($result){
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$signinDt,'is_end'=>1,'status'=>1),
                    'create_user_id' =>$userId,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
                return response()->json(
                    $this->success_rows(1,'绑定成功')
                );
            }
        }else{
            return response()->json(
                $this->success_rows(0,'绑定失败','false')
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
        $action='解绑';
        $userId=ExamScreeningStudent::where('watch_id',$id)->select()->first()->student_id;
        $result=ExamScreeningStudent::where('watch_id',$id)->update(['is_end'=>0]);
        if($result){
            $updated_at=ExamScreeningStudent::where('watch_id',$id)->select('updated_at')->first()->updated_at;
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$updated_at,'is_end'=>0,'status'=>0),
                    'create_user_id' =>$userId,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
                return response()->json(
                    $this->success_rows(1,'解绑成功')
                );
            }
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

        $students=Student::where('id_card',$idCard)->select('id','code')->get();
        foreach($students as $item){
            $student=[
                'id'    =>$item->id,
                'code'  =>$item->code,
//                'exam_id'  =>$item->exam_id,
            ];
        }
        if(!$student){
           return response()->json(
               $this->success_rows(2,'未找到学生相关信息')
           );
        }
        $student['is_end']=ExamScreeningStudent::where('student_id',$student['id'])->select('is_end')->first()->is_end;
//        $student['exam']=Exam::where('exam_id',$student['exam_id'])->select()->first(); //查询准考证号

        if($student['is_end']==1){
            return response()->json(
                $this->success_data($student,0,'已绑定')
            );
        }
         return response()->json(
                 $this->success_data($student,1,'未绑定')
                );
    }


}