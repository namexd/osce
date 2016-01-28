<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/18
 * Time: 15:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Http\Controllers\CommonController;


class IndexController extends CommonController
{
    public function dashboard(){
        $exam   =   new Exam();
        $data   =   $exam->selectExamToday();
        if(count($data) > 0){
            return view('osce::admin.index.examboard',['data'=>$data]);
        }else{
            return view('osce::admin.index.dashboard');
        }
    }

    /**
     * 设置开考
     * @api GET /osce/admin/indwx/set-exam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        考试ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSetExam(Request $request, Exam $exam)
    {
        try{
            $this->validate($request,[
                'id'    => 'required|integer'
            ],[
                'id.required'   => '没有考试ID',
                'id.integer'   => 'ID必须为数字'
            ]);
            //获取考试ID
            $exam_id = $request->get('id');
            $exam = Exam::find($exam_id);
            if(is_null($exam)){
                throw new \Exception('没有找到相关考试');
            }
            $exam->status = 1;

            if($exam->save()){
                return redirect()->route('osce.admin.index.dashboard');
            }else{
                throw new \Exception('开考失败！');
            }
        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}