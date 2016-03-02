<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/15 0015
 * Time: 17:55
 */
namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Pad;
use Modules\Osce\Http\Controllers\CommonController;

class OsceTvController extends  CommonController{


    /**
     *候考轮播考生列表
     * @method GET
     * @url /osce/admin/oscetv/wait-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id       考试Id
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWaitDetail(Request $request){
          $this->validate($request,[
              'exam_id'  => 'required|integer'
          ]);
          $exam_id=$request->get('exam_id');
          $exams=Exam::where('id',$exam_id)->select()->first();
          $mode=Exam::where('id',$exam_id)->select('sequence_mode')->first()->sequence_mode;
          $examQueModel= new ExamQueue();
          $list=$examQueModel->getStudent($mode,$exam_id);

          return view('osce::admin.examManage.exam_remind')->with(['list'=>$list,'exams'=>$exams]);
//        return view('osce::admin.examManage.exam_remind');
    }

}