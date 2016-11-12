<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:00
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Repositories\BaseRepository;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Standard;
/**
 * Class StatisticsRepositories
 * @package Modules\Osce\Repositories
 */
class MyRepositories  extends BaseRepository
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
    //TODO 评分标准模型
    protected $StandardItemModel;

    public function __construct(Exam $exam,ExamResult $examResult,ExamStation $ExamStation,SubjectItem $SubjectItem,Station $Station,Subject $Subject,StandardItem $StandardItem)
    {
        $this->ExamResultModel = $examResult;
        $this->StandardItemModel = $StandardItem;
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
        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=','exam_result.teacher_id');
        });
        $data = $builder->where('subject.id','=',$SubjectId)
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
            )->get();
        return  $data;
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
    public function GetSubjectStandardStatisticsList($ExamId,$SubjectId,$qualified=0){
        $DB = \DB::connection('osce_mis');

        $builder = $this->ExamResultModel->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->leftJoin('exam_score', function($join){
            $join -> on('exam_score.exam_result_id', '=','exam_result.id');
        })->leftJoin('standard_item', function($join){
            $join -> on('standard_item.id', '=','exam_score.standard_item_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','exam_score.subject_id');
        });
        //TODO 加上该条件为统计合格人数
        if($qualified){
            $builder->having($DB->raw('sum(exam_score.score)/sum(standard_item.score)'),'>=','0.6');
        }
        $data = $builder->where('subject.id','=',$SubjectId)
            ->where('exam_screening.exam_id','=',$ExamId)
            ->groupBy($DB->raw('standard_item.pid'))
            ->select(
                'standard_item.pid as pid',
                $DB->raw('avg(exam_score.score) as scoreAvg'),
                $DB->raw('count(exam_result.student_id) as studentQuantity')
            )->get();

        $pid = $data->pluck('pid');

        //获取考核点名称
        if(is_object($pid)){
            $standardModel = new StandardItem();
            $content = $standardModel->whereIn('id', $pid)->select('id','content')->get();
            $contentTwo = $standardModel->whereIn('pid', $pid)->select($DB->raw('count(pid) as pidNum'))->groupBy('pid')->get();

            foreach($data as $k=>$v){
                $data[$k]['standardContent'] = $content[$k]['content'];
                $data[$k]['studentQuantity'] = $data[$k]['studentQuantity']/$contentTwo[$k]['pidNum'];
            }
        }
        return  $data;
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
    public function GetStandardDetails1($standardPid){
        $DB = \DB::connection('osce_mis');
        $builder = $this->StandardItemModel->where('pid','=',$standardPid)->get();
        $examScore = new ExamScore();
        $id = $this->GetIdArr($builder);
        if(count($id)>0){
            $grade = $examScore->whereIn('standard_item_id', $id)->get();
            dd($grade);
        }

        dd($builder);
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

    public function GetIdArr($StandardData){
        $idArr = [];
        if(count($StandardData)>0){
            foreach($StandardData as $v){
                $idArr[] = $v['id'];
            }
        }
        return  $idArr;
    }
}