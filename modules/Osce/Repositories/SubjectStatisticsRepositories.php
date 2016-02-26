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
/*
 * exam_result

exam_station

standard

station

subject*/
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
        //$this->ExamModel = $exam;
        $this->ExamResultModel = $examResult;
        //$this->ExamStationModel = $ExamStation;
        //$this->SubjectItemModel = $SubjectItem;
        //$this->StationModel = $Station;
        //$this->SubjectModel = $Subject;

    }

    /**
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
       $builder = $this->ExamResultModel->leftJoin('station', function($join){
           $join -> on('station.id', '=', 'exam_result.station_id');
       })->leftJoin('subject', function($join){
           $join -> on('subject.id', '=','station.subject_id');
       })->leftJoin('exam_station', function($join){
           $join -> on('exam_station.station_id', '=','station.id');
       })->where('exam_station.exam_id','=',$ExamId);

        //TODO 加上该条件为统计合格人数
        if($qualified){
            $builder->where($DB->raw('exam_result.score/subject.score'),'>','0.6');
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
    public function GetQualifiedNumber(){

    }
}