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
use Modules\Osce\Entities\StationTeacher;
/**
 * Class StatisticsRepositories
 * @package Modules\Osce\Repositories
 */
class SubjectStatisticsRepositories  extends BaseRepository
{
    //翻页配置条数    config('osce.page_size')
    /**
     * TestRepositories constructor.
     */
    //TODO 考试信息模型
    protected $ExamModel;
    //TODO 考试结果记录模型
    protected $ExamResultModel;
    //TODO 考试和考站关联模型
    protected $ExamStationModel;
    //TODO 考试项目子项模型
    protected $SubjectItemModel;
    //TODO 考站模型
    protected $StationModel;
    //TODO 考试项目模型
    protected $SubjectModel;


    public function __construct(Exam $exam,ExamResult $examResult,ExamStation $ExamStation,SubjectItem $SubjectItem,Station $Station,Subject $Subject)
    {
        $this->ExamModel = $exam;
        $this->ExamResultModel = $examResult;
        //$this->ExamStationModel = $ExamStation;
        $this->SubjectItemModel = $SubjectItem;
        //$this->StationModel = $Station;
        //$this->SubjectModel = $Subject;

    }

    /**
     * 科目分析成绩使用
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日09:31:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetSubjectStatisticsList($ExamId,$qualified = 0){
       $DB = \DB::connection('osce_mis');

/*    由于 exam_station 模型 数据缺失 暂时注释掉 改用exam_screening表链接
      $builder = $this->ExamResultModel->leftJoin('station', function($join){
           $join -> on('station.id', '=', 'exam_result.station_id');
       })->leftJoin('subject', function($join){
           $join -> on('subject.id', '=','station.subject_id');
       })->leftJoin('exam_station', function($join){
           $join -> on('exam_station.station_id', '=','station.id');
       })->where('exam_station.exam_id','=',$ExamId);*/

        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->where('exam_screening.exam_id','=',$ExamId);

        //TODO 加上该条件为统计合格人数
        if($qualified){
            $builder->where($DB->raw('exam_result.score/subject.score'),'>=','0.6');
        }

        $return = $builder->groupBy('subjectId')
           ->select(
               'subject.id as subjectId',
               'subject.title',
               'station.mins',
               $DB->raw('avg(exam_result.time) as timeAvg'),
               $DB->raw('avg(exam_result.score) as scoreAvg'),
               $DB->raw('count(exam_result.student_id) as studentQuantity'),
               'subject.score as scoreTotal',
               'exam_result.score as score'
           )
           ->get();
        return  $return;
    }

    /**
     * 用于科目难度分析
     * @method
     * @url /osce/
     * @access public
     * @param $SubjectId
     * @param int $qualified
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日15:21:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetSubjectDifficultyStatisticsList($SubjectId,$qualified=0){
        $DB = \DB::connection('osce_mis');

/*      由于 exam_station 模型 数据缺失 暂时注释掉 改用exam_screening表链接
        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_station', function($join){
            $join -> on('exam_station.station_id', '=','station.id');
        })->leftJoin('exam', function($join){
            $join -> on('exam.id', '=','exam_station.exam_id');
        });*/

        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('exam', function($join){
            $join -> on('exam.id', '=','exam_screening.exam_id');
        });

        //TODO 加上该条件为统计合格人数
        if($qualified){
            $builder->where($DB->raw('exam_result.score/subject.score'),'>=','0.6');
        }
        $builder = $builder->where('subject.id','=',$SubjectId)
            ->groupBy('exam.id')
            ->select(
                'exam.id as ExamId',
                'exam.name as ExamName',
                'subject.id as subjectId',
                'subject.title as subjectName',
                'exam.begin_dt as ExamBeginTime',
                'exam.end_dt as ExamEndTime',
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                $DB->raw('count(exam_result.student_id) as studentQuantity'),
                $DB->raw('exam_result.score/subject.score as a')
            )->orderBy('ExamBeginTime','desc');
        return  $builder->get();
    }

    /**
     * 用于考站成绩分析
     * @method
     * @url /osce/
     * @access public
     * @param $ExamId
     * @param $SubjectId
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetSubjectStationStatisticsList($ExamId,$SubjectId){
        $DB = \DB::connection('osce_mis');

/*      由于 exam_station 模型 数据缺失 暂时注释掉 改用exam_screening表链接
        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_station', function($join){
            $join -> on('exam_station.station_id', '=','station.id');
        })->leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=','exam_result.teacher_id');
        });*/

        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=','exam_result.teacher_id');
        });

        $builder = $builder->where('subject.id','=',$SubjectId)
            ->where('exam_screening.exam_id','=',$ExamId)
            ->groupBy('station.id')
            ->select(
                'station.id as stationId',
                'station.name as stationName',
                'teacher.name as teacherName',
                'station.mins as examMins',
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                $DB->raw('count(exam_result.student_id) as studentQuantity')
            );
        return  $builder->get();
    }

    /**用于考站成绩分析详情
     * @method
     * @url /osce/
     * @access public
     * @param $stationId 考站id
     * @return mixed
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetStationDetails($examId,$subjectId,$stationId){
        $DB = \DB::connection('osce_mis');

        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('student', function($join){
            $join -> on('exam_result.student_id', '=','student.id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=','exam_result.teacher_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam', function($join){
            $join -> on('exam.id', '=','exam_screening.exam_id');
        });

        $builder = $builder->where('station.id','=',$stationId)
            ->where('subject.id','=',$subjectId)
            ->where('exam.id','=',$examId)
            ->groupBy('student.id')
            ->select(
                'exam.name as examName', //考试名称
                'exam_screening.begin_dt',//考试开始时间
                'exam_screening.end_dt',//考试结束时间
                'subject.title as subjectTitle',//科目名称
                'station.name as stationName',//考站名称
                'student.name as studentName',//考生名字
                'exam_result.time',//耗时
                'exam_result.score',//成绩
                'teacher.name as teacherName'//老师
            );
        return  $builder->get();
    }





    /**
     * 用于考核点分析
     * @method
     * @url /osce/
     * @access public
     * @param $ExamId
     * @param $SubjectId
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetSubjectStandardStatisticsList($ExamId,$SubjectId){
        $DB = \DB::connection('osce_mis');

        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('exam_score', function($join){
            $join -> on('exam_score.exam_result_id', '=','exam_result.id');
        })->leftJoin('standard', function($join){
            $join -> on('standard.id', '=','exam_score.standard_id');
        });


        $builder = $builder->where('subject.id','=',$SubjectId)
            ->where('exam_screening.exam_id','=',$ExamId)
            ->groupBy($DB->raw('standard.pid,exam_result.student_id'))
            ->select(
                'standard.pid as pid',
                'exam_result.student_id',
                $DB->raw('SUM(exam_score.score) as score'),//该科目的某一个考核点实际得分
                $DB->raw('SUM(standard.score) as Zscore')   //该科目所有考核点总分
            );

        return  $builder->get();
    }

    /**
     * 去除pid 构建数组
     * @method
     * @url /osce/
     * @access public
     * @author tangjun <tangjun@misrobot.com>
     * @date 2016年2月26日16:34:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetPidArr($StandardData){
        $PidArr = [];
        if(count($StandardData)>0){
            foreach($StandardData as $v){
                $PidArr[] = $v['pid'];
            }
        }
        return  $PidArr;
    }

    /**
     * 根据考核点id 获取考核点内容
     * @method
     * @access public
     * @param $id
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月2日12:56:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetContent($id){
        return  $this->SubjectItemModel
            ->where('id','=',$id)
            ->select('content')
            ->first()
            ->pluck('content');
    }
    /**
     * 获取所有已经完成的考试
     * @method
     * @url /osce/
     * @access public
     * @param int $status
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月1日11:47:49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetExamList($status = 2){
        return $this->ExamModel->where('status','=',$status)
            ->select('id','name')
            ->orderBy('end_dt','desc')
            ->get();
    }

    /**
     * 出科目的下拉菜单
     * @param $examId
     * @return array|\Illuminate\Support\Collection
     * @author Jiangzhiheng
     */
    public function subjectDownlist($examId)
    {
        /*
         * 给考试对应的科目下拉数据
         */
        $StationTeacherBuilder = '';

        if (is_object($examId)) {
            $StationTeacherBuilder = StationTeacher::whereIn('exam_id', $examId);
        } else {
            $StationTeacherBuilder = StationTeacher::where('exam_id', $examId);
        }

        $subjectIdList = $StationTeacherBuilder
            ->groupBy('station_id')
            ->get()
            ->pluck('station_id');

        $stationList = Station::whereIn('id', $subjectIdList)->groupBy('subject_id')->get();

        $subjectList = [];
        foreach ($stationList as $value) {
            $subjectList[] = $value->subject;
        }

        $subjectList = collect($subjectList);

        return $subjectList;
    }

    /**
     * 获取科目列表
     * @method
     * @url /osce/
     * @access public
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetSubjectList(){
        $subject = new Subject();
        $data = $subject->select('id','title')->get();
        return $data;
    }
    /**
     * 用于考核点查看（详情）
     * @method
     * @url /osce/
     * @access public
     * @param $ExamId
     * @param $standardPid 评分标准父编号
     * @return mixed
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetStandardDetails($standardPid){
        $DB = \DB::connection('osce_mis');
        $builder = $this->SubjectItemModel->leftJoin('exam_score', function($join){
            $join -> on('exam_score.standard_id', '=','standard.id');
        });
        $data = $builder->where('standard.pid','=',$standardPid)
            ->groupBy('standard.id')
            ->select(
                'standard.pid',//评分标准父编号
                'standard.content',//名称
                'standard.score', //总分
                'exam_score.score as grade'//成绩
            //$DB->raw('sum(exam_score.score) as totalGrade') //总成绩
            )->get();
        //dd($data);
        return $data;
    }
}