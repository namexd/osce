<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@sulida.com>
 * @date 2016-02-23 14:00
 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Illuminate\Support\Facades\DB;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Repositories\BaseRepository;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Student;
/**
 * Class StatisticsRepositories
 * @package Modules\Osce\Repositories
 */
class TestScoreRepositories  extends BaseRepository
{
    /**
     * 查找考试与场次表获取场次ID
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016年2月26日15:06:29
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTester($ExamId)
    {
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($ExamId);
        $builder = new Exam();
        $builder = ExamResult::whereIn('exam_screening_id', $screening_ids)
                            ->where('flag', '=', 0)->get();         //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
        $idarr = [];
        foreach($builder as $v){
            if($v->student_id){
                $idarr[] = $v->student_id;
            }
        }

        $tester = Student::whereIn('id', $idarr)->get();
        return $tester;
    }

    /**
     * 根据考试ID和学生ID对学生科目成绩分析
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016年2月26日15:06:29
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTestSubject($exam_id, $student_id, $subjectId = [], $paperIds = [])
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($exam_id);
//        //根据 考试项目 获取考站集
//        $ExamDraftModel = new ExamDraft();
//        $stations = $ExamDraftModel->getStationBySubjectExam($subjectId, $elderExam_ids);

        $builder = new ExamResult();
        $builder = $builder->whereIn('exam_result.exam_screening_id', $screening_ids)
                    ->where('exam_result.flag', '=', 0)             //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                    ->leftjoin('student', 'exam_result.student_id', '=', 'student.id')
                    ->leftjoin('station', 'exam_result.station_id', '=', 'station.id')
                    ->leftJoin('subject', 'exam_result.subject_id', '=', 'subject.id')
                    ->leftjoin('exam_paper', 'station.paper_id', '=', 'exam_paper.id');
        //查询当前考试、对应考生的成绩分析
        if($student_id){
            $builder = $builder->where('exam_result.student_id', '=', $student_id)
                    ->select(
                        'subject.id', 'subject.title', 'subject.score as subscore',
                        'subject.mins', 'station.type', 'exam_result.id as result_id',
                        'exam_result.student_id', 'exam_result.score',
                        $DB->raw('sec_to_time(exam_result.time) as time'),          //时长：秒数转化为时分秒
                        'exam_paper.id as paper_id', 'exam_paper.name as paperName',
                        'exam_paper.length as Pmins', 'exam_result.flag as flag'
                    );
        }else{
            $builder = $builder->select(
                        $DB->raw('sec_to_time(FORMAT(avg(exam_result.time), 2)) as timeAvg'),
                        $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                        'subject.id', 'subject.title', 'subject.score', 'subject.mins',
                        'exam_paper.id as paper_id', 'exam_paper.name as paper_name',
                        'exam_result.id as result_id', 'exam_result.student_id',
                        'station.type', 'exam_result.flag as flag'
                    );
        }
        //查询当前考试的成绩分析
        if(!empty($subjectId) || !empty($paperIds)){
            $builder = $builder->whereIn('subject.id', $subjectId)
                        ->orwhereIn('station.paper_id', $paperIds);
        }

        return $builder->groupBy('subject.id')->groupBy('exam_paper.id')
                       ->orderBy('subject.id')->orderBy('exam_paper.id')->get();
    }

    /**
     * 考生成绩分析
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:29:59
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudent(){
        $examResoult = new ExamResult();
        //获取已考过试的所有学生
        $studentList = $examResoult->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->select('student.*')->get();
        return $studentList;
    }

    /**
     * 获取考生已考过的科目
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:29:59
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentSubject($stuid){
        $builder = new Subject();
        $builder = $builder->get();
        return $builder;
    }

    /**
     * 根据学生ID和科目ID获取学生成绩统计
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:29:59
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentScoreCount($student_id,$subid){
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        if($student_id){
            $builder = $builder
                ->where('exam_result.student_id','=',$student_id)
                ->select(
                    'subject.title',
                    'station.mins',
                    'exam_result.begin_dt',
                    'exam_result.id as result_id',
                    'exam_result.time',
                    'exam_result.score','subject.id'
                );
        }else{
            $builder = $builder->select(
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                'subject.id',
                'exam_result.id as result_id'
            );
        }
        $builder = $builder->where('subject.id','=',$subid)->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=', 'exam_result.exam_screening_id');
        })->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        })->leftJoin('exam', function($join){
            $join -> on('exam.id', '=', 'exam_screening.exam_id');
        })->groupBy('exam.id')->get();
        return $builder;
    }

    /**
     * 获取学生成绩分析
     * @param $student_id
     * @param $subid
     * @param $ExamId
     * @param string $sign
     * @return ExamResult
     *
     * @author fandian  <fandian@sulida.com>
     * @date   2016-07-04   17:13
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentScoreAnalysis($student_id, $subid, $ExamId = 0, $sign = 'subject')
    {
        if($sign == 'subject')
        {
            return $this->getStudentHistoryScoreCount($student_id, $subid, $ExamId);
        }else
        {
            return $this->getStudentHistoryScoreOfPaper($student_id, $subid, $ExamId);
        }
    }
    /**
     * @method  GET
     * @access public
     * @param $student_id
     * @param $subid
     * @param $ExamId
     * @return ExamResult
     * @author tangjun <tangjun@sulida.com>
     * @date    2016年3月3日14:04:11
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentHistoryScoreCount($student_id, $subid, $ExamId = 0)
    {
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        $builder = $builder->where('subject.id', '=', $subid)
                ->leftJoin('exam_draft','exam_draft.station_id','=','exam_result.station_id')
                ->leftJoin('exam_draft_flow','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
                ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
                ->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
                ->leftJoin('subject', 'subject.id', '=', 'exam_draft.subject_id')
                ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id');
        if($ExamId)
        {
            $data = $builder->whereIn('exam.id', $ExamId)
                ->select(
                    $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                    $DB->raw('FORMAT(avg(exam_result.score),2) as scoreAvg'),
                    'exam.id as exam_id', 'exam.name as exam_name',
                    'subject.id', 'station.type',
                    'exam_result.id as result_id', 'exam_result.student_id'
                );
        }else{
            $data = $builder->where('exam_result.student_id', '=', $student_id)
                ->select(
                    'subject.id', 'subject.title', 'station.type',
                    'exam.id as exam_id', 'exam.name as exam_name',
                    $DB->raw('sec_to_time(exam_result.time) as mins'),
                    'exam_result.begin_dt',
                    'exam_result.id as result_id',
                    'exam_result.score',
                    'exam_result.student_id'
                );
        }

        return $data->groupBy('exam.id')->get();
    }

    /**
     * 获取 对应学生 的考卷历史成绩分析
     * @param $student_id
     * @param $paperid
     * @param $ExamId
     * @return mixed
     *
     * @author fandian  <fandian@sulida.com>
     * @date   2016-07-04   17:35
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentHistoryScoreOfPaper($student_id, $paperid, $ExamId)
    {
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        $builder = $builder->where('exam_paper.id', '=', $paperid)
            ->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
            ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
            ->leftJoin('exam_paper', 'station.paper_id', '=', 'exam_paper.id')
            ->leftJoin('exam_screening', 'exam_result.exam_screening_id', '=', 'exam_screening.id')
            ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id');
        if($ExamId)
        {
            $data = $builder->whereIn('exam.id', $ExamId)
                ->select(
                    $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                    $DB->raw('FORMAT(avg(exam_result.score),2) as scoreAvg'),
                    'exam.id as exam_id', 'exam.name as exam_name',
                    'exam_paper.id', 'exam_paper.name as title', 'station.type',
                    'exam_result.id as result_id', 'exam_result.student_id'
                );
        }else{
            $data = $builder->where('exam_result.student_id', '=', $student_id)
                ->select(
                    'exam_paper.id', 'exam_paper.name as title', 'station.type',
                    'exam.id as exam_id', 'exam.name as exam_name',
                    $DB->raw('sec_to_time(exam_result.time) as mins'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as score'),
                    'exam_result.begin_dt',
                    'exam_result.id as result_id',
                    'exam_result.student_id'
                );
        }

        return $data->groupBy('exam.id')->get();
    }

    /**
     * 根据学生ID和科目ID获取学生成绩统计
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:29:59
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStudentScoreAvg($student_id,$subid,$ExamId = 0){
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        if($student_id){
            $builder = $builder
                ->where('exam_result.student_id','=',$student_id)
                ->select(
                    'subject.title',
                    'station.mins',
                    'exam_result.begin_dt',
                    'exam_result.id as result_id',
                    'exam_result.time',
                    'exam_result.score','subject.id'
                );
        }else{
            $builder = $builder->select(
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                'subject.id',
                'exam_result.id as result_id'
            );
        }
        $builder = $builder->where('subject.id','=',$subid)->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=', 'exam_result.exam_screening_id');
        })->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        })->leftJoin('exam', function($join){
            $join -> on('exam.id', '=', 'exam_screening.exam_id');
        })->groupBy('exam.id')->get();
        return $builder;
    }

    /**
     * 查找科目
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-2-29 09:29:59
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getSubList(){
        $subject = new Subject();
        $subjectlist = $subject->get()->toArray();
        return $subjectlist;
    }

    /**
     * 获取考试数据
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 16:56:06
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamList(){
        $exam = new Exam();
        $examlist = $exam->where('status','=',2)->select('id','name')->get()->toArray();
        return $examlist;
    }

    /**
     * 获取科目
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 16:56:06
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getSubjectlist($examid)
    {
        $ExamResult = new ExamResult();//
        $examlist = $ExamResult->where('exam_screening.exam_id','=',$examid)
            ->leftjoin('exam_screening',function($join){
                $join->on('exam_screening.id','=','exam_result.exam_screening_id');
            })->leftjoin('station',function($join){
                $join->on('station.id','=','exam_result.station_id');
            })->leftjoin('subject',function($join){
                $join->on('subject.id','=','station.subject_id');
            })->select('subject.id','subject.title')->groupBy('subject.id')->get();
        return $examlist;
    }

    /**
     * 考生成绩分析-老师列表数据
     * @access public
     * @param $ExamId 考试id
     * @param int $qualified 考试项目id
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 17:26:32
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherData2($examid, $subjectid, $subject = 'undefined')
    {
        //获取所有场次ID（包括子考试的场次ID）
//        $ExamScreening = new ExamScreening();
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examid);

        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();

        //$subject 为undefined，表示为试卷ID，否则为考试项目ID
        if($subject == 'undefined')
        {
            //传过来的值是试卷id
            $examlist = $ExamResult->where('exam_paper.id', '=', $subjectid)
                ->where('exam_screening.exam_id', '=', $examid)
                ->leftjoin('exam_screening', 'exam_screening.id','=','exam_result.exam_screening_id')
                ->leftjoin('student', 'student.id', '=', 'exam_result.student_id')
                ->leftjoin('station', 'station.id', '=', 'exam_result.station_id')
                ->leftjoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
                ->select(
                    'student.teacher_name',
                    'student.grade_class',
                    'exam_paper.id as pid',
                    'exam_screening.exam_id as exam_id',
                    'exam_result.id as rid',
                    $DB->raw('count(student.id) as stuNum'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore'),
                    $DB->raw('FORMAT(max(exam_result.score), 2) as maxScore'),
                    $DB->raw('FORMAT(min(exam_result.score), 2) as minScore')
                )->groupBy('student.grade_class')->get();
        }else{
//            $connection=\DB::connection('osce_mis');
//            $connection->enableQueryLog();
            //传过来的值为科目id
            $examlist = $ExamResult->where('subject.id',$subjectid)
                ->where('exam_screening.exam_id','=',$examid)
                ->leftjoin('exam_screening', 'exam_screening.id','=','exam_result.exam_screening_id')
                ->leftjoin('student', 'student.id','=','exam_result.student_id')
                ->leftjoin('station', 'station.id','=','exam_result.station_id')
                ->leftjoin('subject', 'subject.id','=','station.subject_id')
                ->select(
                    'student.teacher_name',
                    'student.grade_class',
                    'exam_screening.exam_id as exam_id',
                    'exam_result.id as rid',
                    $DB->raw('count(student.id) as stuNum'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore'),
                    $DB->raw('FORMAT(max(exam_result.score), 2) as maxScore'),
                    $DB->raw('FORMAT(min(exam_result.score), 2) as minScore')
                )->groupBy('student.grade_class')->get();
//            dd($connection->getQueryLog());
        }

//        dd($examlist->toArray());
        return $examlist;
    }

    /**
     * 教学成绩分析-根据班级统计分析数据
     * @param $examid
     * @param $subjectid
     * @param string $subject
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-20 14:13
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getTeacherData($examid, $subjectid, $subject = 'undefined')
    {
        $DB = \DB::connection('osce_mis');
//        $dsn = $DB->enableQueryLog();
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examid);

        $result = ExamResult::whereIn('exam_result.exam_screening_id', $screening_ids)
                ->where('exam_result.flag', '=', 0)         //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                ->leftjoin('student', 'student.id', '=', 'exam_result.student_id');
        //$subject 为undefined，表示为试卷ID，否则为考试项目ID
        if($subject == 'undefined')
        {
            $result = $result->where('exam_paper.id', '=', $subjectid)
                ->leftjoin('station', 'station.id', '=', 'exam_result.station_id')
                ->leftjoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
                ->select(
                    'student.teacher_name',
                    'student.grade_class',
                    'exam_result.id as rid',
                    'exam_paper.id as pid',
                    $DB->raw('count(student.id) as stuNum'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore'),
                    $DB->raw('FORMAT(max(exam_result.score), 2) as maxScore'),
                    $DB->raw('FORMAT(min(exam_result.score), 2) as minScore')
                );
        }else
        {
            $ExamDraftModel = new ExamDraft();
            //根据 考试项目、考试(包括子考试) 获取对应考站数组
            $stations = $ExamDraftModel->getStationBySubjectExam($subjectid, $elderExam_ids);
            $result = $result->whereIn('exam_result.station_id', $stations);
            $result = $result->where('exam_result.subject_id', $subjectid)
//            $result = $result->leftjoin (function($query) {
//                     $query->select(DB::raw(
//                            'exam_score.exam_result_id'
//                            . 'exam_score.subject_id '
//                        )) ->groupBy('exam_score.subject_id');
//
//                    })

//             $result = $result->leftjoin($DB->raw('select exam_result_id , subject_id FROM exam_score GROUP BY exam_result_id'),'exam_result.id','=','exam_score.exam_result_id')
//                \DB::connection('')->query('')
//            $result = $result->leftjoin('exam_score', function($query) use($subjectid){
//                $query->on('exam_result.id','=','exam_score.exam_result_id')
//                                                    ->having('exam_score.subject_id',$subjectid)
//                                                        ->groupBy('exam_score.subject_id');
//            })
//            $result = $result->where('exam_score.subject_id',$subjectid)
                ->select(
                    'student.teacher_name',
                    'student.grade_class',
                    'exam_result.id as rid',
                    $DB->raw('count(student.id) as stuNum'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore'),
                    $DB->raw('FORMAT(max(exam_result.score), 2) as maxScore'),
                    $DB->raw('FORMAT(min(exam_result.score), 2) as minScore')
                );
        }

//        $ghdh = $DB ->getQueryLog();
//        dd($ghdh);
        return $result->groupBy('student.grade_class')->get();
    }

    /**
     * 考生成绩分析-老师列表数据
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 17:26:32
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getGradeScore($classId,$subid,$examid){
        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();
        if($classId){
            if(intval($subid)){
                $ExamResult = $ExamResult->where('exam_paper.id','=',$subid)->where('student.grade_class','=',$classId)->select(
                    'exam.name',
                    $DB->raw('avg(exam_result.score) as avgScore'),
                    'exam.id',
                    'exam_result.id as rid',
                    'exam_paper.id as pid'
                );
            }else{
                $ExamResult = $ExamResult->where('student.grade_class','=',$classId)->select(
                    'exam.name',
                    $DB->raw('avg(exam_result.score) as avgScore'),
                    'exam.id',
                    'exam_result.id as rid'
                );
            }

        }else{
            if(intval($subid)){
                $ExamResult = $ExamResult->where('exam_paper.id','=',$subid);
            }
            $ExamResult = $ExamResult->select(
                $DB->raw('avg(exam_result.score) as avgScore'),
                'exam.id'
            );
        }

        $ExamResult = $ExamResult->where('exam.id','=',$examid);
        $ExamResult = $ExamResult->leftjoin('exam_screening',function($join){
            $join->on('exam_screening.id','=','exam_result.exam_screening_id');
        })->leftjoin('exam',function($join){
            $join->on('exam.id','=','exam_screening.exam_id');
        })->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->leftjoin('station',function($join){
            $join->on('station.id','=','exam_result.station_id');
        });
        if(intval($subid)){

            $ExamResult = $ExamResult->where('exam_paper.id','=',$subid)->leftjoin('exam_paper',function($join){
                $join->on('exam_paper.id','=','station.paper_id');
            });
        }

        $examlist = $ExamResult->groupBy('student.teacher_name')->get();
        return $examlist;
    }

    /**
     * 获取班级历史记录
     * @param $gradeClass
     * @param $paper_id
     * @param $exam_id
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-21 14:13
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getGradeScore2($examId = [], $paper_id, $subject_id, $gradeClass = '')
    {
        $DB = \DB::connection('osce_mis');
        //根据班级获取其 参加过的考试ID组
        if(empty($examId)){
            $exams  = Student::select(['exam.id', 'exam.pid'])->leftJoin('exam', 'student.exam_id', '=', 'exam.id')
                    ->where('grade_class', '=', $gradeClass)->groupBy('exam_id')->get();
            $examId = [];
            //只取主考试ID数组
            foreach ($exams as $exam) {
                $examId[] = $exam->pid? :$exam->id;
            }
            $examId = array_unique($examId);    //去重
        }

        $examlist = [];      //用于保存返回的数据
        foreach ($examId as $item)
        {
            //获取所有场次ID（包括子考试的场次ID）
            list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($item);

            $ExamResult = new ExamResult();
            $ExamResult = $ExamResult
                    ->leftjoin('student', 'exam_result.student_id', '=', 'student.id')
                    ->leftjoin('station', 'exam_result.station_id', '=', 'station.id')
                    ->leftjoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                    ->leftjoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
                    ->whereIn('exam_result.exam_screening_id', $screening_ids)
                    ->whereIn('exam.id', $elderExam_ids)
                    ->where('exam_result.flag', '=', 0);             //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
            //根据班级查找
            if($gradeClass){
                $ExamResult = $ExamResult->where('student.grade_class', '=', $gradeClass)
                                         ->groupBy('student.grade_class');
            }
            //判断是 考试项目 还是 考卷
            if($subject_id){
                $ExamDraftModel = new ExamDraft();
                //根据 考试项目、考试(包括子考试) 获取对应考站数组
                $stations = $ExamDraftModel->getStationBySubjectExam($subject_id, $elderExam_ids);

                $ExamResult = $ExamResult->whereIn('exam_result.station_id', $stations)
                            ->where('exam_result.subject_id', $subject_id)
                            ->select(
                                'exam.id', 'exam.name',
                                'exam.pid', 'exam_result.id as result_id',
                                $DB->raw('count(student.id) as stuNum'),
                                $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore')
                            );
            }elseif ($paper_id)
            {
                $ExamResult = $ExamResult->where('exam_paper.id', '=', $paper_id)
                    ->leftjoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
                    ->select(
                        'exam.id', 'exam.name',
                        'exam.pid',
                        'exam_result.id as result_id', 'exam_paper.id as paper_id',
                        $DB->raw('FORMAT(avg(exam_result.score), 2) as avgScore')
                    );
            }
            $examResult = $ExamResult->first();

            if(!is_null($examResult)){
                if($examResult->pid){
                    $examResult->name = Exam::select('name')->where('id', '=', $examResult->pid)->first()->name;
                }
            }else{
                //去掉没有数据的考试
                unset($item);
                continue;
            }
            $examlist[] = $examResult;
        }

        return [collect($examlist), $examId];
    }

    /**
     * 考生成绩分析-班级成绩明细
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date    2016-3-2 17:26:32
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getGradeDetailList($exam_id, $paperID, $classID, $subject = 'subject')
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($exam_id);

        $ExamResult = new ExamResult();
        $examlist = $ExamResult->where('student.grade_class', '=', $classID)
                    ->whereIn('exam_result.exam_screening_id', $screening_ids)
                    ->where('exam_result.flag', '=', 0)             //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                    ->leftjoin('student', 'student.id', '=', 'exam_result.student_id')
                    ->leftjoin('teacher', 'teacher.id','=','exam_result.teacher_id')
                    ->groupBy('student.id')
                    ->select(
                        'student.name', 'teacher.name as tname',
                        'exam_result.score', 'exam_result.begin_dt',
//                        $DB->raw('count(student.id) as stuNum'),
                        $DB->raw('sec_to_time(exam_result.time) as time')       //时长：秒数转化为时分秒
                    );

        //$subject == 'undefined'，表示为理论考试，需查询试卷信息
        if($subject == 'undefined')
        {

            $examlist = $examlist->where('exam_paper.id', '=', $paperID)
                    ->leftjoin('station', 'station.id', '=', 'exam_result.station_id')
                    ->leftjoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id');
        }
        else   //否则查询考试项目
        {
            $ExamDraftModel = new ExamDraft();
            //根据 考试项目、考试(包括子考试) 获取对应考站数组
            $stations = $ExamDraftModel->getStationBySubjectExam($paperID, $elderExam_ids);
            $examlist = $examlist->whereIn('exam_result.station_id', $stations);
            $examlist = $examlist->where('exam_result.subject_id', $paperID);
        }
//        dd($paperID, $examlist->get());
        return $examlist->get();
    }

    /**
     * 考生成绩分析-班级明细简介
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <zhoufuixang@sulida.com>
     * @date    2016-3-2 17:26:32  2016-06-21 11:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamDetails($exam_id, $classID, $paperID, $subject)
    {
        $DB = \DB::connection('osce_mis');
        //获取考试详情
        $exam = Exam::find($exam_id);
        if(is_null($exam)){
            throw new \Exception('没有找到对应的考试信息', -101);
        }
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($exam_id);

        $ExamResult = new ExamResult();//
        $examlist   = $ExamResult->leftjoin('student', 'student.id', '=', 'exam_result.student_id')
                    ->whereIn('exam_result.exam_screening_id', $screening_ids)
                    ->where('student.grade_class', '=', $classID)
                    ->where('exam_result.flag', '=', 0);          //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）

        //存在试卷ID，表示为理论考试，需查询试卷信息
        if($subject == 'undefied' || $subject == 0)
        {
            $examlist = $examlist->where('exam_paper.id', '=', $paperID)
                        ->leftjoin('station', 'station.id', '=', 'exam_result.station_id')
                        ->leftjoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
                        ->select('student.grade_class',
                            $DB->raw('MIN(exam_result.begin_dt) AS begin_dt'),
                            $DB->raw('MAX(exam_result.end_dt) AS end_dt'),
                            'exam_paper.id', 'exam_paper.name as paper_name'
                        )->first();
        }else{
            $ExamDraftModel = new ExamDraft();
            //根据 考试项目、考试(包括子考试) 获取对应考站数组
            $stations = $ExamDraftModel->getStationBySubjectExam(($paperID? :$subject), $elderExam_ids);

            $examlist = $examlist->whereIn('exam_result.station_id', $stations)
                        ->select('student.grade_class',
                            $DB->raw('MIN(exam_result.begin_dt) AS begin_dt'),
                            $DB->raw('MAX(exam_result.end_dt) AS end_dt')
                        )->first();
        }
        //将主考试信息加入其中
        if(!is_null($examlist)){
            $examlist->name = $exam->name;
        }

        return $examlist;
    }

    /**
     * 获取考试的 考试项目、试卷的下拉列表
     * @param $exam_id
     * @return array
     */
    public function getSubjectPaperList($exam_id, $sign = true)
    {
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($exam_id);

        if($sign){
            //获取该考试对应的试卷信息
            $paperlist = ExamResult::whereIn('exam_result.exam_screening_id', $screening_ids)
                ->where('exam_result.flag', '=', 0)             //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                ->whereNotNull('exam_paper_exam_station.exam_paper_id')
                ->leftjoin('exam_paper_exam_station', 'exam_paper_exam_station.station_id', '=', 'exam_result.station_id')
                ->leftjoin('exam_paper', 'exam_paper.id', '=', 'exam_paper_exam_station.exam_paper_id')
                ->select('exam_paper.id', 'exam_paper.name', 'exam_result.station_id')
                ->groupBy('exam_paper.id')->get()->toArray();
        }else{
            $paperlist = [];
        }

        //获取该场考试对应科目信息
        $subjectlist = ExamResult::whereIn('exam_result.exam_screening_id', $screening_ids)
            ->where('exam_result.flag', '=', 0)             //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
            ->whereNotNull('exam_draft.subject_id')
            ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
            ->leftJoin('exam_draft_flow', 'exam_draft_flow.exam_id', '=', 'exam_screening.exam_id')
            ->leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->leftJoin('subject', 'subject.id', '=', 'exam_draft.subject_id')
            ->select('subject.id', 'subject.title as name', 'exam_draft.subject_id')
            ->groupBy('subject.id')->get()->toArray();

        return [$subjectlist, $paperlist];
    }


}


