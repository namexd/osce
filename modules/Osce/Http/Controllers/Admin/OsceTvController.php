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

    public function postWaitDetail(Request $request)
    {
        try{
            $this->validate($request,[
                'exam_id'   => 'required|integer',
                'page'      => 'sometimes|integer'
            ]);
            $exam_id = $request->get('exam_id');
            $page    = $request->get('page');
            $pageSize= 4;
            $exam    = Exam::where('id', '=', $exam_id)->select('sequence_mode')->first();
            if($exam){
                $mode = $exam->sequence_mode;
            } else{
                throw new \Exception('没找到当前考试！');
            }
            $ExamQueue  = new ExamQueue();
            $pagination = $ExamQueue->getPageSize($exam_id, $pageSize);
            //根据排序方式 获取数据
            if ($mode == 1) {
                $students   = $ExamQueue->getWaitRoomStudents($exam_id, $pageSize);
            } elseif ($mode == 2) {
                $students   = $ExamQueue->getWaitStationStudents($exam_id, $pageSize);
            }

            return response()->json(
                $this->success_data(
                    [
                        'rows'      => $students,
                        'total'     => ceil($pagination->total()/$pageSize),
                        'page_size' => $pageSize,
                        'page'      => $pagination->currentPage()
                    ],
                    1, '获取数据成功'
                )
            );

        } catch(\Exception $ex){
            return response()->json($this->fail($ex));
        }
    }


}