<?php

namespace Modules\Osce\Http\Controllers\Api;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;


class IndexController extends CommonController
{


    /**
     *检测腕表是否存在
     * @method GET 接口
     * @url /api/1.0/private/osce/watch-status
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        code        腕表code必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2016-1-12 17:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchStatus(Request $request){
        $this->validate($request,[
            'code' =>'required|integer'
        ]);
        $code=$request->get('code');
        $id=Watch::where('code',$code)->select()->first()->id;
        if(!$id){
            return response()->json(
                $this->success_rows(3,'该腕表不存在')
            );
        }else{
            $status=Watch::where('id',$id)->select()->first()->status;
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

    }

    /**
     *绑定腕表
     * @method GET 接口
     * @url /api/1.0/private/osce/bound-watch
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        腕表id(必须的)
     * * string     id_card   身份证号码(必须的)
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getBoundWatch(Request $request){
        $this->validate($request,[
            'id'      =>'required|integer',
            'id_card' =>'required'
        ]);
        $id=$request->get('id');
        $id_card=$request->get('id_card');
        $result=Watch::where('id',$id)->update(['status'=>1]);
        if($result){
            $action='绑定';
            $student_id=ExamScreeningStudent::where('idcard',$id_card)->select()->first()->id;
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
     * @url /api/1.0/private/osce/unwrap-watch
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
     * @date 2016-1-12   17:35
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUnwrapWatch(Request $request){
        $this->validate($request,[
            'id' =>'required|integer'
        ]);
        $id=$request->get('id');
        $id_card=$request->get('id_card');
        $student_id=ExamScreeningStudent::where('idcard',$id_card)->select()->first()->id;
        $result=Watch::where('watch_id',$id)->update(['status'=>0]);
        if($result){
            $action='解绑';
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
     * @url /api/1.0/private/osce/student-details
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id_card        身份证号码
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date 2016-1-12 17:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentDetails(Request $request){
        $this->validate($request,[
            'id_card' => 'required'
        ]);

        $idCard=$request->get('id_card');

        $student_id=Student::where('idcard',$idCard)->select('id')->first()->id;

        if(!$student_id){
           return response()->json(
               $this->success_rows(2,'未找到学生相关信息')
           );
        }

        $data=array('code'=>$student_id);

        $watch_id=ExamScreeningStudent::where('student_id',$student_id)->select()->first();
        if(count($watch_id)>0){
            $status=Watch::where('watch_id',$watch_id)->select('status')->first()->status;
            if($status==1){
                return response()->json(
                    $this->success_data($data,1,'已绑定腕表')
                );
            }
        }
         return response()->json(
                 $this->success_data($data,0,'未绑定腕表')
                );
    }

    /**
     * 添加腕表接口
     *
     * @api GET /api/1.0/private/osce/watch/add
     * @access private
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string		code			设备编码(必须的)
     * * int		user_id			操作人编号(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author limingyao <limingyao@misrobot.com>
     * @date 2016-01-12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddWatch(Request $request){

        $this->validate($request,[
            'code'          =>  'required',
            'user_id'       =>  'required|integer'
        ]);

        try{
            $watch=Watch::create([
                'code'          =>  $request->get('code'),
                'name'          =>  $request->get('name',''),
                'status'        =>  $request->get('status',1),
                'description'   =>  $request->get('description',''),
                'factory'       =>  $request->get('factory',''),
                'sp'            =>  $request->get('sp',''),
                'created_user_id'=> $request->get('user_id'),
            ]);

            if($watch->id>0){
                return response()->json(
                    $this->success_data()
                );
            }

            return response()->json(
                $this->fail(new \Exception('添加腕表失败'))
            );
        }
        catch( \Exception $ex){
            return response()->json(
                $this->fail($ex)
            );
        }

    }


    /**
     * 删除腕表接口
     *
     * @api GET /api/1.0/private/osce/watch/delete
     * @access private
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int		id			设备id(必须的)
     * * int		user_id			操作人编号(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author limingyao <limingyao@misrobot.com>
     * @date 2016-01-12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDeleteWatch(Request $request){

        $this->validate($request,[
            'id'            =>  'required|integer',
            'user_id'       =>  'required|integer'
        ]);

        $count=Watch::destroy($request->get('id'));

        if($count>0){
            return response()->json(
                $this->success_data()
            );
        }

        return response()->json(
            $this->fail(new \Exception('删除腕表失败'))
        );
    }



    /**
     * 更新腕表状态接口
     *
     * @api GET /api/1.0/private/osce/watch/delete
     * @access private
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int		id			设备id(必须的)
     * * int        status      状态
     * * int		user_id		操作人编号(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author limingyao <limingyao@misrobot.com>
     * @date 2016-01-12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUpdateWatch(Request $request){

        $this->validate($request,[
            'id'            =>  'required|integer',
            'status'        =>  'required|integer',
            'user_id'       =>  'required|integer'
        ]);


        $count=Watch::where('id','=',$request->get('id'))
            ->update(['status'=>$request->get('status')]);

        if($count>0){
            return response()->json(
                $this->success_data()
            );
        }

        return response()->json(
            $this->fail(new \Exception('更新腕表失败'))
        );
    }

    /**
     *获取当日考试列表
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(){
        $exam=new Exam();
        $time=time();
        $examList=$exam->getTodayList($time);
        if(count($examList)){
             return response()->json(
                 $this->success_rows(1,'success',count($examList),$pagesize=1,count($examList),$examList)
             );
        }
        return response()->json(
            $this->fail(new \Exception('今日无考试场次'))
        );

    }
}