<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:00
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日15:06:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTester($ExamId){
        $builder = new Exam();
        $builder = $builder->where('exam.id','=',$ExamId)->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.exam_id', '=', 'exam.id');
        })->leftJoin('exam_result', function($join){
            $join -> on('exam_result.exam_screening_id', '=', 'exam_screening.id');
        })->get();
        $idarr = [];
        foreach($builder as $v){
            if($v->student_id){
                $idarr[] = $v->student_id;
            }
        }

        $tester = Student::whereIn('id',$idarr)->get();
        return $tester;
    }

    /**
     * 根据考试ID和学生ID对学生科目成绩分析
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016年2月26日15:06:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTestSubject($examid,$student_id,$subjectId){
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        if($student_id){
            $builder = $builder->where('exam_result.student_id','=',$student_id)->select('subject.title','subject.score as subscore','station.mins','exam_result.id as result_id','exam_result.time','exam_result.score','subject.id');
        }else{
            $builder = $builder->select(
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                'subject.id',
                'subject.title',
                'subject.score',
                'exam_result.id as result_id'
            );
        }
        if(!empty($subjectId)){
            $builder = $builder->whereIn('subject.id',$subjectId);
        }

        $builder = $builder->where('exam_screening.exam_id','=',$examid)->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=', 'exam_result.exam_screening_id');
        })->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        })->groupBy('subject.id')->get();
        //dd($builder);
        return $builder;
    }

    /**
     * 考生成绩分析
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:29:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:29:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:29:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @method  GET
     * @access public
     * @param $student_id
     * @param $subid
     * @param $ExamId
     * @return ExamResult
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月3日14:04:11
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentHistoryScoreCount($student_id,$subid,$ExamId = 0){
        $DB = \DB::connection('osce_mis');
        $builder = new ExamResult();
        if($ExamId){
            $builder = $builder->whereIn('exam.id', $ExamId)
                ->select(
                    $DB->raw('avg(exam_result.time) as timeAvg'),
                    $DB->raw('avg(exam_result.score) as scoreAvg'),
                    'subject.id',
                    'exam_result.id as result_id',
                    'exam.id as exam_id'
                );
        }else{
            $builder = $builder->where('exam_result.student_id', '=', $student_id)
                ->select(
                    'subject.title',
                    'exam_result.time as mins',
                    'exam_result.begin_dt',
                    'exam_result.id as result_id',
                    'exam_result.score','subject.id',
                    'exam.id as exam_id',
                    'exam.name as exam_name'
                );
        }
        $data = $builder->where('subject.id', '=', $subid)
            ->leftJoin('student', function ($join) {
                $join->on('student.id', '=', 'exam_result.student_id');
            })->leftJoin('exam_screening', function ($join) {
                $join->on('exam_screening.id', '=', 'exam_result.exam_screening_id');
            })->leftJoin('station', function ($join) {
                $join->on('station.id', '=', 'exam_result.station_id');
            })->leftJoin('subject', function ($join) {
                $join->on('subject.id', '=', 'station.subject_id');
            })->leftJoin('exam', function ($join) {
                $join->on('exam.id', '=', 'exam_screening.exam_id');
            })->groupBy('exam.id')
            ->get();
        return $data;

    }

    /**
     * 根据学生ID和科目ID获取学生成绩统计
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:29:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-2-29 09:29:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 16:56:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 16:56:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSubjectlist($examid){
        $ExamResult = new ExamResult();//
        $examlist = $ExamResult->where('exam_screening.exam_id','=',$examid)->leftjoin('exam_screening',function($join){
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
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 17:26:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTeacherData($examid,$subjectid){
        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();
        $examlist = $ExamResult->where('subject.id','=',$subjectid)->where('exam_screening.exam_id','=',$examid)->leftjoin('exam_screening',function($join){
            $join->on('exam_screening.id','=','exam_result.exam_screening_id');
        })->leftjoin('station',function($join){
            $join->on('station.id','=','exam_result.station_id');
        })->leftjoin('subject',function($join){
            $join->on('subject.id','=','station.subject_id');
        })->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->select(
            'student.teacher_name',
            'student.grade_class',
            'subject.id as subid',
            'exam_screening.exam_id as exam_id',
            'exam_result.id as rid',
            $DB->raw('count(student.id) as stuNum'),
            $DB->raw('avg(exam_result.score) as avgScore'),
            $DB->raw('max(exam_result.score) as maxScore'),
            $DB->raw('min(exam_result.score) as minScore')
        )->groupBy('student.teacher_name')->get();
        return $examlist;
    }
    /**
     * 考生成绩分析-老师列表数据
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 17:26:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getGradeScore($classId,$subid,$examid){
        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();
        if($classId){
            $ExamResult = $ExamResult->where('student.grade_class','=',$classId)->where('subject.id','=',$subid)->select(
                'exam.name',
                $DB->raw('avg(exam_result.score) as avgScore'),
                'exam.id',
                'exam_result.id as rid',
                'subject.id as sid'
            );
        }else{
            $ExamResult = $ExamResult->where('subject.id','=',$subid)->select(
                $DB->raw('avg(exam_result.score) as avgScore'),
                'exam.id'
            );
        }
        $ExamResult = $ExamResult->where('exam.id','=',$examid);
        $examlist = $ExamResult->leftjoin('exam_screening',function($join){
            $join->on('exam_screening.id','=','exam_result.exam_screening_id');
        })->leftjoin('exam',function($join){
            $join->on('exam.id','=','exam_screening.exam_id');
        })->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->leftjoin('station',function($join){
            $join->on('station.id','=','exam_result.station_id');
        })->leftjoin('subject',function($join){
            $join->on('subject.id','=','station.subject_id');
        })->orderBy('exam.name')->get();
        return $examlist;
    }

    /**
     * 考生成绩分析-班级成绩明细
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 17:26:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getGradeDetailList($examID,$subID){
        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();
        $examlist = $ExamResult->where('exam.id','=',$examID)->where('subject.id','=',$subID)->leftjoin('exam_screening',function($join){
            $join->on('exam_screening.id','=','exam_result.exam_screening_id');
        })->leftjoin('exam',function($join){
            $join->on('exam.id','=','exam_screening.exam_id');
        })->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->leftjoin('teacher',function($join){
            $join->on('teacher.id','=','exam_result.teacher_id');
        })->leftjoin('station',function($join){
            $join->on('station.id','=','exam_result.station_id');
        })->leftjoin('subject',function($join){
            $join->on('subject.id','=','station.subject_id');
        })->select(
            'student.name',
            'exam_result.begin_dt',
            'exam_result.time',
            'exam_result.score',
            'teacher.name as tname'
        )->get();
        return $examlist;
    }

    /**
     * 考生成绩分析-班级明细简介
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date    2016-3-2 17:26:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamDetails($examID,$ResultID,$subjectID){
        $DB = \DB::connection('osce_mis');
        $ExamResult = new ExamResult();//
        //dd($subjectID);
        $examlist = $ExamResult->where('subject.id','=',$subjectID)->where('exam.id','=',$examID)->where('exam_result.id','=',$ResultID)->leftjoin('exam_screening',function($join){
            $join->on('exam_screening.id','=','exam_result.exam_screening_id');
        })->leftjoin('exam',function($join){
            $join->on('exam.id','=','exam_screening.exam_id');
        })->leftjoin('student',function($join){
            $join->on('student.id','=','exam_result.student_id');
        })->leftjoin('station',function($join){
            $join->on('station.id','=','exam_result.station_id');
        })->leftjoin('subject',function($join){
            $join->on('subject.id','=','station.subject_id');
        })->select(
            'exam.name',
            'exam_result.begin_dt',
            'exam_result.end_dt',
            'student.grade_class',
            'subject.title',
            'subject.id'
        )->first();
        //dd($examlist);
        return $examlist;
    }
}


