<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@sulida.com>
 * @date 2016-02-23 14:28
 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Entities\Drawlots\Station;
use Modules\Osce\Entities\ExamPaper;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Station as OsceStation;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Illuminate\Http\Request;
use Modules\Osce\Repositories\TestScoreRepositories;

/**
 * Class SubjectStatisticsController
 * @package Modules\Osce\Http\Controllers\Admin
 */
class SubjectStatisticsController  extends CommonController
{

    /**
     * 科目成绩分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/subject-grade-list
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author yangshaolin <yangshaolin@sulida.com>   <fandian@sulida.com>
     * @date    2016年2月23日15:43:34                    2016-07-06
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function SubjectGradeList(Request $request, SubjectStatisticsRepositories $subjectStatisticsR)
    {
         $examid=\Input::get('id');
        //\DB::connection('osce_mis')->enableQueryLog();
        //获取所有场次ID（包括子考试的场次ID）TODO:fandian 2016-06-22
        list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examid);
        //查询统计所需数据
        $rew = $subjectStatisticsR->getSubjectStatisticsList($screening_ids, $elderExam_ids);

        //$queries = \DB::connection('osce_mis')->getQueryLog();
        $subje_title = $rew->pluck('title')->toArray();
        $paper_title = $rew->pluck('paper_title')->toArray();
        $standardStr = implode(',', array_merge(array_diff($subje_title, $paper_title), array_diff($paper_title, $subje_title)));   //去掉对应的空值，组合成新的数组，再组成字符串
//        $timeAvgStr  = implode(',', $rew->pluck('timeAvg')->toArray());
        $scoreAvgStr = implode(',', $rew->pluck('scoreAvg')->toArray());

        //统计合格率
        foreach($rew as $key => $value)
        {
            //查询成绩合格的人数
            $result = $subjectStatisticsR->getViaStudentNum($screening_ids, $value->station_id, $value->rate_score, $value->paper_id);
            //计算合格率
            $rew[$key]['qualifiedPass'] = $value->studentQuantity? sprintf("%.2f", ($result->via/$value['studentQuantity'])*100).'%' : '0%';
        }

        //考试的下拉菜单（父ID=0，考试状态=[1,2]）
        $examlist = $subjectStatisticsR->GetExamList(0);

        if($request->ajax()){
            return $this->success_data([
                'list'=>$rew, 'standardStr'=>$standardStr, 'scoreAvgStr'=>$scoreAvgStr
            ], 1, '成功');
        }
        return view('osce::admin.statisticalAnalysis.statistics_subject', ['examlist'  => $examlist]);
    }

    /**
     * 科目难度分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/subject-analyze
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author yangshaolin <yangshaoliin@sulida.com>
     * @date    2016年3月4日10:08:52
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function  SubjectGradeAnalyze(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        $subjectId = \Input::get('id');     //考试项目ID
        $sign      = \Input::get('sign');   //用于区分是考试项目，还是考卷
        //查询分析所需数据
        list($SubjectDiff,$examId)= $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList([],$subjectId, $sign);
        //主要用来统计合格的人数

        list($rewTwo) = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList($examId,$subjectId, $sign , true);
        $standardStr = '';
        $qualifiedPass='';
           foreach ($SubjectDiff as $key => $rewval){
            //给结果展示列表中序号列加入数据
               $rewval['number'] = $key + 1;
               $rewval['qualifiedPass'] = '0';
               $rewval['ExamBeginTime'] = substr($rewval['ExamBeginTime'],0,10);
                   foreach ($rewTwo as $rewTwoval){
                       if ($rewval['ExamId'] == $rewTwoval['ExamId']) {
                           $rewval['qualifiedPass'] = $rewval['studentQuantity']?sprintf("%.2f", ($rewTwoval['studentQuantity'] / $rewval['studentQuantity']) * 100):0;
                       }
                   }

               if ($standardStr) {
                   $standardStr    .= ',' .substr($rewval['ExamBeginTime'],0,7);
                   $qualifiedPass  .= ','.$rewval['qualifiedPass'];
               } else {
                   $standardStr    .= substr($rewval['ExamBeginTime'],0,7);
                   $qualifiedPass  .= $rewval['qualifiedPass'];
               }
               $rewval['qualifiedPass'] = $rewval['qualifiedPass'].'%';
           }
        $StrList = [
            'standardStr'   => $standardStr,
            'qualifiedPass' => $qualifiedPass
        ];
  
        //获取有效的考试项目
        $ExamList = $subjectStatisticsRepositories->GetExamList(0);
        $SubjectList = [];
        if(count($ExamList)>0){
            $SubjectList = $subjectStatisticsRepositories->subjectDownlist($ExamList->pluck('id'));
        }
        //ajax请求判断返回不同数据
        if ($request->ajax()) {
            return $this->success_data(['list' => $SubjectDiff, 'StrList' => $StrList]);
        }
        return view('osce::admin.statisticalAnalysis.statistics_subject_level', ['list' => $SubjectDiff, 'subjectList' => $SubjectList, 'StrList' => $StrList]);
    }

    /**考站成绩分析列表
     * @method GET
     * @url /osce/admin/subject-statistics/station-grade-list
     * @access public
     * @param Request $request
     * @param SubjectStatisticsController $subjectStatisticsRepositories
     * @param SubjectStatisticsRepositories $subject
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@sulida.com>
     * @date 2016年3月4日10:08:43
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function stationGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsR)
    {
        //验证
        $this->validate($request, [
            'examId'    => 'sometimes', //考试编号
            'subjectId' => 'sometimes', //科目编号
            'sign'      => 'sometimes', //区分项目 跟 考卷
        ]);

        $examInfo    = $subjectStatisticsR->GetExamList(0);                     //获取考试列表信息

        //获取sign（sign为 subject 表示为考试项目，sign为paper 表示为考卷）
        $sign   = ($request->input('sign'))? : 'subject';                       //默认为考试项目
        $examId = intval($request->input('examId'))? : $examInfo->first()->id;  //获取考试ID

        $subjectList = $subjectStatisticsR->subjectDownlist($examId);           //获取考试项目、考卷下拉列表
        //获取考试项目ID
        $subjectId   = intval($request->input('subjectId'))? : ($subjectList->isEmpty() ? null:$subjectList->first()->id);

        //获取考站分析列表
        $list   = $subjectStatisticsR->GetSubjectStationStatisticsList($examId, $subjectId, $sign);

        $stationNameStr = implode(',', $list->pluck('stationName')->toArray()); //取出考站名称以逗号隔开组合字符串
        $scoreAvgStr    = implode(',', $list->pluck('scoreAvg')->toArray());    //取出平均分以逗号隔开组合字符串

        //异步请求，返回数组
        if ($request->ajax()) {
            return $this->success_data([
                'stationList'=>$list->toArray(), 'stationNameStr'=>$stationNameStr, 'scoreAvgStr'=>$scoreAvgStr
            ]);
        }

        //将数据展示到页面
        return view('osce::admin.statisticalAnalysis.statistics_subject_examation', [
            'examInfo'      => $examInfo ,          //考试列表
            'subjectInfo'   => $subjectList ,       //科目列表
            'stationList'   => $list->toArray(),    //考站成绩分析列表
            'examId'        => $examId,             //考试ID
            'subjectId'     => $subjectId           //科目id
        ]);
    }

    /**
     * 考站成绩分析详情
     * @url  GET osce/admin/subject-statistics/stationDetails
     * @access public
     * @param Request $request
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @return \Illuminate\View\View
     * @author xumin <xumin@sulida.com>   <fandian@sulida.com>
     * @date                                2016-06-23 14:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function stationDetails(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        //验证
        $this->validate($request, [
            'examId'    => 'required|integer',
            'subjectId' => 'required|integer',
            'stationId' => 'required|integer',
            'sign'      => 'sometimes'
        ]);

        $sign      = $request->input('sign');
        $examId    = $request->input('examId',0);       //获取考试id
        $subjectId = $request->input('subjectId',0);    //获取科目id
        $stationId = $request->input('stationId',0);    //获取考站id
//        dump($sign, $examId, $subjectId, $stationId);
        //获取考站成绩分析详情，以及主考试信息
        list($data, $examInfo) = $subjectStatisticsRepositories->GetStationDetails($examId, $subjectId, $stationId, $sign);

        //区分是考试项目，还是考卷
        if($sign == 'subject'){
            $subject = Subject::find($subjectId);
        }else{
            $subject = ExamPaper::find($subjectId);
        }
        $station = OsceStation::find($stationId);
        //头部数据详情
        $title['examName']      = $examInfo->examName;  //考试名称
        $title['time']          = $examInfo->begin_dt.'~'.$examInfo->end_dt;            //考试时间
        $title['subjectTitle']  = ($sign == 'subject')?$subject->title:$subject->name;  //考试项目名称
        $title['stationName']   = $station->name;       //考试时间

        //将数据展示到页面
        return view('osce::admin.statisticalAnalysis.statistics_subject_examation_detail', [
            'title'             => $title,  //头部数据详情
            'stationDetails'    => $data,   //考站详情
        ]);
    }



    /**考核点分析列表
     * @method GET
     * @url /osce/admin/subject-statistics/standard-grade-list
     * @access public
     * @param Request $request
     * @param SubjectStatisticsController $subjectStatisticsRepositories
     * @param SubjectStatisticsRepositories $subject
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@sulida.com> <fandian@sulida.com>重构
     * @date    2016年3月2日18:21:59       2016-06-23 9:10
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function standardGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsR)
    {
        //验证
        $this->validate($request, [
            'examId'    => 'sometimes',//考试编号
            'subjectId' => 'sometimes',//科目编号
        ]);
        //获取除开理论考试外的所有已经完成的考试信息
        $examInfo = $subjectStatisticsR->GetExamListNoStandardGrade();
        //获取考试ID，（未传考试ID，默认选考试列表第一个）
        $examId   = intval($request->input('examId'))? :($examInfo->isEmpty()? null:$examInfo->first()->id);

        //获取考试项目 下拉列表（除去 理论考试考卷）
        $subjectList = $subjectStatisticsR->subjectDownlist($examId, false);
        //获取考试项目数据，（未传考试项目ID，默认选考试项目列表第一个）
        $subjectId   = intval($request->input('subjectId'))? : ($subjectList->isEmpty() ? null:$subjectList->first()->id);

        //统计相关数据方便  下一步运算
        $rew = $subjectStatisticsR->GetSubjectStandardStatisticsList($examId, $subjectId);

//        //fandian
//        $pidArr = array_unique($rew->pluck('pid')->toArray());      //取出pid，转为数组，去重
//        sort($pidArr);      //重新排序
//        $sonRew = [];
//        if(count($pidArr) >1)
//        {
//            foreach ($pidArr as $item) {
//                $pidRate = StandardItem::where('id', '=', $item)->select('coefficient')->first()->coefficient;
//                //统计相关数据方便  下一步运算
//                $sonRew[$item]['pidRate']  = round($pidRate, 2);
//                $sonRew[$item]['standard'] = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList($examId, $subjectId, 0, $item);
//            }
//        }

        $datas = [];
        $standardContent = '';  //考核点
        $qualifiedPass   = '';  //合格率
        $number          = 1;   //序号
        //重构数组
        if(!empty($rew))
        {
            foreach($rew as $k => $v)
            {
                if($k>=1){
                    //证明是同一个考核点下的子考核点
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
                foreach($rew as $key => $val)
                {
                    //证明是同一个考核点下的子考核点
                    if($v['pid'] == $val['pid'])
                    {
                        //获取去对应考核点的折算率
                        $pidRate = StandardItem::where('id', '=', $val['pid'])->select('coefficient')->first()->coefficient;
                        $rew[$k]['studentCount'] = $rew[$k]['studentCount']+1;
                        $rew[$k]['studentTotalScore'] = $rew[$k]['studentTotalScore']+$val['score'];
                        if($val['Zscore'] != 0)
                        {
                            //求折算成绩合格率
                            if($val['score']/($val['Zscore']*$pidRate) >= 0.6){
                                $rew[$k]['studentQualifiedCount'] = $rew[$k]['studentQualifiedCount']+1;
                            }
                        }

                    }
                }
                //计算该考核点的平均分数
                $rew[$k]['studentAvgScore'] = sprintf("%.2f",$rew[$k]['studentTotalScore']/$rew[$k]['studentCount']);
                //计算该考核点的合格率
                $rew[$k]['studentQualifiedPercentage'] = sprintf("%.4f",$rew[$k]['studentQualifiedCount']/$rew[$k]['studentCount'])*100;
                //获取该考核点名称

                $content = StandardItem::where('id','=',$v['pid'])->select('content')->first();

                $content = !empty($content)?$content['content']:'-';
                $datas[] = [
                    'number'            => $number++,   //序号
                    'standardContent'   => $content,    //考核点名称
                    'pid'               => $v['pid'],   //评分标准父编号
                    'scoreAvg'          => $rew[$k]['studentAvgScore'], //平均成绩
                    'studentQuantity'   => $rew[$k]['studentCount'],    //考试人数
                    'qualifiedPass'     => $rew[$k]['studentQualifiedPercentage'].'%',  //合格率
                ];

                if($standardContent){
                    $standardContent .= ','.$content;
                    $qualifiedPass   .= ','.$rew[$k]['studentQualifiedPercentage'];
                }else{
                    $standardContent .= $content;
                    $qualifiedPass   .= $rew[$k]['studentQualifiedPercentage'];
                }
            }
        }

        $StrList = [
            'standardContent'   => $standardContent,
            'qualifiedPass'     => $qualifiedPass,
        ];
        if ($request->ajax()) {
            return $this->success_data(['standardList' => $datas, 'StrList' => $StrList]);
        }

        //将数据展示到页面
        return view('osce::admin.statisticalAnalysis.statistics_subject_standard', [
            'examInfo'      => $examInfo->toArray(),    //考试列表
            'subjectInfo'   => $subjectList->toArray(), //科目列表
            'standardList'  => $datas,                  //考核点分析列表
            'StrList'       => $StrList,
        ]);
    }
    /**考核点查看（详情）
     * @method GET
     * @url  /osce/admin/subject-statistics/standardDetails
     * @access public
     * @param Request $request
     * @param SubjectStatisticsController $subjectStatisticsRepositories
     * @return string
     * @author xumin <xumin@sulida.com>
     * @date    2016年3月2日18:21:51
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function standardDetails(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        //验证
        $this->validate($request, [
            'examId'        => 'required|integer',
            'subjectId'     => 'required|integer',
            'standardPid'   => 'required|integer'
        ]);

        $standardPid = $request->input('standardPid',0);
        $examId      = $request->input('examId',0);
        $subjectId   = $request->input('subjectId',0);

        //获取去对应考核点的折算率
        $pidRate = StandardItem::where('id', '=', $standardPid)->select('coefficient')->first()->coefficient;
        //查询考核点分析所需数据
        $result = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList($examId, $subjectId, $standardPid);//558

        $datas = [];
        $number = 1;//序号
        //重构数组
        if(!empty($result)){
            foreach($result as $k => $v){
                if($k>=1){
                    //证明是同一个考核点下的子考核点
                    if($result[$k]['standard_item_id'] == $result[$k-1]['standard_item_id']){
                        continue;
                    }
                }

                //统计该考核点的人数
                $result[$k]['studentCount'] = 0;
                //统计该考核点的总分数
                $result[$k]['studentTotalScore'] = 0;
                //统计该考核点的平均分数
                $result[$k]['studentAvgScore'] = 0;
                //统计该考核点的合格人数
                $result[$k]['studentQualifiedCount'] = 0;
                //统计该考核点的合格率
                $result[$k]['studentQualifiedPercentage'] = 0;
                foreach($result as $key => $val){
                    //证明是同一个考核点下的子考核点
                    if($v['standard_item_id'] == $val['standard_item_id']){
                        $result[$k]['studentCount'] = $result[$k]['studentCount']+1;
                        $result[$k]['studentTotalScore'] = $result[$k]['studentTotalScore']+$val['score'];
                        if($val['Zscore'] != 0){
                            if($val['score']/($val['Zscore']*$pidRate) >= 0.6){
                                $result[$k]['studentQualifiedCount'] = $result[$k]['studentQualifiedCount']+1;
                            }
                        }
                    }
                }
                //计算该考核点的平均分数
                $result[$k]['studentAvgScore'] = sprintf("%.2f",$result[$k]['studentTotalScore']/$result[$k]['studentCount']);
                //计算该考核点的合格率
                $result[$k]['studentQualifiedPercentage'] = sprintf("%.4f",$result[$k]['studentQualifiedCount']/$result[$k]['studentCount'])*100;
                //获取该考核点名称
                $content = StandardItem::where('id','=',$v['standard_item_id'])->select('content')->first();

                $datas[] = [
                    'number'            => $number++,//序号
                    'standardContent'   => !empty($content)?$content['content']:'-',//考核点名称
                    'id'                => $v['standard_item_id'],//评分标准父编号
                    'scoreAvg'          => $result[$k]['studentAvgScore'],//平均成绩
                    'studentQuantity'   => $result[$k]['studentCount'],//考试人数
                    'qualifiedPass'     => $result[$k]['studentQualifiedPercentage'].'%',//合格率
                ];
            }
        }

        return view('osce::admin.statisticalAnalysis.statistics_subject_standard_detail', [
            'datas'=>$datas
        ]);
    }


    /**根据考试id获取对应的科目
     * @method GET
     * @url /osce/admin/subject-statistics/get-subject
     * @access public
     * @param Request $request
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@sulida.com>
     * @date    2016年3月2日14:09:32
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getSubject(Request $request,TestScoreRepositories $TestScoreR)
    {
        //验证
        $this->validate($request, [
            'exam_id'   => 'required|integer',
            'sign'      => 'sometimes'
        ]);

        $examId = $request->input('exam_id');
        $sign   = $request->input('sign')? false:true;

        try {
            list($subjectList, $paperList) = $TestScoreR->getSubjectPaperList($examId, $sign);
            //将考卷数组 与 考试项目数组合并
            $subjectList = array_merge($subjectList, $paperList);

            return response()->json($this->success_data($subjectList));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }



}