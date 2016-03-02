<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:28
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\MyRepositories;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Illuminate\Http\Request;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;

/**
 * Class SubjectStatisticsController
 * @package Modules\Osce\Http\Controllers\Admin
 */
class MyController  extends CommonController
{

    /**考站成绩分析列表
     * @method GET
     * @url /osce/admin/subject-statistics/station-grade-list
     * @access public
     * @param Request $request
     * @param MyRepositories $subjectStatisticsRepositories
     * @param SubjectStatisticsRepositories $subject
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function stationGradeList(Request $request,MyRepositories $subjectStatisticsRepositories,SubjectStatisticsRepositories $subject){
        //获取考试列表信息
        $examList = $subject->GetExamList();
        $examInfo = '';
        if(count($examList)>0){
            foreach($examList as $k=>$v){
                $examInfo[$k]['id'] = $v['id'];
                $examInfo[$k]['name'] = $v['name'];
            }
        }
        //获取科目列表信息
        $subjectList = $subjectStatisticsRepositories->GetSubjectList();
        $subjectInfo = '';
        if(count($subjectList)>0){
            foreach($subjectList as $k=>$v){
                $subjectInfo[$k]['id'] = $v->id;
                $subjectInfo[$k]['title'] = $v->title;
            }
        }
        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);
        $examId = $request->input('examId',count($examInfo)>0?$examInfo[0]['id']:0);

        $subjectId = $request->input('subjectId',0);
        //获取考站分析列表
        $list = $subjectStatisticsRepositories->GetSubjectStationStatisticsList($examId, $subjectId);
        $datas = [];
        $stationNameStr = '';
        $scoreAvgStr = '';
        if(count($list) > 0){
            foreach($list as $k=>$item){
                $datas[] = [
                    'number'           => $k+1,//序号
                    'stationId'        => $item->stationId,//考站id
                    'stationName'      => $item->stationName,//考站名称
                    'teacherName'      => $item->teacherName,//评分老师
                    'examMins'          => $item->examMins,//考试限时
                    'timeAvg'           => sprintf("%01.2f", $item->timeAvg),//平均耗时
                    'scoreAvg'          => sprintf("%01.2f", $item->scoreAvg),//平均成绩
                    'studentQuantity'  => $item->studentQuantity,//考试人数
                ];
                if($stationNameStr){
                    $stationNameStr .= ','.$item->stationName;
                    $scoreAvgStr .= ','.$item->scoreAvg;
                }else{
                    $stationNameStr .= $item->stationName;
                    $scoreAvgStr .= $item->scoreAvg;
                }
            }
        }

        $StrList = [
            'stationNameStr' => $stationNameStr,
            'scoreAvgStr' => $scoreAvgStr,
        ];

        if ($request->ajax()) {
            return $this->success_data(['stationList'=>$datas,'StrList'=>$StrList]);
        }
        $subjectList = $this->subjectDownlist($examId);
        $subjectInfo = count($subjectList)?$subjectList:$subjectInfo;
        //dd($datas);
        //将数据展示到页面
        return view('osce::admin.statisticalanalysis.statistics_examation', [
            'examInfo' =>$examInfo ,//考试列表
            'subjectInfo' =>$subjectInfo ,//科目列表
            'stationList'=>$datas, //考站成绩分析列表
            'StrList'=>$StrList
        ]);
    }

    /**考核点分析列表
     * @method GET
     * @url /osce/admin/subject-statistics/standard-grade-list
     * @access public
     * @param Request $request
     * @param MyRepositories $subjectStatisticsRepositories
     * @param SubjectStatisticsRepositories $subject
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardGradeList(Request $request,MyRepositories $subjectStatisticsRepositories,SubjectStatisticsRepositories $subject){
        //获取考试列表信息
        $examList = $subject->GetExamList();
        $examInfo = '';
        if(count($examList)>0){
            foreach($examList as $k=>$v){
                $examInfo[$k]['id'] = $v['id'];
                $examInfo[$k]['name'] = $v['name'];
            }
        }
        //获取科目列表信息
        $subjectList = $subjectStatisticsRepositories->GetSubjectList();
        $subjectInfo = '';
        if(count($subjectList)>0){
            foreach($subjectList as $k=>$v){
                $subjectInfo[$k]['id'] = $v->id;
                $subjectInfo[$k]['title'] = $v->title;
            }
        }

        //验证
        $this->validate($request, [
            'examId' => 'sometimes|int',//考试编号
            'subjectId' => 'sometimes|int',//科目编号
        ]);


        $examId = $request->input('examId',count($examInfo)>0?$examInfo[0]['id']:0);
        //获取考试项目数据
        $subjectList = $this->subjectDownlist($examId);
        $subjectInfo = count($subjectList)?$subjectList:$subjectInfo;

        $subjectId = $request->input('subjectId',count($subjectInfo)>0?$subjectInfo[0]['id']:0);

        //统计相关数据方便  下一步运算
        $rew = $subject->GetSubjectStandardStatisticsList($examId,$subjectId);

        $datas = [];
        $standardContent = '';//考核点
        $qualifiedPass = '';//合格率
        $number = 1;//序号
        //重构数组
        if(!empty($rew)){
            foreach($rew as $k => $v){
                if($k>=1){
                    if($rew[$k]['pid'] == $rew[$k-1]['pid']){
                        continue;
                    }
                }
                //统计该考核点的人数
                $rew[$k]['studentCount'] = 0;
                //统计该考核点的总分数
                $rew[$k]['studentTotalScore'] = 0;
                //统计该考核点的平均分数
                $rew[$k]['studentAvgScore'] = 0;
                //统计该考核点的合格人数
                $rew[$k]['studentQualifiedCount'] = 0;
                //统计该考核点的合格率
                $rew[$k]['studentQualifiedPercentage'] = 0;
                foreach($rew as $key => $val){
                        if($v['pid'] == $val['pid']){
                            $rew[$k]['studentCount'] = $rew[$k]['studentCount']+1;
                            $rew[$k]['studentTotalScore'] = $rew[$k]['studentTotalScore']+$val['score'];
                            if($val['Zscore'] != 0){
                                if($val['score']/$val['Zscore'] >= 0.6){
                                    $rew[$k]['studentQualifiedCount'] = $rew[$k]['studentQualifiedCount']+1;
                                }
                            }

                        }
                }
                //计算该考核点的平均分数
                $rew[$k]['studentAvgScore'] = sprintf("%.2f",$rew[$k]['studentTotalScore']/$rew[$k]['studentCount']);
                $rew[$k]['studentQualifiedPercentage'] = sprintf("%.4f",$rew[$k]['studentQualifiedCount']/$rew[$k]['studentCount'])*100;

                $content = $subject->GetContent($v['pid']);
                $datas[] = [
                    'number'               => $number++,//序号
                    'standardContent'     => $content,//考核点名称
                    'pid'                   => $v['pid'],//评分标准父编号
                    'scoreAvg'             => $rew[$k]['studentAvgScore'],//平均成绩
                    'studentQuantity'     => $rew[$k]['studentCount'],//考试人数
                    'qualifiedPass'       => $rew[$k]['studentQualifiedPercentage'].'%',//合格率
                ];

                if($standardContent){
                    $standardContent .= ','.$content;
                    $qualifiedPass .= ','.$rew[$k]['studentQualifiedPercentage'];
                }else{
                    $standardContent .= $content;
                    $qualifiedPass .= $rew[$k]['studentQualifiedPercentage'];
                }

            }
        }

        $StrList = [
            'standardContent' => $standardContent,
            'qualifiedPass' => $qualifiedPass,
        ];
        if ($request->ajax()) {
            return $this->success_data(['standardList'=>$datas,'StrList'=>$StrList]);
        }

        //将数据展示到页面
//        dd($datas);
        return view('osce::admin.statisticalanalysis.statistics_check', [
            'examInfo'      =>$examInfo ,//考试列表
            'subjectInfo' =>$subjectInfo ,//科目列表
            'standardList' =>$datas, //考核点分析列表
            'StrList'=>$StrList,
        ]);
    }
    /**考核点查看（详情）
     * @method GET
     * @url  /osce/admin/subject-statistics/standardDetails
     * @access public
     * @param Request $request
     * @param MyRepositories $subjectStatisticsRepositories
     * @return string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardDetails(Request $request,MyRepositories $subjectStatisticsRepositories){
        $standardPid = $request->input('standardPid',0);
        //查询考核点分析所需数据
        $result = $subjectStatisticsRepositories->GetStandardDetails(558);//558
        //所点击的考核点的子考核点对应的数据
        $datainfo=[];
        $content = '';//考核点
        $grade = '';//分数
        if(count($result)>0){
            foreach($result as $k=>$v){
                $datainfo[$k]['number'] = $k+1;//序号
                $datainfo[$k]['content'] = $v->content;//考核内容
                $datainfo[$k]['score'] = $v->score; //总分
                $datainfo[$k]['grade'] = $v->grade; //成绩

                if($content){
                    $content .= ','.$v->content;
                    $grade .= ','.$v->grade;
                }else{
                    $content .= $v-> content;
                    $grade .= $v->grade;
                }
            }
        }
        $StrList = [
            'content' => $content,
            'grade' => $grade,
        ];
        if ($request->ajax()) {
            return $this->success_data(['datainfo'=>$datainfo,'StrList'=>$StrList]);
        }
    }
    /**
     * 动态获取ajax列表
     * @author
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
            $subjectList = $this->subjectDownlist($examId);

            return response()->json($this->success_data($subjectList->toArray()));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 出科目的下拉菜单
     * @param $examId
     * @return array|\Illuminate\Support\Collection
     * @author Jiangzhiheng
     */
    private function subjectDownlist($examId)
    {
        /*
         * 给考试对应的科目下拉数据
         */
        $subjectIdList = StationTeacher::where('exam_id', $examId)
            ->groupBy('station_id')->get()->pluck('station_id');

        $stationList = Station::whereIn('id', $subjectIdList)->groupBy('subject_id')->get();

        $subjectList = [];
        foreach ($stationList as $value) {
            $subjectList[] = $value->subject;
        }

        $subjectList = collect($subjectList);

        return $subjectList;
    }
}
