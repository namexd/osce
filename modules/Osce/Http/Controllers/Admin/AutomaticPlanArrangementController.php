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
    /**
     * 智能排考的着陆页
     * @param Request $request
     * @author Jiangzhiheng
     * @time 2016-02-22 18：01
     * @return \Illuminate\Http\JsonResponse
     */
    function getIndex(Request $request) {

        $this->validate($request,[
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');
        try {
            $automaticPlanArrangement = new AutomaticPlanArrangement($examId,new ExamPlaceEntity(),new Exam());
            /** @var 考试id $examId */
            return response()->json($this->success_data($automaticPlanArrangement->output($examId)));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }

    }

    /**
     * 开始排考
     * @author Jiangzhiheng
     * @time 2016-02-19 09:34
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function postBegin(Request $request) {
        $this->validate($request,[
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');

        try {
            $automaticPlanArrangement = new AutomaticPlanArrangement($examId,new ExamPlaceEntity(),new Exam());
//            dd($automaticPlanArrangement->plan($examId));
            return response()->json($this->success_data($automaticPlanArrangement->plan($examId)));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}