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
use Modules\Osce\Entities\InformTrain;
use Modules\Osce\Entities\Train;
use Modules\Osce\Http\Controllers\CommonController;
use DB;

class ExamTrainController extends CommonController
{


    /**
     *考前培训列表
     * @api GET /osce/wechat/examtrain/exam-training-index
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
     *
     * 'name', 'address','begin_dt','end_dt','teacher','content','attachments','status','create_user_id'
     */
        public  function  getExamTrainingIndex( Request $request,InformTrain $train){
            $message= [];
            $informModel= new InformTrain();
                $list = $informModel->getInformList();
                dd($list);
                foreach($list as $data){
                    $message['name'] = $data['name'];
                    $message['address'] = $data['address'];
                    $message['begin_dt'] = $data['begin_dt'];
                    $message['end_dt'] = $data['end_dt'];
                }
                dd($message);
//            }else{
//                throw new \Exception('请重新登陆！！！！');
//            }
//            return  view();
        }


    /**
     *考前培训添加
     * @api GET /osce/wechat/examtrain/add-training
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
        dd(1111111);
            $this->validate($request,[
                'name' => 'required',
                'address' => 'required',
                'begin_dt' => 'required',
                'end_dt' => 'required',
                'teacher' => 'required',
                'content' => 'required',
                'attachments' =>'required',
                'status' =>'required'
            ]);
             


        $data=[
            'name'=> Input::get('name'),
            'address'=> Input::get('address'),
            'begin_dt'=> Input::get('begin_dt'),
            'end_dt'=> Input::get('end_dt'),
            'teacher'=> Input::get('teacher'),
            'content'=> Input::get('content'),
            'attachments'=> Input::get('attachments'),
        ];

        $add=InformTrain::create($data);
        if($add != fasle){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     *考前培训删除
     * @api GET /osce/wechat/examtrain/delete-training
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
          dd(111111);
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
     * @api GET /osce/wechat/examtrain/see-training
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
        dd(11111);
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('osce_mis')->table('train')->where('id','=',$id)->first()->toArray();
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
        $list =[
            'name' =>$data['name'],
            'address' =>$data['address'],
            'begin_dt' =>$data['begin_dt'],
            'end_dt' =>$data['end_dt'],
            'teacher' =>$data['teacher'],
            'content' =>$data['content'],
            'attachments' =>$data['attachments'],
        ];
           die(json_encode($list));
    }

}