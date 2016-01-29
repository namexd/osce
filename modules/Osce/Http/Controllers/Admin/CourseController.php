<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/28
 * Time: 17:33
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Http\Controllers\CommonController;

class CourseController extends CommonController
{
    /**
     * @param Request $request
     * @author Jiangzhiheng
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        try {
            //验证
            $this->validate($request,[
                'exam_id' => 'sometimes|integer',
                'subject_id' => 'sometimes|integer',
            ]);

            $examId = $request->input('exam_id');
            $subjectId = $request->input('subject_id');

            //科目列表数据
            $subject = new Subject();
            $subjectData = $subject->CourseControllerIndex($examId,$subjectId);

            foreach ($subjectData as &$item) {
                //找到按科目为基础的所有分数还有总人数
                $avg =$subject->CourseControllerAvg(
                    $item->exam_id,
                    $item->subject_id
                );
                //如果不为空avg不为空
                if (!empty($avg)) {
                    if ($avg->pluck('score')->count() != 0 || $avg->pluck('time')->count() != 0) {
                        $item->avg_score = $avg->pluck('score')->sum()/$avg->pluck('score')->count();
                        $item->avg_time = $avg->pluck('time')->sum()/$avg->pluck('time')->count();
                        $item->avg_total = $avg->count();
                    } else {
                        $item->avg_score = 0;
                        $item->avg_time = 0;
                        $item->avg_total = $avg->count();
                    }
                }
            }
            return view('osce::admin.statistics_query.subject_scores_list',['data'=>$subjectData]);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
//            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}