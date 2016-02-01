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
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Http\Controllers\CommonController;
use Cache;

class CourseController extends CommonController
{
    /**
     * 科目统计的主页
     * @param Request $request
     * @author Jiangzhiheng
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'exam_id' => 'sometimes|integer',
                'subject_id' => 'sometimes|integer',
            ]);

            $examId = $request->input('exam_id');
            $subjectId = $request->input('subject_id');

            //考试的下拉菜单
            $examDownlist = Exam::select('id', 'name')->where('exam.status','<>',0)->orderBy('begin_dt', 'desc')->get();

            //TODO:科目的下拉菜单 已经废弃，改用ajax

            //科目列表数据
            $subject = new Subject();
            $exam = new Exam();
            $subjectData = $exam->CourseControllerIndex($examId, $subjectId);
//            dd($subjectData);
            foreach ($subjectData as &$item) {
                //找到按科目为基础的所有分数还有总人数
                $avg = $subject->CourseControllerAvg(
                    $item->exam_id,
                    $item->subject_id
                );
                //如果avg不为空
                if (!empty($avg)) {
                    if ($avg->pluck('score')->count() != 0 || $avg->pluck('time')->count() != 0) {
                        $item->avg_score = $avg->pluck('score')->sum()/$avg->pluck('score')->count();
                        date_default_timezone_set("UTC");
                        $item->avg_time = date('H:i:s',$avg->pluck('time')->sum()/$avg->pluck('time')->count());
                        date_default_timezone_set("PRC");
                        $item->avg_total = $avg->count();
                    } else {
                        $item->avg_score = 0;
                        $item->avg_time = 0;
                        $item->avg_total = $avg->count();
                    }
                }
            }

            return view('osce::admin.statistics_query.subject_scores_list',
                ['data'=>$subjectData,
                    'examDownlist'=>$examDownlist,
                    'exam_id'=>$examId,
                    'subject_id'=>$subjectId
                ]);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
//            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 科目管理-学生的主页
     * @param Request $request
     * @author Jiangzhiheng
     * @return \Illuminate\View\View
     */
    public function getStudent(Request $request)
    {
        //验证
        $this->validate($request, [
            'exam_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'exam' => 'required',
            'subject' => 'required',
            'avg_score' => 'required',
            'avg_time' => 'required'
        ]);

        //获得参数
        $examId = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        $exam = $request->input('exam');
        $subject = $request->input('subject');
        $avgScore = $request->input('avg_score');
        $avgTime = $request->input('avg_time');
        $data = Student::getStudentByExamAndSubject($examId, $subjectId);
        //将排名的数组循环插入表中
        foreach ($data as $key => &$item) {
            $item->ranking = $key + 1;
        }

        return view('osce::admin.statistics_query.subject_student_list', [
            'data' => $data,
            'exam' => $exam,
            'subject' => $subject,
            'avgScore' => $avgScore,
            'avgTime' => $avgTime
        ]);
    }

    /**
     * 考生统计
     * @param Request $request
     * @return \Illuminate\View\View
     * @author Jiangzhiheng
     */
    public function getStudentScore(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'sometimes|integer',
            'message' => 'sometimes'
        ]);
        $examId =   '';
        $message=   '';
        $examDownlist = Exam::select('id', 'name')->where('exam.status','<>',0)->orderBy('begin_dt', 'desc')->get();
        //获得最近的考试的id
        $lastExam = Exam::orderBy('begin_dt','desc')->where('exam.status','<>',0)->first();
        if (is_null($lastExam)) {
            $list = [];
        } else {

            $lastExamId = $lastExam->id;
            //获得参数
            $examId = $request->input('exam_id',$lastExamId);
            $message = $request->input('message',"");

            //获得学生的列表在该考试的列表
            $list = Student::getStudentScoreList($examId, $message);
            //为每一条数据插入统计值
            foreach ($list as $key => &$item) {
                $item->ranking = $key+1;
            }
        }
        return view('osce::admin.statistics_query.student_scores_list',[
            'data'=>$list,
            'examDownlist'=>$examDownlist,
            'exam_id'=>$examId,
            'message'=>$message
        ]);
    }

    /**
     * 考生成绩详情
     * @param Request $request
     * @return \Illuminate\View\View url    /osce/admin/course/student-details
     * url    /osce/admin/course/student-details
     * @throws \Exception
     * @author zhouqiang
     */
    public  function getStudentDetails(Request $request){
        $this->validate($request,[
          'student_id'=>'required|integer'
        ]);
        $studentId= $request->get('student_id');
        $examresultModel= new ExamResult();
        $studentList= $examresultModel->getstudentData($studentId);

        if(!$studentList){
            throw new \Exception('数据查询失败');
        }

        return view('osce::admin.statistics_query.student_subject_list',['studentList'=>$studentList]);

    }

    /**
     * 动态获取ajax列表
     * @author Jiangzhiheng
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubject(Request $request)
    {
        //验证
        $this->validate($request, [
            'exam_id'=>'sometimes|integer'
        ]);

        $examId = $request->input('exam_id',"");

        try {
            $exam = new Exam();
            $data = $exam->CourseControllerIndex($examId);

            return response()->json($this->success_data($data->toArray()));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}