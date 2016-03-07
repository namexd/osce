<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;


use App\Entities\User;
use Cache;
use Illuminate\Http\Request;
use Modules\Osce\Entities\QuestionBankEntities\ExaminationPaper;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
class ExaminationPaperController extends CommonController
{
    /**
     * 获取考试列表
     * @url       GET /osce/admin/exam/exam-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(Request $request, Exam $exam)
    {
        //验证略
        $this->validate($request,[
            'exam_name' =>'sometimes'
        ]);

        $formData = $request->only('exam_name');

        //从模型得到数据
        $data = $exam->showExamList($formData);

        //得到考试组成
        foreach ($data as &$item) {
            $item->constitute = $this->getExamConstitute($item['id']);
        }

        return view('osce::admin.exammanage.exam_assignment', ['data' => $data]);

    }


}