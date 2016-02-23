<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/19
 * Time: 9:32
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\AutomaticPlanArrangement\AutomaticPlanArrangement;
use Modules\Osce\Entities\AutomaticPlanArrangement\ExamPlaceEntity;
use Modules\Osce\Entities\AutomaticPlanArrangement\Exam;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

class AutomaticPlanArrangementController extends CommonController
{

    function getIndex(Request $request) {
        //todo 着陆页，先不忙
        $this->validate($request,[
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');

        $automaticPlanArrangement = new AutomaticPlanArrangement($examId,new ExamPlaceEntity(),new Exam());
        $automaticPlanArrangement->output($examId);
    }

    /**
     * 开始排考
     * @author Jiangzhiheng
     * @time 2016-02-19 09:34
     * @param Request $request
     */
    function postBegin(Request $request) {
        $this->validate($request,[
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');

        $automaticPlanArrangement = new AutomaticPlanArrangement($examId,new ExamPlaceEntity(),new Exam());
        $automaticPlanArrangement->plan($examId);
    }
}