<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:30
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Station;
use Modules\Osce\Http\Controllers\CommonController;

class ExamController extends CommonController
{
    /**
     * 获取考试列表
     * @api       GET /osce/admin/exam/exam-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(Request $request, Exam $exam)
    {
        //验证略

        //从模型得到数据
        $data = $exam->showExamList();

        return view('osce::admin.exammanage.exam_assignment', ['data' => $data]);

    }

    /**
     * 删除考试
     * @api       POST /osce/admin/exam/delete
     * @access    public
     * @param Request $request post请求<br><br>
     *                         <b>post请求字段：</b>
     * id 考试id
     * @param Exam $exam
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelete(Request $request, Exam $exam)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            //获取id
            $id = $request->input('id');

            //进入模型逻辑
            $result = $exam->deleteData($id);

            if ($result !== true) {
                throw new \Exception('删除考试失败，请重试！');
            } else {
                return redirect()->route('osce.admin.exam.getExamList');
            }

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex);
        }
    }

    /**
     * 通过考试的id获取考站
     * @api       GET /osce/admin/exam/station-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                                      id  考试id
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationList(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //$id为考试id
        $id = $request->input('id');

        //如果在考试考场表里查不到信息的话，就说明还没有选择跳到上一个选项卡去
        $result = ExamScreening::where('exam_screening.exam_id','=',$id)->first();
        if (!$result) {
            return redirect()->route('')->withErrors('对不起，请选择房间');
        }

        //得到room_id
        $roomId = $result->room_id;

        //根据room_id得到考站列表
        $data = Station::where('station.room_id' , '=' , $roomId)
            ->select([
                'station.id as id',
                'station.name as name'
            ])->get();

        return view('osce::admin.exammanage.sp_invitation', ['data' => $data]);
    }

    /**
     * 获取考试列表 接口 （带翻页）
     * @api GET /osce/admin/invigilator/exam-list-data
     * @access public
     *
     * @return json {id:考试ID,name:考试名称}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getExamListData(){
        $exam   = new Exam();
        $pagination = $exam->showExamList();

        $data   =   $pagination->toArray();
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),config('msc.page_size'),$pagination->currentPage(),$data['data'])
        );
    }
}