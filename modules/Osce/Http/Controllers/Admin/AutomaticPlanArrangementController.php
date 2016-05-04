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
use Modules\Osce\Entities\SmartArrange\Export\StudentArrange;
use Modules\Osce\Entities\SmartArrange\Export\UserListExport;
use Modules\Osce\Entities\SmartArrange\SmartArrange;
use Modules\Osce\Entities\SmartArrange\SmartArrangeRepository;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Auth;
use Modules\Osce\Repositories\Common;
use Response;

class AutomaticPlanArrangementController extends CommonController
{
    /*
     * 保存请求对象的实例
     */
    private $request;

    /**
     * AutomaticPlanArrangementController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * TODO 此方法暂时没启用
     * @url
     * @access public
     *
     * @param Request $request
     *  请求字段：
     *
     * @return mixed
     *
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
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
     * 考试排考
     * @url osce/admin/arrangement/begin
     * @access public
     * @param SmartArrangeRepository $smartArrangeRepository
     * 请求字段：
     * 考试id exam_id
     * @return response
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-01
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    function postBegin(SmartArrangeRepository $smartArrangeRepository)
    {
        $this->validate($this->request, [
            'exam_id' => 'required|integer'
        ]);
        
        $examId = $this->request->input('exam_id');
        
        try {
            set_time_limit(0);
            $exam = \Modules\Osce\Entities\Exam::doingExam($examId);
            Common::valueIsNull($exam, -999, '当前的考试错误');

            return response()->json($this->success_data($smartArrangeRepository->plan($exam)));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 保存排考
     * @url osce/admin/arrangement/store
     * @access public
     * @param Request $request
     * @param SmartArrangeRepository $smartArrangeRepository
     * 请求字段：
     * 考试id exam_id
     * @return response
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-02-23 17:30
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    function postStore(SmartArrangeRepository $smartArrangeRepository)
    {
        $this->validate($this->request, [
            'exam_id' => 'required|integer'
        ]);

        $examId = $this->request->input('exam_id');

        $exam = \Modules\Osce\Entities\Exam::doingExam($examId);

        ExamPlan::where('exam_id', $examId)->delete();
        try {
            $smartArrangeRepository->store($exam);

            return redirect()->route('osce.admin.exam.getIntelligence', ['id' => $examId]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     *
     * @url osce/admin/arrangement/export
     * @access public
     * @param SmartArrangeRepository $smartArrangeRepository
     * @param UserListExport $export
     * @param StudentArrange $arrange
     * 请求字段：
     * 考试id exam_id
     * @return bool
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-01 16：48
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExport(SmartArrangeRepository $smartArrangeRepository, UserListExport $export, StudentArrange $arrange)
    {
        $this->validate($this->request, [
            'exam_id' => 'required|integer'
        ]);

        try {
            $id = $this->request->input('exam_id', null);
            Common::valueIsNull($id, 501, '没有考试');
            $smartArrangeRepository->export($id, $export, $arrange);
            return true;
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}