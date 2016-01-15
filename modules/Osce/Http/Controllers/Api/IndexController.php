<?php

namespace Modules\Osce\Http\Controllers\Api;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamScreening;
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
               return \Response::json(array('code' => 3));
           } else {
               $id=$id->id;
               $status = Watch::where('id', $id)->select()->first()->status;
               $student_id = ExamScreeningStudent::where('watch_id', $id)->select()->first();
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
                   return response()->json(
                       $this->success_data($data,1, '该腕表已绑定')
                   );
               } elseif ($status == 0) {
                   if(!$student_id){
                       $data=array('student_id'=>'','status'=>$status);
                       return response()->json(
                           $this->success_data($data,0, '未绑定')
                       );
                   }
                   $code = Student::where('id', $student_id->id)->select('code')->first();
                   if(!$code){
                       $data = array('code' => '','status'=>$status,'student_id'=>$student_id->id);
                   }else{
                       $data = array('code' => $code,'status'=>$status,'student_id'=>$student_id->id);
                   }
                   return response()->json(
                       $this->success_data($data, 0, '未绑定')
                   );
               } else {
                   if(!$student_id){
                       $data=array('student_id'=>'','status'=>$status);
                       return response()->json(
                           $this->success_data($data,2, '该腕表已损坏')
                       );
                   }
                   $code = Student::where('id', $student_id->id)->select('code')->first();
                   if(!$code){
                       $data = array('code' => '','status'=>$status,'student_id'=>$student_id->id);
                   }else{
                       $data = array('code' => $code,'status'=>$status,'student_id'=>$student_id->id);
                   }
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
            'code'      =>'required|integer',
            'id_card' =>'required',
            'exam_id' =>'required'
        ]);
        $code=$request->get('code');
        $id_card=$request->get('id_card');
        $exam_id=$request->get('exam_id');
        $id=Watch::where('code',$code)->select('id')->first()->id;
        $student_id=Student::where('idcard',$id_card)->select()->first();
        if(!$student_id){
            return \Response::json(array('code' => 3));
        }
        $student_id=$student_id->id;
        $screen_id=ExamScreening::where('exam_id',$exam_id)->select('id')->get();
        $exam_screen_id=ExamScreeningStudent::whereIn('exam_screening_id',$screen_id)->where('student_id',$student_id)->select()->first();
        if(!$exam_screen_id){
            return \Response::json(array('code' =>4));
        }
        $exam_screen_id=$exam_screen_id->id;
        if($exam_screen_id) {
            $result = ExamScreeningStudent::where('id', $exam_screen_id)->where('student_id', $student_id)->update(['watch_id' => $id]);

            if (!$result) {
                return \Response::json(array('code' => 2));
            }
            $result = Watch::where('id', $id)->update(['status' => 1]);
            if ($result) {
                $action = '绑定';
                $updated_at = ExamScreeningStudent::where('watch_id', $id)->select()->orderBy('updated_at', 'DESC')->first()->updated_at;
                $data = array(
                    'watch_id' => $id,
                    'action' => $action,
                    'context' => array('time' => $updated_at, 'status' => 1),
                    'student_id' => $student_id,
                );
                $watchModel = new WatchLog();
                $watchModel->historyRecord($data);
                return \Response::json(array('code' => 1));
            } else {
                return \Response::json(array('code' => 0));
            }
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
    public function getUnwrapWatch(Request $request){
        $this->validate($request,[
            'code' =>'required|integer'
        ]);
        $code=$request->get('code');
        $id=Watch::where('code',$code)->select('id')->first()->id;
        $student_id=ExamScreeningStudent::where('watch_id',$id)->select('student_id')->first();
        if(!$student_id){
            $result=Watch::where('id',$id)->update(['status'=>0]);
            if($result){
                return \Response::json(array('code'=>2));
            }else{
                return \Response::json(array('code'=>0));
            }
        }
        $student_id=$student_id->student_id;
        $result=Watch::where('id',$id)->update(['status'=>0]);
        if($result){
            $action='解绑';
            $updated_at=ExamScreeningStudent::where('watch_id',$id)->select('updated_at')->first()->updated_at;
                $data=array(
                    'watch_id'       =>$id,
                    'action'         =>$action,
                    'context'        =>array('time'=>$updated_at,'status'=>0),
                    'student_id'     =>$student_id,
                );
                $watchModel=new WatchLog();
                $watchModel->historyRecord($data);
            return \Response::json(array('code'=>1));
        }else{
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

        $watch_id=ExamScreeningStudent::where('student_id',$student_id)->select('watch_id')->first();

        if($watch_id){
            $watch_id=$watch_id->watch_id;
            $status=Watch::where('id',$watch_id)->select('status')->first()->status;

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
            'code'                  =>  'required',
            'status'                =>  'required',
            'created_user_id'       =>  'required|integer',
            'description'           =>  'sometimes',
            'factory'               =>  'sometimes',
            'sp'                    =>  'sometimes',
            'purchase_dt'           =>  'sometimes',
        ]);

        try{
            $watch=Watch::create([
                'code'          =>  $request->get('code'),
                'name'          =>  $request->get('name',''),
                'status'        =>  $request->get('status',1),
                'description'   =>  $request->get('description',''),
                'factory'       =>  $request->get('factory',''),
                'sp'            =>  $request->get('sp',''),
                'created_user_id'=> $request->get('created_user_id'),
                'purchase_dt'   => $request->get('purchase_dt'),
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
            'code'                    =>  'required|integer',
            'created_user_id'       =>  'required|integer'
        ]);

        $count=Watch::where('code',$request->get('code'))->delete();

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
            'created_user_id'       =>  'required|integer',
            'description'           =>  'sometimes',
            'factory'               =>  'sometimes',
            'sp'                    =>  'sometimes',
            'purchase_dt'           =>  'sometimes',
        ]);


        $count=Watch::where('code'   ,'=', $request->get('code'))
            ->update([
                'name'          =>  $request->get('name',''),
                'status'        =>  $request->get('status'),
                'description'   =>  $request->get('description'),
                'factory'       =>  $request->get('factory'),
                'sp'            =>  $request->get('sp'),
                'created_user_id'=> $request->get('created_user_id'),
                'purchase_dt'   => $request->get('purchase_dt'),
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
     *
     * @method GET
     * @url /api/1.0/private/osce/watch/watch-detail
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


    /**
     *查询腕表数据
     * @method GET
     * @url   /api/1.0/private/osce/watch/list
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
    public function getWatchList(Request $request)
    {
        $this->validate($request, [
            'code' => 'sometimes',
            'status' => 'sometimes|integer',
        ]);

        $code = intval($request->get('code'));
        $status = intval($request->get('status'));
        try{
            $watchModel=new Watch();
            $list=$watchModel->getWatch($code,$status);

            $data = [];
            foreach ($list as $item) {
                $data[] = ['id' => $item->id,
                    'status' => $item->status,
                    'name' => $item->name,
                    'code' => $item->code,
                ];

            }
            $row = [];
            foreach ($data as $itm) {
                if ($itm['status'] == 1) {
                    $studentId = ExamScreeningStudent::where('id', $itm['id'])->select('student_id')->first();
                    if (!$studentId) {
                        $row[] = [
                            'id' => $itm['id'],
                            'status' => $itm['status'],
                            'name' => $itm['name'],
                            'code' => $itm['name'],
                            'studentId' => '',
                        ];
                    } else {
                        $row[] = [
                            'id' => $itm['id'],
                            'status' => $itm['status'],
                            'name' => $itm['name'],
                            'code' => $itm['code'],
                            'studentId' => $studentId->student_id,
                        ];
                    }

                } else {
                    $row[] = [
                        'id' => $itm['id'],
                        'status' => $itm['status'],
                        'name' => $itm['name'],
                        'code' => $itm['code'],
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
                        'studentName' => $studentName,
                    ];
                } else {
                    $list[] = [
                        'id' => $v['id'],
                        'status' => $v['status'],
                        'name' => $v['name'],
                        'code' => $v['code'],
                        'studentName' => '-',
                    ];
                }

            }
            return response()->json(
                $this->success_data($list, 1, 'success')
            );
        } catch( \Exception $ex){
            return response()->json(
                $this->fail($ex)
            );
        };

    }
}