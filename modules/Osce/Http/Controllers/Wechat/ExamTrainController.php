<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/11 0011
 * Time: 15:31
 */

namespace Modules\Osce\Http\Controllers\Wechat;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Modules\Osce\Entities\InformTrain;
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
    public function  getExamTrainingIndex(Request $request, InformTrain $train)
    {

//        $user=Auth::user();
//        $userId=$user->id;
//        if(!$userId){
//            return response()->json(
//                $this->success_rows(0,'false')
//            );
//        }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get()->toArray();
        return response()->json(
            $this->success_rows(1,'success',$pagination->total(),config('osce.page_size'),$pagination->currentPage(),$list)
        );
    }


    //附件上传
    public function postExamTrainingUpload()
    {




    }


    //附件点击下载
    public function getTrainDownload()
    {


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

    public function postAddTraining(Request $request, InformTrain $train)
    {
        dd(1111111);
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'begin_dt' => 'required',
            'end_dt' => 'required',
            'teacher' => 'required',
            'content' => 'required',
            'attachments' => 'required',
            'create_user_id' => 'required',
            'status' => 'required'
        ]);

        $data = [
            'name' => Input::get('name'),
            'address' => Input::get('address'),
            'begin_dt' => Input::get('begin_dt'),
            'end_dt' => Input::get('end_dt'),
            'teacher' => Input::get('teacher'),
            'content' => Input::get('content'),
            'create_user_id' => Input::get('create_user_id'),
        ];
        $attachments  = Input::get('attachments');
        $data['attachments']=serialize($attachments);
        $add = InformTrain::create($data);
        if ($add != false) {

            return redirect()->back()->withInput()->withErrors('添加成功');
        } else {
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


    public function getDeleteTraining(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = intval($request->get('id'));
        $user = Auth::user();
        $userId = $user->id;
        $creteId = InformTrain::where('id', $id)->select()->first()->create_user_id;
        $manager = config('osce.manager');
        if ($userId !== $creteId || $creteId !== $manager[0]) {
            return response()->json(
                $this->success_rows(3, 'false')
            );
        }
        $result = InformTrain::where('id', $id)->delete();
        if ($result) {
            return response()->json(
                $this->success_rows(1, 'success')
            );
        }
        return response()->json(
            $this->success_rows(0, 'false')
        );

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
        $id = urlencode(e(Input::get('id')));

        if($id){
            $data = DB::connection('osce_mis')->table('inform_training')->where('id','=',$id)->get()->toArray();
            dd($data);
            $list =[
                'name' =>$data['name'],
                'address' =>$data['address'],
                'begin_dt' =>$data['begin_dt'],
                'end_dt' =>$data['end_dt'],
                'teacher' =>$data['teacher'],
                'content' =>$data['content'],
                'attachments' =>$data['attachments'],
            ];
        }else{
            throw new \Exception('查看失败！请重试');
//            return redirect()->back()->withInput()->withErrors('系统异常');
        }


        dd($list);
           die(json_encode($list));
    }

    /**
     *考前培训编辑
     * @api GET /osce/wechat/examtrain/edit-training
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
     public function getEditTraining(Request $request){
         $id = urlencode(e(Input::get('id')));
         if($id){

         }


     }


}