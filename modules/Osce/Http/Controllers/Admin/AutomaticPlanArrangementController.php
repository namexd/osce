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
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\SmartArrange\SmartArrange;
use Modules\Osce\Entities\SmartArrange\SmartArrangeRepository;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Auth;
use Modules\Osce\Repositories\Common;
use Illuminate\Container\Container as App;

class AutomaticPlanArrangementController extends CommonController
{
    /**
     * TODO 此方法暂时未使用
     * 智能排考的着陆页
     * @param Request $request
     * @author Jiangzhiheng
     * @time 2016-02-22 18：01
     * @return \Illuminate\Http\JsonResponse
     */
    function getIndex(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);
        $examId = $request->input('exam_id');
//        try {
//            $automaticPlanArrangement = new AutomaticPlanArrangement($examId, new ExamPlaceEntity(), new Exam());
//
//            return response()->json($this->success_data($automaticPlanArrangement->output($examId)));
//        } catch (\Exception $ex) {
//            return response()->json($this->fail($ex));
//        }
        try {
            $exam = \Modules\Osce\Entities\Exam::doingExam($examId);
            $smartArrange = new SmartArrangeRepository();

            return response()->json($this->success_data($smartArrange->output($exam)));
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
    function postBegin(Request $request, App $app)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');
//        try {
//            $automaticPlanArrangement = new AutomaticPlanArrangement($examId, new ExamPlaceEntity(), new Exam());
//            return response()->json($this->success_data($automaticPlanArrangement->plan($examId)));
//        } catch (\Exception $ex) {
//            return response()->json($this->fail($ex));
//        }
//        try {
            set_time_limit(0);
            $exam = \Modules\Osce\Entities\Exam::doingExam($examId);
            Common::valueIsNull($exam, -999, '当前的考试错误');
            $smartArrangeRepository = new SmartArrangeRepository($app);

            return response()->json($this->success_data($smartArrangeRepository->plan($exam)));
//        } catch (\Exception $ex) {
//            return response()->json($this->fail($ex));
//        }
    }

    /**
     * 智能排考的保存
     * @param Request $request request的实例
     * @param ExamPlan $examPlan examPlan的实例
     * @return $this
     * @author Jiangzhiheng
     * @time 2016-02-23 17:30
     */
    function postStore(Request $request, SmartArrangeRepository $smartArrangeRepository)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);
        $examId = $request->input('exam_id');
        $exam = \Modules\Osce\Entities\Exam::doingExam($examId);

        ExamPlan::where('exam_id', $examId)->delete();
        try {
            $smartArrangeRepository->store($exam);
        //获取操作者
//        $user = Auth::user();



//            $examPlan->storePlan($examId, $user);

            return redirect()->route('osce.admin.exam.getIntelligence', ['id' => $examId]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}