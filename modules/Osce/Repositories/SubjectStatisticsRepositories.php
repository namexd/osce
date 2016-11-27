<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2016-02-23 14:00
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaperFormal;
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
    protected $StandardItemModel;
    //TODO 考站模型
    protected $StationModel;
    //TODO 考试项目模型
    protected $SubjectModel;
    //TODO 考试场次模型
    protected $ExamScreeningModel;


    public function __construct(Exam $exam,ExamResult $examResult,ExamStation $ExamStation,SubjectItem $StandardItem,Station $Station,Subject $Subject,ExamScreening $ExamScreening)
    {
        $this->ExamModel = $exam;
        $this->ExamResultModel = $examResult;
        //$this->ExamStationModel = $ExamStation;
        $this->StandardItemModel = $StandardItem;
        //$this->StationModel = $Station;
        //$this->SubjectModel = $Subject;
        $this->ExamScreeningModel = $ExamScreening;

    }

    /**
     * 科目分析成绩使用
     * @access public
     * @param $ExamId
     * @param int $qualified
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年2月26日09:31:50
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetSubjectStatisticsList2($ExamId,$qualified = 0)
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）TODO:zhoufuxiang 2016-06-22
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($ExamId);
        $builder = $this->ExamResultModel
            ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
            ->leftJoin('subject', 'subject.id', '=', 'station.subject_id')
            ->leftJoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
            ->leftjoin('exam_paper_formal', 'exam_paper_formal.student_id', '=', 'exam_result.student_id')
            ->whereIn('exam_result.exam_screening_id', $screening_ids)
            ->where('exam_result.flag', '=', 0);                    //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）

        //TODO:zhoufuxiang 2016-06-01 加上该条件为统计合格人数
        if($qualified){
            $builder->whereNotNull('subject.id')
                    ->where($DB->raw('exam_result.score/subject.rate_score'), '>=', '0.6')
                    ->orwhereNotNull('exam_paper.id')
                    ->where($DB->raw('exam_result.score/exam_paper_formal.total_score'), '>=', '0.6');//TODO:GaoDapeng 2016-06-17 加上该条件为统计理论合格人数
//            $builder = $builder
//                ->where(function ($query) use ($DB) {
//                    $query->whereNotNull('subject.id')->where($DB->raw('exam_result.score/subject.rate_score'), '>=', '0.6');
//                })
//                //TODO:GaoDapeng 2016-06-17 加上该条件为统计理论合格人数
//                ->orWhere(function ($query) use ($DB) {
//                    $query->whereNull('subject.id')->where($DB->raw('exam_result.score/exam_paper_formal.total_score'), '>=', '0.6');
//                });

        }
        $return = $builder->groupBy('station.id')
            ->select(
                'station.id as station_id',
                'subject.id as subjectId',
                'subject.title',
                'subject.mins',
                $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                $DB->raw('count(exam_result.student_id) as studentQuantity'),
                'subject.score as scoreTotal',
                'exam_result.original_score as score',
                'exam_paper.id as paper_id',
                'exam_paper.name as paper_title',
                'exam_paper.length as paper_mins',
                'exam_paper_formal.total_score as paper_total'
            )
            ->get();
        return $return;
    }

    /**
     * 分析单次考试 考试项目成绩
     * @param $screening_ids
     * @param $elderExam_ids
     * @return mixed
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @date   2016-07-05 10:00
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getSubjectStatisticsList($screening_ids, $elderExam_ids)
    {
        $DB = \DB::connection('osce_mis');
        $ExamDraftModel = new ExamDraft();
        //查询考试里对应的所有考站
        $station_ids = array_unique($ExamDraftModel->getStationBySubjectExam('', $elderExam_ids));
        sort($station_ids);

        $builder = $this->ExamResultModel
            ->select([
                'station.id as station_id',
                'subject.id as subjectId', 'subject.title', 'subject.mins',
                $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                $DB->raw('COUNT(exam_result.id) as studentQuantity'),
                'subject.rate_score as rate_score',
                'subject.score as scoreTotal',
                'exam_paper.id as paper_id',
                'exam_paper.name as paper_title',
                'exam_paper.length as paper_mins',
            ])
            ->leftJoin('subject', 'exam_result.subject_id', '=', 'subject.id')
            ->leftJoin('station', 'exam_result.station_id', '=', 'station.id')
            ->leftJoin('exam_paper', 'station.paper_id', '=', 'exam_paper.id')
            ->whereIn('exam_result.exam_screening_id', $screening_ids)
            ->whereIn('station.id', $station_ids)
            ->where('exam_result.flag', '=', 0);                    //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）

        $return = $builder->groupBy('exam_paper.id')->groupBy('subject.id')->get();

        return $return;
    }


    /**
     * 用于科目难度分析
     * @method
     * @url /osce/
     * @access public
     * @param $SubjectId
     * @param int $qualified
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年2月26日15:21:01
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetSubjectDifficultyStatisticsList($examId =[],$SubjectId, $sign = 'subject', $qualified=0)
    {
        $DB = \DB::connection('osce_mis');
        if(empty($examId)){
            //获取所有考过这个科目的考试Id
            $Exam = ExamDraftFlow::leftJoin('exam_draft','exam_draft.exam_draft_flow_id','=','exam_draft_flow.id')
                ->leftJoin('exam','exam.id','=','exam_draft_flow.exam_id')
                ->where('exam_draft.subject_id','=',$SubjectId)
                ->where('exam.status','=',2)
                ->select(
                    'exam.id',
                    'exam.pid'
                )
                ->groupBy('exam.id')
                ->get();
            //只获取主考试id
            $examId = [];
            foreach ($Exam as $value){
                $examId[] = $value->pid? :$value->id;
            }
            $examId = array_unique($examId);    //去重
        }
        $SubjectDiff =[];
        foreach ($examId as $item){
            //获取所有场次ID（包括子考试的场次ID）TODO:zhoufuxiang 2016-06-22
            list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($item);
            $builder = $this->ExamResultModel
                ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
                ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
                ->where('exam_result.flag', '=', 0)                  //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                ->where('exam_result.subject_id', '=', $SubjectId)
                ->whereIn('exam.id', $elderExam_ids)   
                ->whereIn('exam_result.exam_screening_id', $screening_ids);
            //$sign == 'subject' 为考试项目
            if($sign === 'subject')
            {
               
                //TODO 加上该条件为统计合格人数
                if($qualified){
                    $builder->where($DB->raw('exam_result.original_score/subject.score'), '>=', '0.6');
                }
                $builder = $builder->where('subject.id', '=', $SubjectId)
                    ->leftJoin('exam_draft', 'exam_draft.station_id', '=', 'exam_result.station_id')
                    ->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                    ->leftJoin('subject', 'exam_draft.subject_id', '=', 'subject.id')
                    ->whereNotNull('station.subject_id')
                    ->select(
                        'exam.id as ExamId',
                        'exam.pid as pid',
                        'exam.name as ExamName',
                        'exam.begin_dt as ExamBeginTime',
                        'exam.end_dt as ExamEndTime',
                        'subject.id as subjectId',
                        'subject.title as subjectName',
                        $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                        $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                        $DB->raw('count(exam_result.student_id) as studentQuantity')
                    );
            }else{
                //使用考卷，为理论考试
                //TODO 加上该条件为统计合格人数
                if($qualified){
                    $builder->where($DB->raw('exam_result.original_score/exam_paper_formal.total_score'), '>=', '0.6');
                }

                $builder = $builder->where('exam_paper_formal.exam_paper_id', '=', $SubjectId)
                    ->leftJoin('exam_paper_formal', 'exam_paper_formal.student_id', '=', 'exam_result.student_id')
                    ->whereNotNull('station.paper_id')
                    ->select(
                        'exam.id as ExamId',
                        'exam.pid as pid',
                        'exam.name as ExamName',
                        'exam.begin_dt as ExamBeginTime',
                        'exam.end_dt as ExamEndTime',
                        'exam_paper_formal.exam_paper_id as subjectId',
                        'exam_paper_formal.name as subjectName',
                        $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                        $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                        $DB->raw('count(exam_result.student_id) as studentQuantity')
                    );
            }
            $examResult =$builder->orderBy('ExamBeginTime','desc')->first();;
            if(!is_null($examResult) && $examResult->ExamId!=null){
                if($examResult->pid){
                    $examResult->ExamName = Exam::select('name')->where('id', '=', $examResult->pid)->first()->name;
                }
            }else{
                //去掉没有数据的考试
                unset($item);
                continue;
            }
            
            $SubjectDiff[]= $examResult;
        }

            return [$SubjectDiff,$examId];
    }

    /**
     * 用于考站成绩分析
     * @method
     * @url /osce/
     * @access public
     * @param $ExamId
     * @param $SubjectId
     * @return mixed
     * @author tangjun <tangjun@163.com>   <zhoufuxiang@163.com>
     * @date    2016年2月26日15:36:25            2016-07-06
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetSubjectStationStatisticsList($ExamId, $SubjectId, $sign = 'subject')
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($ExamId);

        $builder = $this->ExamResultModel
            ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
            ->leftJoin('teacher', 'teacher.id', '=', 'exam_result.teacher_id')
            ->whereIn('exam_result.exam_screening_id', $screening_ids)
            ->where('exam_result.flag', '=', 0);                    //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）

        //$sign == 'subject' 为考试项目
        if($sign == 'subject')
        {
            $builder = $builder
//                ->whereIn('exam_draft_flow.exam_id', $elderExam_ids)
//                ->leftJoin('exam_draft','exam_result.station_id','=','exam_draft.station_id')
//                ->leftjoin('exam_draft_flow','exam_draft.exam_draft_flow_id','=','exam_draft_flow.id')
                ->leftJoin('subject', 'subject.id', '=', 'exam_result.subject_id')
                ->where('subject.id', '=', $SubjectId)
                ->whereNotNull('subject.id')
                ->select(
                    'station.id as stationId',
                    'station.name as stationName',
                    'teacher.name as teacherName',
                    'subject.mins as examMins',
                    $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                    $DB->raw('count(exam_result.student_id) as studentQuantity')
                );
        }else{
            //使用考卷，为理论考试
            $builder = $builder
                ->leftJoin('exam_paper_formal', 'exam_paper_formal.student_id', '=', 'exam_result.student_id')
                ->where('station.paper_id', '=', $SubjectId)
                ->whereNotNull('station.paper_id')
                ->select(
                    'station.id as stationId',
                    'station.name as stationName',
                    'teacher.name as teacherName',
                    'exam_paper_formal.length as examMins',
                    $DB->raw('sec_to_time(avg(exam_result.time)) as timeAvg'),
                    $DB->raw('FORMAT(avg(exam_result.score), 2) as scoreAvg'),
                    $DB->raw('count(exam_result.student_id) as studentQuantity')
                );
        }
        return  $builder->groupBy('station.id')->get();
    }

    /**用于考站成绩分析详情 sec_to_time( FORMAT(
     * @method
     * @url /osce/
     * @access public
     * @param $stationId 考站id
     * @return mixed
     * @author xumin <xumin@163.com>
     * @date
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetStationDetails($examId, $subjectId, $stationId, $sign = 'subject')
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）  TODO:zhoufuxiang 2016-06-23
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examId);
        //获取主考试信息、及整个考试时间
        $examInfo = Exam::whereIn('id', $elderExam_ids)
                    ->select([
                        'name as examName',
                        $DB->raw('FROM_UNIXTIME(MIN(UNIX_TIMESTAMP(begin_dt))) as begin_dt'),
                        $DB->raw('FROM_UNIXTIME(MAX(UNIX_TIMESTAMP(end_dt))) as end_dt')
                    ])->orderBy('id')->first();

        $builder = $this->ExamResultModel
                    ->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
                    ->leftJoin('teacher', 'teacher.id', '=', 'exam_result.teacher_id')
                    ->whereIn('exam_result.exam_screening_id', $screening_ids)
                    ->where('exam_result.flag', '=', 0)                   //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）
                    ->where('exam_result.station_id', '=', $stationId)
                    ->groupBy('student.id');

        //$sign == 'subject' 为考试项目
        if($sign == 'subject'){
            $builder = $builder
                    ->leftJoin('subject', 'subject.id', '=', 'exam_result.subject_id')
                    ->whereNotNull('subject.id')                    //只取存在考试项目的
                    ->where('subject.id', '=', $subjectId)
                    ->select(
                        'subject.title as subjectTitle',            //考试项目名称
                        'student.name as studentName',              //考生名字
                        'student.grade_class as gradeClass',        //班级
                        'teacher.name as teacherName',              //老师
                        $DB->raw('sec_to_time(exam_result.time) as time'),  //耗时
                        'exam_result.score',                        //折算成绩
                        'exam_result.begin_dt',                     //考试开始时间
                        'exam_result.end_dt'                        //考试结束时间
                    );
        }else{
            //使用考卷，为理论考试
            $builder = $builder
                    ->leftJoin('station', 'exam_result.station_id', '=', 'station.id')
                    ->leftJoin('exam_paper', 'station.paper_id', '=', 'exam_paper.id')
                    ->whereNotNull('station.paper_id')              //只取存在考卷的
                    ->where('exam_paper.id', '=', $subjectId)
                    ->select(
                        'exam_paper.name as subjectTitle',          //考试项目名称
                        'student.name as studentName',              //考生名字
                        'student.grade_class as gradeClass',        //班级
                        'teacher.name as teacherName',              //老师
                        $DB->raw('sec_to_time(exam_result.time) as time'),  //耗时
                        'exam_result.score',                        //折算成绩
                        'exam_result.begin_dt',                     //考试开始时间
                        'exam_result.end_dt'                        //考试结束时间
                    );
        }

        return  [$builder->get(), $examInfo];
    }





    /**
     * 用于考核点分析
     * @method
     * @url /osce/
     * @access public
     * @param $ExamId
     * @param $SubjectId
     * @param $standardPid; 默认为 0 统计考核项父节点，  统计对应父考核点的考核子项
     * @return mixed
     * @author tangjun <tangjun@163.com> <zhoufuxiang@163.com>
     * @date    2016年2月26日15:36:25          2016-06-23 9:30
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetSubjectStandardStatisticsList($ExamId, $SubjectId, $standardPid=0, $pid=0)
    {
        $DB = \DB::connection('osce_mis');
        //获取所有场次ID（包括子考试的场次ID）  TODO:zhoufuxiang 2016-06-23
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($ExamId);

        $builder = $this->ExamResultModel
            ->select(
                'exam_result.student_id',
                'exam_result.id as exam_result_id',
                'exam_score.standard_item_id',
                'standard.id as standard_id',
                'standard_item.pid',
                $DB->raw('FORMAT(SUM(exam_score.score), 2) as score'),      //该科目的某一个考核点实际得分
                $DB->raw('FORMAT(SUM(standard_item.score), 2) as Zscore')   //该科目所有考核点总分
            )
            ->leftJoin('subject', 'subject.id', '=', 'exam_result.subject_id')
            ->leftJoin('exam_score', 'exam_score.exam_result_id', '=', 'exam_result.id')
            ->leftJoin('standard_item', 'standard_item.id', '=', 'exam_score.standard_item_id')
            ->leftJoin('standard', 'standard.id', '=', 'standard_item.standard_id')
            ->whereIn('exam_result.exam_screening_id', $screening_ids)
            ->where('subject.id', '=', $SubjectId)
            ->where('exam_result.flag', '=', 0);                   //只取有效成绩（0:使用；1:作废；2:弃考；3:作弊；4:替考）

        //根据需求 group不同的字段
        if($standardPid){
            $builder = $builder->where('standard_item.pid', '=', $standardPid)
                               ->groupBy($DB->raw('standard_item.id, exam_result.student_id'));
        }else{
            $builder = $builder->groupBy($DB->raw('standard_item.pid, exam_result.student_id'));
        }
        //根据父ID（pid）统计查询
        if($pid){
            $builder = $builder->where('standard_item.pid', '=', $pid);
        }

        return  $builder->get();
    }
    /**
     * 去除pid 构建数组
     * @method
     * @url /osce/
     * @access public
     * @author tangjun <tangjun@163.com>
     * @date 2016年2月26日16:34:06
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
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
     * @author tangjun <tangjun@163.com>
     * @date    2016年3月2日12:56:09
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetContent($id){
        $data = $this->StandardItemModel
            ->where('id','=',$id)
            ->select('content')
            ->first();
        if(!empty($data)){
            $data = $data->pluck('content');
        }
        return  $data;
    }

    /**
     * 获取所有已经完成的考试
     * @url  GET /osce/exam-list
     * @access public
     * @param null $pid
     * @param array $status
     * @return mixed
     *
     * @author tangjun <tangjun@163.com>   <zhoufuxiang@163.com>
     * @date    2016年3月1日11:47:49             2016-07-01 10:20
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetExamList($pid = null, $status = [1,2])
    {
        //将考试状态 转为数组
        $status = is_array($status)? $status : explode(',', $status);

        $result = $this->ExamModel->whereIn('status', $status)
                ->select('id', 'name', 'status', 'begin_dt', 'end_dt')
                ->orderBy('end_dt','desc');
        //判断是否只查主考试（pid为0：表示为主考试）
        if($pid !== null){
            //将考试状态 转为数组，并将父考试加入判断条件中
            $pid    = is_array($pid)? $pid : explode(',', $pid);
            $result = $result->whereIn('pid', $pid);
        }
        return $result->get();
    }
    /**
     * 获取除开理论考试外的所有已经完成的考试
     * @method
     * @url /osce/
     * @access public
     * @param int $status
     * @return mixed
     * @author wt <wangtao@163.com>
     * @date    2016年3月31日
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetExamListNoStandardGrade($pid = 0, $status = [1,2]){
        return $this->ExamModel
            ->select('exam.id as id','exam.name as name')
            ->leftJoin('exam_draft_flow', 'exam.id', '=', 'exam_draft_flow.exam_id')
            ->leftJoin('exam_draft', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->leftJoin('station', 'exam_draft.station_id', '=', 'station.id')
            ->whereIn('exam.status', $status)
            ->where('exam.pid', '=', $pid)
            ->where('station.type', '<>', 3)
            ->groupBy('exam.id')
            ->orderBy('end_dt','desc')
            ->get();


    }
    /**
     * 出科目的下拉菜单
     * @param $examId
     * @return array|\Illuminate\Support\Collection
     * @author Jiangzhiheng     <zhoufuxiang@163.com> 2016-06-29
     */
    public function subjectDownlist($examId, $paper = true, $sign = false)
    {
        //给考试对应的科目下拉数据
        if (is_object($examId)) {
            $StationTeacherBuilder = StationTeacher::whereIn('exam_id', $examId);
        } else {
            $StationTeacherBuilder = StationTeacher::where('exam_id', $examId);
        }
        $stationIdList = $StationTeacherBuilder->groupBy('station_id')->get()->pluck('station_id')->toArray();

        //TODO：zhoufuxiang  2016-06-29 从考场安排中获取对应的考试项目

        $subjectIdList = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                        ->whereIn('exam_draft.station_id', $stationIdList)
                        //->whereIn('exam_draft_flow.exam_id',$examId)
                        ->whereNotNull('exam_draft.subject_id');
        if (is_object($examId)) {
            $subjectIdList = $subjectIdList->whereIn('exam_draft_flow.exam_id',$examId);
        } else {
            $subjectIdList = $subjectIdList->where('exam_draft_flow.exam_id',$examId);
        }
        $subjectIdList = $subjectIdList ->groupBy('exam_draft.subject_id')
                                        ->select('exam_draft.subject_id')
                                        ->get();

        $subjectList = [];
        $paperList   = [];
        foreach ($subjectIdList as $value) {
            $subjectList[] = $value->subject;
        }
        if($paper){
            $paperIdList   = Station::whereIn('id', $stationIdList)->whereNotNull('paper_id')->groupBy('paper_id')->get();
            $paperList = [];
            foreach ($paperIdList as $value) {
                $paperList[] = $value->paper;
            }
        }
        if($sign){
            return [collect($subjectList), collect($paperList)];
        }

        $list = array_merge($subjectList, $paperList);   //考试项目、考卷数组合并
        $list = collect($list);

        return $list;
    }

    /**
     * 获取科目列表
     * @method
     * @url /osce/
     * @access public
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
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
     * @author tangjun <tangjun@163.com>
     * @date    2016年2月26日15:36:25
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function GetStandardDetails($standardPid){
        $DB = \DB::connection('osce_mis');
        $builder = $this->StandardItemModel->leftJoin('exam_score', function($join){
            $join -> on('exam_score.standard_item_id', '=','standard_item.id');
        });
        $data = $builder->where('standard_item.pid','=',$standardPid)
            ->groupBy('standard_item.id')
            ->select(
                'standard_item.pid',//评分标准父编号
                'standard_item.content',//名称
                'standard_item.score', //总分
                'exam_score.score as grade'//成绩
            //$DB->raw('sum(exam_score.score) as totalGrade') //总成绩
            )->get();
        //dd($data);
        return $data;
    }

    /**
     * 时间转换
     * @method
     * @url /osce/
     * @access public
     * @param $seconds, 秒戳
     * @return mixed
     * @author tangjun <tangjun@163.com>
     * @date    2016年3月4日09:41:31
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function timeTransformation($seconds){
        $time = 0;
        date_default_timezone_set("UTC");
        $time = date('H:i:s',$seconds);
        date_default_timezone_set("PRC");
        return  $time;
    }

    /**
     * 根据考试id和科目id找到对应的考生以及考生的成绩信息
     * @param $examId
     * @param $subjectId
     * @param string $sign
     * @return mixed
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @date   2016-06-23 15:45
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getStudentByExamAndSubject($examId, $subjectId, $sign = 'subject')
    {
        $DB = \DB::connection('osce_mis');
        \DB::connection('osce_mis')->enableQueryLog();
        //通过考试id获取所有场次包括子场次的id
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examId);

        //获取主考试信息、及整个考试时间
        $examInfo = Exam::whereIn('id', $elderExam_ids)
            ->select([
                'name',
                $DB->raw('FROM_UNIXTIME(MIN(UNIX_TIMESTAMP(begin_dt))) as begin_dt'),
                $DB->raw('FROM_UNIXTIME(MAX(UNIX_TIMESTAMP(end_dt))) as end_dt')
            ])->orderBy('id')->first();

        $builder = $this->ExamResultModel
            ->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
            ->leftJoin('teacher', 'teacher.id', '=', 'exam_result.teacher_id')
            ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
            ->whereIn('exam_result.exam_screening_id', $screening_ids)
            ->groupBy('exam_result.id')
            ->orderBy('exam_result.score', 'desc');

        //$sign == 'subject' 为考试项目
        if($sign == 'subject')
        {
            $builder = $builder
                ->leftJoin('subject', 'subject.id', '=', 'exam_result.subject_id')
                ->where('subject.id', '=', $subjectId)
                ->select([
                    'student.id as student_id',
                    'student.name as student_name',
                    'teacher.name as teacher',
                    'station.type as station_type',
                    'subject.mins as mins',
                    'exam_result.id as exam_result_id',
                    'exam_result.score as exam_result_score',
                    $DB->raw('sec_to_time(exam_result.time) as exam_result_time'),
                    'exam_result.begin_dt as exam_result_begin',
                    'exam_result.flag as exam_result_flag'
                ]);
        }else{
            //使用考卷，为理论考试
            $builder = $builder->leftJoin('exam_paper', 'exam_paper.id', '=', 'station.paper_id')
                ->whereNotNull('station.paper_id')                    //只取存在考卷的
                ->where('station.paper_id', '=', $subjectId)
                ->select([
                    'student.id as student_id',
                    'student.name as student_name',
                    'teacher.name as teacher',
                    'station.type as station_type',
                    'exam_paper.length as mins',
                    'exam_result.id as exam_result_id',
                    'exam_result.score as exam_result_score',
                    $DB->raw('sec_to_time(exam_result.time) as exam_result_time'),
                    'exam_result.begin_dt as exam_result_begin',
                    'exam_result.flag as exam_result_flag'
                ]);
        }

        return [$builder->paginate(config('osce.page_size')), $examInfo];
    }

    /**
     * 查询成绩合格的人数
     * @param $screening_ids
     * @param $station_id
     * @param $rate_score
     * @param $paper_total
     * @return mixed
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @date   2016-07-05 16:45
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getViaStudentNum($screening_ids, $station_id, $rate_score, $paper_id)
    {
        $DB = \DB::connection('osce_mis');
        $result = ExamResult::select([$DB->raw('COUNT(id) as via')])
                ->whereIn('exam_screening_id', $screening_ids)
                ->where('station_id', '=', $station_id)
                ->where('flag', '=', 0);
        //判断是否有考试项目的折算总分（不存在则为理论考试）
        if($rate_score){
            $result = $result->where($DB->raw("exam_result.score/$rate_score"), '>=', 0.6);
        }else{
            $paper_total = ExamPaperFormal::select('total_score')->where('exam_paper_id', '=', $paper_id)->first()->total_score;
            $result = $result->where($DB->raw("exam_result.score/$paper_total"), '>=', 0.6);
        }

        return $result->first();
    }

}