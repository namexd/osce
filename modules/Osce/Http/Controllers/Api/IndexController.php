<?php

namespace Modules\Osce\Http\Controllers\Api;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;


use Modules\Osce\Entities\ExamAbsent;

use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreening;

use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;


class IndexController extends CommonController
{

    /**
     *检测腕表是否存在
     * @method GET 接口
     * @url /api/1.0/private/osce/watch/watch-status
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
            'code' =>'required'
        ]);
        $code=$request->get('code');
        $id = Watch::where('code', $code)->select()->first();
        if (!$id) {
            return \Response::json(array('code' => 3));//数据库无腕表
        } else {
            $id=$id->id;
            $status = Watch::where('id', $id)->select()->first()->status;
            $student_id = ExamScreeningStudent::where('watch_id', $id)->select()->orderBy('id','DESC')->first();
            //腕表状态是使用中
            if ($status == 1) {
                if(!$student_id){
                    $data=array('student_id'=>'','status'=>$status);
                    return response()->json(
                        $this->success_data($data,4, '该腕表已绑定')
                    );
                }
                $code = Student::where('id', $student_id->id)->select('code')->first();
                if(!$code){
                    $data = array('code' => '','status'=>$status,'student_id'=>$student_id->id);
                }else{
                    $data = array('code' => $code,'status'=>$status,'student_id'=>$student_id->id);
                }
                //腕表绑定中返回绑定的学生信息
                return response()->json(
                    $this->success_data($data,1, '该腕表已绑定')
                );
            } elseif ($status == 0) {
                $data=array('student_id'=>'','status'=>$status);
                   //腕表未绑定
                   return response()->json(
                       $this->success_data($data, 0, '未绑定')
                   );
               } else {
                $data=array('student_id'=>'','status'=>$status);
                   return response()->json(
                       $this->success_data($data, 2, '该腕表已损坏')
                   );
               }
        }
    }

    /**
     *绑定腕表
     * @method GET 接口
     * @url /api/1.0/private/osce/watch/bound-watch
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
            'code'      =>'required',//腕表code
            'id_card'   =>'required',//身份证号
            'exam_id'   =>'required' //考试id
        ]);
        $code=$request->get('code');
        $id_card=$request->get('id_card');
        $exam_id=$request->get('exam_id');
        $id=Watch::where('code',$code)->select('id')->first()->id;//获取腕表id
        $student_id=Student::where('idcard',$id_card)->where('exam_id',$exam_id)->select()->first();//查询学生是否参加当前考试
        if(!$student_id){
            return \Response::json(array('code' => 3));//没有参加当前考试
        }
        $student_id=$student_id->id;//获取学生id
        $planId=ExamPlan::where('student_id',$student_id)->where('exam_id',$exam_id)->select('id')->first();//查询是否安排考试
        if(!$planId ){
            return \Response::json(array('code' =>4));//未安排当前考试
        }
        $students=$this->getStudentList($request);
        $idcards=[];
        $students=json_decode($students->content());
        foreach($students->data as $item){
         $idcards[]=$item->idcard;
        }
        if(!in_array($id_card,$idcards)){
            return \Response::json(array('code'=>5));//未在考试队列 请等待
        }
        $examStatus=Exam::where('status','=',1)->select()->first();


        //修改场次状态
        $examScreeningModel =   new ExamScreening();
        $examScreening  =   $examScreeningModel ->  getExamingScreening($exam_id);
        if(is_null($examScreening))
        {
            $examScreening      =   $examScreeningModel  ->  getNearestScreening($exam_id);
            $examScreening      ->  status  =   1;
            if(!$examScreening      ->  save())
            {
                throw new \Exception('场次开考失败！');
            }
        }

        if($examStatus){
            if($examStatus->id!=$exam_id){
                return \Response::json(array('code'=>6));
            }
        }
        $screen_id=ExamOrder::where('exam_id',$exam_id)->where('student_id',$student_id)->select('exam_screening_id')->first();//获取考试场次Id
        $exam_screen_id=$screen_id->exam_screening_id;
        $result = Watch::where('id', $id)->update(['status' => 1]);//修改腕表状态
        if ($result) {
            $action = '绑定';
            $updated_at =date('Y-m-d H:i:s',time());
            $data = array(
                'watch_id' => $id,
                'action' => $action,
                'context' => array('time' => $updated_at, 'status' => 1),
                'student_id' => $student_id
            );
            $watchModel = new WatchLog();
            $watchModel->historyRecord($data,$student_id,$exam_id,$exam_screen_id);//插入使用记录

            $examScreeningStudent   =   ExamScreeningStudent::where('student_id','=',$student_id)->where('exam_screening_id','=',$exam_screen_id)->first();
            if(is_null($examScreeningStudent))
            {
                if(!ExamScreeningStudent::create(['watch_id' => $id,'student_id'=>$student_id,'signin_dt'=>$updated_at,'exam_screening_id'=>$exam_screen_id,'is_signin'=>1]))
                {
                    throw new \Exception('签到失败');
                }
            }
            else
            {
                $examScreeningStudent-> is_end   =   0;
                $examScreeningStudent-> watch_id =   $id;
                $examScreeningStudent-> signin_dt =  $updated_at;
                if(!$examScreeningStudent->save())
                {
                    throw new \Exception('签到失败');
                }
            }
//            $ExamScreeingStudentId=ExamScreeningStudent::where('watch_id' ,'=',$id)->where('student_id','=',$student_id)->where('exam_screening_id','=',$exam_screen_id)->first();
//            if($ExamScreeingStudentId){
//                ExamScreeningStudent::where('watch_id' ,'=',$id)->where('student_id','=',$student_id)->update(['is_end'=>0]);
//            }else{
//                ExamScreeningStudent::create(['watch_id' => $id,'student_id'=>$student_id,'signin_dt'=>$updated_at,'exam_screening_id'=>$exam_screen_id,'is_signin'=>1]);//签到
//
//            }
            ExamOrder::where('exam_id',$exam_id)->where('student_id',$student_id)->update(['status'=>1]);//更改考生状态
            Exam::where('id',$exam_id)->update(['status'=>1]);//更改考试状态
            return \Response::json(array('code' => 1));
        } else {
            return \Response::json(array('code' => 0));
        }

    }

    /**
     *解除绑定腕表
     * @method GET 接口
     * @url /api/1.0/private/osce/watch/unwrap-watch
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
    public function getUnwrapWatch(Request $request)
    {
        $this->validate($request,[
            'code'      =>'required',//腕表设备编码
            'exam_id'   =>'required' //考试id
        ]);

        $code=$request->get('code');
        $exam_id=$request->get('exam_id');
        //开启事务
        $connection = \DB::connection('osce_mis');
        $connection ->beginTransaction();
        try{
            $id = Watch::where('code',$code)->select('id')->first()->id;    //获取腕表id
            $student_id = WatchLog::where('watch_id',$id)->where('action','绑定')->select('student_id')->orderBy('id','DESC')->first();//腕表使用记录查询学生id
            if(!$student_id){    //如果学生不存在
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    return \Response::json(array('code'=>2));       //该腕表绑定的学生不存在
                }else{
                    return \Response::json(array('code'=>0));       //解绑失败
                }
            }
            $student_id=$student_id->student_id;
            //获取学生信息
            $studentInfo = Student::where('id', $student_id)->select(['id','name','code as idnum','idcard'])->first();

            //获取学生的考试状态
            $student = new Student();
            $exameeStatus = $student->getExameeStatus($studentInfo->id,$exam_id);
            $status = $this->checkType($exameeStatus->status);

            $screen_id = ExamOrder::where('exam_id','=',$exam_id)->where('student_id','=',$student_id)->first();  //考试场次编号
            if(!$screen_id){
                $result = Watch::where('id',$id)->update(['status'=>0]);//解绑
                if($result){
                    $action = '解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    //将解绑记录添加到腕表使用历史表中
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);
                    return \Response::json([
                        'code' => 1,
                        'data' => [
                            'name'  => $studentInfo->name,
                            'idnum' => $studentInfo->idnum,
                            'idcard'=> $studentInfo->idcard,
                            'status'=> $status
                        ]
                    ]);   //解绑成功
                }else{
                    throw new \Exception('解绑失败');
                }
            }
            $exam_screen_id = $screen_id->exam_screening_id;
            $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $student_id)->count();
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($exam_id);
            if($ExamFinishStatus==$studentExamSum){ //如果考试流程结束
                ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>1]);//更改考试场次终止状态
                ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>2]);//更改考生排序状态
                $result = Watch::where('id',$id)->update(['status'=>0]);
                if($result){
                    $action='解绑';
                    $updated_at = date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel=new WatchLog();
                    $watchModel->unwrapRecord($data);


                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening  =  new ExamScreening();
                    $examScreening  -> getExamCheck();
                    $connection->commit();

                    return \Response::json([
                        'code' => 1,
                        'data' => [
                            'name'  => $studentInfo->name,
                            'idnum' => $studentInfo->idnum,
                            'idcard'=> $studentInfo->idcard,
                            'status'=> $status
                        ]
                    ]);
                }else{
                    throw new \Exception('解绑失败');
                }
            }

            //如果考试流程未结束 还是解绑,把考试排序的状态改为0
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                $action = '解绑';
                $result = ExamOrder::where('student_id',$student_id)->where('exam_id',$exam_id)->update(['status'=>0]);
                if($result){
                    $updated_at =date('Y-m-d H:i:s',time());
                    $data = array(
                        'watch_id'       =>$id,
                        'action'         =>$action,
                        'context'        =>array('time'=>$updated_at,'status'=>0),
                        'student_id'     =>$student_id,
                    );
                    $watchModel = new WatchLog();
                    $watchModel->unwrapRecord($data);
                    ExamScreeningStudent::where('watch_id',$id)->where('student_id',$student_id)->where('exam_screening_id',$exam_screen_id)->update(['is_end'=>2]);

                    //TODO:罗海华 2016-02-06 14:27     检查考试是否可以结束
                    $examScreening   =   new ExamScreening();
                    $examScreening  ->getExamCheck();
                    //检查考试是否可以结束
                    $connection->commit();
                }
                return \Response::json([
                    'code' => 1,
                    'data' => [
                        'name'  => $studentInfo->name,
                        'idnum' => $studentInfo->idnum,
                        'idcard'=> $studentInfo->idcard,
                        'status'=> $status
                    ]
                ]);
            }else{
                throw new \Exception('解绑失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            return \Response::json(array('code'=>0));
        }
    }

    /**
     *检测学生状态
     * @method GET
     * @url /api/1.0/private/osce/watch/student-details
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
            'id_card' => 'required',
            'exam_id' => 'required'
        ]);

        $idCard=$request->get('id_card');
        $examId=$request->get('exam_id');

        $studentInfo=Student::where('idcard',$idCard)->where('exam_id',$examId)->select(['id','idcard','code','exam_sequence'])->first();

        if(!$studentInfo){
            return response()->json(
                $this->success_rows(2,'未找到学生相关信息')
            );
        }
        //返回的数据
        $data = [
            'code'          => $studentInfo->code,           //学号
            'idcard'        => $studentInfo->idcard,         //身份证
            'exam_sequence' => $studentInfo->exam_sequence,  //准考证
        ];

        $action=WatchLog::where('student_id',$studentInfo->id)->select('action')->orderBy('id','DESC')->first();
        if($action){
            if($action->action=='绑定') {
                return response()->json(
                    $this->success_data($data, 1, '已绑定腕表'));
            }else{
                return response()->json(
                    $this->success_data($data,0,'未绑定腕表')
                );
            }
        }else{
            return response()->json(
                $this->success_data($data,0,'未绑定腕表')
            );
        }

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
            'code'                  =>  'required',
            'status'                =>  'required',
            'name'                  =>  'sometimes',
            'create_user_id'        =>  'required|integer',
            'description'           =>  'sometimes',
            'factory'               =>  'sometimes',
            'sp'                    =>  'sometimes',
            'purchase_dt'           =>  'sometimes',
            'nfc'                   =>  'sometimes',
        ]);

        $code=$request->get('code');
        $id=Watch::where('code',$code)->select()->first();
        if($id){
            return \Response::json(array('code'=>3));
        }
        $nfc=$request->get('nfc');
        $id=Watch::where('nfc_code',$nfc)->select()->first();
        if($id){
            return \Response::json(array('code'=>3));
        }

        try{
            $watch=Watch::create([
                'code'          =>  $request->get('code'),
                'name'          =>  $request->get('name',''),
                'status'        =>  $request->get('status',1),
                'description'   =>  $request->get('description',''),
                'factory'       =>  $request->get('factory',''),
                'sp'            =>  $request->get('sp',''),
                'create_user_id'=> $request->get('create_user_id'),
                'purchase_dt'   => $request->get('purchase_dt'),
                'nfc_code'      => $request->get('nfc',''),
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
     * * int		id			    设备id(必须的)
     * * int		user_id			操作人编号(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author limingyao <limingyao@misrobot.com>
     * @date 2016-01-12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDeleteWatch(Request $request)
    {
        $this->validate($request,[
            'code'              =>  'required',
            'create_user_id'    =>  'required|integer'
        ]);
        $code   = $request->get('code');
        $id     = Watch::where('code',$code)->select()->first()->id;
        $Log_id = WatchLog::where('watch_id',$id)->select()->get();//查询使用记录
        $screen_watch = ExamScreeningStudent::where('watch_id',$id)->select()->get();
        if(count($Log_id)>0 || count($screen_watch)>0 ){
            return \Response::json(array('code'=>10));
        }
        $result = Watch::where('id',$id)->delete();
        if($result){
            return \Response::json(array('code'=>1));
        }

        return response()->json(
            $this->fail(new \Exception('删除腕表失败'))
        );
    }



    /**
     * 更新腕表状态接口
     *
     * @api GET /api/1.0/private/osce/watch/update
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
            'code'                  =>  'required',
            'status'                =>  'required',
            'name'                  =>  'sometimes',
            'create_user_id'        =>  'required|integer',
            'description'           =>  'sometimes',
            'factory'               =>  'sometimes',
            'sp'                    =>  'sometimes',
            'purchase_dt'           =>  'sometimes',
            'nfc'                   =>  'sometimes',
        ]);

        $code=$request->get('code');
        $id=Watch::where('code',$code)->select()->first();
        $nfc=$request->get('nfc');
        if($nfc){
            $watch_id=Watch::where('nfc_code',$nfc)->select()->first();
            if($watch_id){
                if($id->id!=$watch_id->id){
                    return \Response::json(array('code'=>3));
                }
            }
        }
        $count=Watch::where('code'   ,'=', $code)
            ->update([
                'name'          =>  $request    ->  get('name'),
                'factory'       =>  $request    ->  get('factory'),
                'sp'            =>  $request    ->  get('sp'),
                'description'   =>  $request    ->  get('description'),
                'create_user_id'   =>  $request    ->  get('create_user_id'),
                'status'        =>  $request    ->  get('status'),
                'purchase_dt'   =>  $request    ->  get('purchase_dt'),
                'nfc_code'           =>  $request    ->  get('nfc',''),
            ]);

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
     *编辑返回设备信息
     * @method GET
     * @url /api/1.0/private/osce/watch/watch-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        code       设备编码(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchDetail(Request $request){
        $this->validate($request,[
            'code'  =>  'required'
        ]);

        try{
            $list=Watch::where('code',$request->get('code'))->select()->get();
            return response()->json(
                $this->success_data($list,1,'success')
            );}
        catch( \Exception $ex){
            return response()->json(
                $this->fail($ex)
            );
        }
    }


    /**
     *获取当日考试列表
     * @method GET
     * @url /api/1.0/private/osce/watch/exam-list
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
    public function getExamList()
    {
        $exam = new Exam();
        $status = 0;
        $examList = $exam->getTodayList($status);
        if(count($examList)){
            foreach($examList as $item){
                 if($item->status == 1){
                     $status = 1;
                     $examList = $exam->getTodayList($status);
                 }
            }
            return response()->json(
                $this->success_rows(1,'success',count($examList),$pagesize=1,count($examList),$examList)
            );
        }
        return \Response::json(array('code' => 4));
    }


    /**
     *查询腕表数据
     * @method GET
     * @url   /api/1.0/private/osce/watch/list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        code        设备编码(必须的)
     * * string        status      状态(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchList(Request $request)
    {
        $this->validate($request, [
            'code' => 'sometimes',
            'status' => 'sometimes',
        ]);

        $code = $request->get('code');
        $status = $request->get('status');
        try {
            $watchModel = new Watch();
            $list = $watchModel->getWatch($code, $status);
            $data = [];
            foreach ($list as $item) {
                $data[] = ['id' => $item->id,
                    'status' => $item->status,
                    'name' => $item->name,
                    'code' => $item->code,
                    'nfc' => $item->nfc_code,
                ];

            }
            $row = [];
            foreach ($data as $itm) {
                if ($itm['status'] == 1) {
                    $studentId = ExamScreeningStudent::where('watch_id', $itm['id'])->select('student_id')->orderBy('id','DESC')->first();
                    if (!$studentId) {
                        $row[] = [
                            'id' => $itm['id'],
                            'status' => $itm['status'],
                            'name' => $itm['name'],
                            'code' => $itm['code'],
                            'nfc' => $itm['nfc'],
                            'studentId' => '',
                        ];
                    } else {
                        $row[] = [
                            'id' => $itm['id'],
                            'status' => $itm['status'],
                            'name' => $itm['name'],
                            'code' => $itm['code'],
                            'nfc' => $itm['nfc'],
                            'studentId' => $studentId->student_id,
                        ];
                    }

                } else {
                    $row[] = [
                        'id' => $itm['id'],
                        'status' => $itm['status'],
                        'name' => $itm['name'],
                        'code' => $itm['code'],
                        'nfc' => $itm['nfc'],
                        'studentId' => '',
                    ];
                }
            }
            $list = [];

            foreach ($row as $v) {
                if ($v['studentId']) {
                    $studentName = Student::where('id', $v['studentId'])->select('name')->first()->name;
                    $list[] = [
                        'id' => $v['id'],
                        'status' => $v['status'],
                        'name' => $v['name'],
                        'code' => $v['code'],
                        'nfc' => $v['nfc'],
                        'studentName' => $studentName,
                    ];
                } else {
                    $list[] = [
                        'id' => $v['id'],
                        'status' => $v['status'],
                        'name' => $v['name'],
                        'code' => $v['code'],
                        'nfc' => $v['nfc'],
                        'studentName' => '-',
                    ];
                }

            }
            return response()->json(
                $this->success_data($list, 1, 'success')
            );
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }
    }

    /**
     *获取当前考试第一批学生
     * @method GET
     * @url /api/1.0/private/osce/watch/student-list
     * @access public
     *
     * @param Request $request get请求<br><br>
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

    public function getStudentList(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);
        $exam_id = $request->get('exam_id');
        //$screen_id = ExamScreening::where('exam_id', $exam_id)->where('status', 1)->orderBy('begin_dt')->first();
        $examScreeningModel =   new ExamScreening();
        $examScreening      =   $examScreeningModel ->  getExamingScreening($exam_id);
        var_dump($examScreening);echo "<br>";
        if(is_null($examScreening))
        {
            $examScreening  =   $examScreeningModel->getNearestScreening($exam_id);
        }
        dd($examScreening);
        if (!$examScreening) {
            return \Response::json(array('code' => 2));
        }
        $screen_id = $examScreening->id;
        $studentModel = new Student();
//        try {
            $screenModel = new ExamScreening();

//            $stations = $examDraftFlowModel->leftjoin('exam_draft', function ($join) {
//                $join->on('exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id');
//            })->where('exam_draft_flow.exam_id', '=', $exam_id)
//                ->where('exam_draft_flow.exam_screening_id', '=', $screen_id)
//                ->select('exam_draft.station_id')
//                ->get();
        //查找exam_screening
        $stations = $screenModel->where('exam_screening.exam_id','=',$exam_id)->leftjoin('exam_gradation', function ($join) {
                $join->on('exam_gradation.order', '=', 'exam_screening.gradation_order');
            })->leftjoin('exam_draft_flow', function ($join) {
                $join->on('exam_draft_flow.order', '=', 'exam_gradation.order');
            })->leftjoin('exam_draft', function ($join) {
                $join->on('exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id');
            })->groupBy('exam_screening.id')->get();
            /*
            $mode=Exam::where('id',$exam_id)->select('sequence_mode')->first()->sequence_mode;
            //$mode 为1 ，表示以考场分组， 为2，表示以考站分组 //TODO zhoufuxiang
            if($mode==1){
                $rooms=ExamFlowRoom::where('exam_id',$exam_id)->where('effected',1)->select('room_id')->get();
                $stations=RoomStation::whereIn('room_id',$rooms)->select('station_id')->get();

            } else{
                $stations = ExamFlowStation::where('exam_id', $exam_id)->where('effected',1)->select('station_id')->get();
            }
            */
            $countStation=[];
            foreach($stations as $item){
                $countStation[]=$item->station_id;
            }

            $countStation=array_unique($countStation);
            $batch=config('osce.batch_num');//默认为2
            $countStation=count($countStation)*$batch;//可以绑定的学生数量 考站数乘以倍数

            $list = $studentModel->getStudentQueue($exam_id, $screen_id,$countStation);//获取考生队列
        //dd($list);
            $data=[];
            foreach($list as $itm){
                $data[]=[
                    'name' => $itm->name,
                    'idcard' => $itm->idcard,
                    'code' => $itm->code,
                    'exam_screening_id' => $itm->exam_screening_id,
                ];
            }
            $count = count($list);
            return response()->json(
                $this->success_data($data, 1, 'count:'.$count)
            );

//            }elseif($mode==2){
//                $stations=ExamFlowStation::where('exam_id',$exam_id)->select('station_id')->get();
//                $countStation=[];
//                foreach($stations as $item){
//                    $countStation[]=$item->station_id;
//                }
//                $countStation=array_unique($countStation);
//                $batch=config('osce.batch_num');
//                $countStation=count($countStation)*$batch;
//                $list = $studentModel->getStudentQueue($exam_id, $screen_id,$countStation);
//                $data=[];
//                foreach($list as $itm){
//                    $data[]=[
//                        'name' => $itm->name,
//                        'idcard' => $itm->idcard,
//                        'code' => $itm->code,
//                        'exam_screening_id' => $itm->exam_screening_id,
//                    ];
//                }
//                $count = count($list);
//                return response()->json(
//                    $this->success_data($data, 1, 'count:'.$count)
//                );
//            }

//        } catch (\Exception $ex) {
//            return response()->json(
//                $this->fail($ex)
//            );
//        }
    }


    /**
     *插入缺考的学生
     * @method GET
     * @url /api/1.0/private/osce/watch/absent-student
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
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
    public function getAbsentStudent($studentId,$examId){
             $status=ExamOrder::where('student_id',$studentId)->where('exam_id',$examId)->select('status')->first()->status;
             if($status==4){
               $result=ExamOrder::where('student_id',$studentId)->where('exam_id',$examId)->update(['status'=>3]);
               if($result){
                   $screen_id=ExamScreening::where('exam_id',$examId)->where('status',1)->orderBy('begin_dt')->first()->id;
                   $result=ExamAbsent::create([
                       'student_id'  => $studentId,
                       'exam_id'     => $examId,
                       'exam_screening_id'  => $screen_id,
                   ]);
                   if($result){
                       //TODO zhoufuxiang
                       //获取该考试最后一位学生（按开始考试时间排序）, 若此学生与当前缺考学生是同一个，则将考试标为已结束
                       $examOrder = ExamOrder::where('exam_id', $examId)->select(['begin_dt', 'student_id'])->orderBy('begin_dt', 'DESC')->first();
                       if($examOrder->student_id == $studentId){
                           //检查考试是否可以结束
                           $examScreening   =   new ExamScreening();
                           $examScreening  ->getExamCheck();
                       }

                       return \Response::json(array('code'=>1));//缺考记录插入成功
                   }
                   return \Response::json(array('code'=>0));//缺考记录插入失败
               }
             }
    }

    /**
     *迟到学生处理
     * @method GET
     * @url /api/1.0/private/osce/watch/skip-last
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           exam_id        考试Id(必须的)
     * * string        id_card        身份证号(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSkipLast(Request $request){
        $this->validate($request,[
            'exam_id'  => 'required|integer',
            'id_card'  => 'required',
        ]);
        $exam_id=$request->get('exam_id');
        $idcard=$request->get('id_card');
        $studentId=Student::where('idcard',$idcard)->where('exam_id',$exam_id)->select('id')->first();
        if(!$studentId){
          return \Response::json(array('code'=>2));//未找到该学生
        }
        $studentId=$studentId->id;
        $screen_id=ExamOrder::where('student_id',$studentId)->where('exam_id',$exam_id)->select('exam_screening_id')->first()->exam_screening_id;
        $result=$this->changeSkip($studentId,$exam_id,$screen_id);
//        $examScreening=new ExamScreening();
//        $examScreening->closeExam($request->get('exam_id'));
        //TODO:zhouqiang 2016-04-01 14:27     检查考试是否可以结束
//        $examScreening  =  new ExamScreening();
//        $examScreening  -> getExamCheck();
        return $result;
    }

    /**
     *迟到或者缺考学生的处理
     * @method GET
     * @url
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        studentId        学生id(必须的)
     * * int        exam_id          考试id(必须的)
     * * int        screen_id        场次id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function changeSkip($studentId,$exam_id,$screen_id)
    {
        $status = ExamOrder::where('student_id', $studentId)->where('exam_id', $exam_id)->select('status')->first()->status; //查询学生当前状态
        if ($status == 4) {
            return $this->getAbsentStudent($studentId, $exam_id); //插入缺考记录 学生已缺考
        } elseif ($status == 2) {
            return \Response::json(array('code' => 3));//该学生考试已结束
        } elseif ($status == 1) {
            return \Response::json(array('code' => 4));//该学生正在考试
        }
        $beginDt = ExamOrder::where('exam_id', $exam_id)->where('exam_screening_id', $screen_id)->select('begin_dt')->orderBy('begin_dt', 'DESC')->first()->begin_dt;//获取最后一位学生的开始考试时间
        $lastDt = strtotime($beginDt) + 10;
        $time = date('Y-m-d H:i:s', $lastDt);
        $result = ExamOrder::where('exam_id', $exam_id)->where('student_id', $studentId)->update(['begin_dt' => $time, 'status' => 4]);//修改该学生考试开始时间
        if ($result) {
            return \Response::json(array('code' => 1));
        }
        return \Response::json(array('code' => 0));
    }

}