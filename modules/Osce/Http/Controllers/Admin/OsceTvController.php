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
     *∫Úøº¬÷≤•øº…˙¡–±Ì
     * @method GET
     * @url /osce/admin/oscetv/write-detail
     * @access public
     *
     * @param Request $request post«Î«Û<br><br>
     * <b>post«Î«Û◊÷∂Œ£∫</b>
     * * int        exam_id       øº ‘Id
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function getWriteDetail(Request $request){
          $this->validate($request,[
              'exam_id'  => 'required|integer'
          ]);
          $exam_id=$request->get('exam_id');
          $description=Exam::where('id',$exam_id)->select('description')->first()->description;
          $mode=Exam::where('id',$exam_id)->select('sequence_mode')->first()->sequence_mode;
          $examQueModel= new ExamQueue();
          $list=$examQueModel->getStudent($mode,$exam_id);

          return view()->with(['list'=>$list,'description'=>$description]);
      }

}