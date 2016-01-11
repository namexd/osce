<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/11 0011
 * Time: 15:31
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Modules\Osce\Entities\Train;
use Modules\Osce\Http\Controllers\CommonController;
use DB;

class ExamTrainController extends CommonController
{


    /**
     *考前培训列表
     * @api GET /osce/admin/examtrain/exam-training-index
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
        public  function  getExamTrainingIndex( Request $request,Train $train){
            $id = urlencode(e(Input::get('id')));
            $message= [];
            if($id){
                $user= DB::connection('sys_mis')->table('users')->where('id','=',$id)->select('name')->get();
                $message['user_name'] = $user->name;
                $list = $train->get()->toArray();
                foreach($list as $data){
                    $message['name'] = $data['name'];
                    $message['place'] = $data['place'];
                    $message['begin_dt'] = $data['begin_dt'];
                    $message['end_dt'] = $data['end_dt'];
                }
            }else{
                throw new \Exception('请重新登陆！！！！');
            }
            return  view();
        }


    /**
     *考前培训添加
     * @api GET /osce/admin/examtrain/add-training
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public  function postAddTraining(Request $request ,Train $train){
            $this->validate($request,[
                'name' => 'required',
                'place' => 'required',
                'begin_dt' => 'required',
                'end_dt' => 'required',
                'teacher_name' => 'required',
                'remark' => 'required',
                'accessory' =>'required'
            ]);
        $data=[
            'name'=> Input::get('name'),
            'place'=> Input::get('place'),
            'begin_dt'=> Input::get('begin_dt'),
            'end_dt'=> Input::get('end_dt'),
            'teacher_name'=> Input::get('teacher_name'),
            'remark'=> Input::get('remark'),
            'accessory'=> Input::get('accessory'),
        ];

        $add=Train::create();
        if($data != fasle){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     *考前培训删除
     * @api GET /osce/admin/examtrain/delete-training
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


      public  function getDeleteTraining(Request $request){
          $id = urlencode(e(Input::get('id')));
          if($id){
              $data = DB::connection('osce_mis')->table('train')->where('id','=',$id)->delete();
              if($data != fasle){
                  return redirect()->back()->withInput()->withErrors('删除成功');
              }else{
                  return redirect()->back()->withInput()->withErrors('系统异常');
              }
          }else{
              return redirect()->back()->withInput()->withErrors('系统异常');
          }

      }


    /**
     *考前培训查看
     * @api GET /osce/admin/examtrain/see-training
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        教师id(必须的)
     * @return   view
     ** @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function   getSeeTraining(Request $request){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('osce_mis')->table('train')->where('id','=',$id)->first()->toArray();
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
        $list =[
            'name' =>$data['name'],
            'place' =>$data['place'],
            'begin_dt' =>$data['begin_dt'],
            'end_dt' =>$data['end_dt'],
            'teacher_name' =>$data['teacher_name'],
            'remark' =>$data['remark'],
            'accessory' =>$data['accessory'],
        ];
           die(json_encode($list));
    }

}