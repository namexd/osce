<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-02-23 14:28
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Http\Controllers\Admin;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Subject;
use Illuminate\Http\Request;

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
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年2月23日15:43:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SubjectGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){

         $examid=\Input::get('id');
        //\DB::connection('osce_mis')->enableQueryLog();
        //查询统计所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid);
        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectStatisticsList($examid,true);
        //dd($rewTwo);
        //$queries = \DB::connection('osce_mis')->getQueryLog();
        $standardStr = '';
        $timeAvgStr = '';
        $scoreAvgStr = '';
       // $count= 0;
        //统计合格率
        foreach($rew as $key => $val){

            $rew[$key]['qualifiedPass'] = '0%';
            $rew[$key]['scoreAvg'] =sprintf('%.2f',$val['scoreAvg']);
            $rew[$key]['timeAvg'] =sprintf('%.2f',$val['timeAvg']);
            //给结果展示列表中序号列加入数据
            $rew[$key]['number']=$key+1;
            foreach($rewTwo as $v){
                if($val['subjectId'] == $v['subjectId']){
                    $rew[$key]['qualifiedPass'] = sprintf("%.2f", ($v['studentQuantity']/$val['studentQuantity'])*100).'%';
                }
            }
            if($standardStr){
                $standardStr .= ','.$val['title'];
                $timeAvgStr .= ','.$val['timeAvg'];
                $scoreAvgStr .= ','.$val['scoreAvg'];
            }else{
                $standardStr .= $val['title'];
                $timeAvgStr .= $val['timeAvg'];
                $scoreAvgStr .= $val['scoreAvg'];
            }
        }
        $StrList = [
            'standardStr' => $standardStr,
            'timeAvgStr' => $timeAvgStr,
            'scoreAvgStr' => $scoreAvgStr,
                  ];

        $exam = new Exam();
        $examlist= $exam->where('status','=','2')->select('id','name')->orderBy('end_dt','desc')->get()->toarray();

        if($request->ajax()){
            return $this->success_data(['list'=>$rew,'StrList'=>$StrList],1,'成功');
        }
        return  view('osce::admin.statisticalanalysis.statistics_subject',['examlist'=>$examlist,'StrList'=>$StrList,'list'=>$rew]);

    }

    public function SubjectGradeInfo(){
       dd('科目详情');
   }

    /**
     * 科目难度分析列表
     * @method  GET
     * @url /osce/admin/subject-statistics/subject-analyze
     * @access public
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author yangshaolin <yangshaoliin@misrobot.com>
     * @date    2016年2月29日14:20:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function  SubjectGradeAnalyze(request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        $subid = \Input::get('id');
        //查询分析所需数据
        $rew = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList($subid);

        //主要用来统计合格的人数
        $rewTwo = $subjectStatisticsRepositories->GetSubjectDifficultyStatisticsList($subid, true);
        //$queries = \DB::connection('osce_mis')->getQueryLog();

        $standardStr = '';
        $qualifiedPass='';
        foreach ($rew as $key => $val) {

            //给结果展示列表中序号列加入数据
            $rew[$key]['number'] = $key + 1;
            $rew[$key]['qualifiedPass'] = '0';
            $rew[$key]['scoreAvg'] =sprintf('%.2f',$val['scoreAvg']);
            $rew[$key]['timeAvg'] =sprintf('%.2f',$val['timeAvg']);
            $rew[$key]['ExamBeginTime'] = substr($val['ExamBeginTime'],0,7);

            foreach ($rewTwo as $v) {
                if ($val['ExamId'] == $v['ExamId']) {
                    $rew[$key]['qualifiedPass'] = sprintf("%.2f", ($v['studentQuantity'] / $val['studentQuantity']) * 100);
                }


            }
            if ($standardStr) {
                $standardStr .= ',' . $val['ExamBeginTime'];
                $qualifiedPass.=','.$val['qualifiedPass'];
            } else {
                $standardStr .= $val['ExamBeginTime'];
                $qualifiedPass.=$val['qualifiedPass'];
            }
             $val['qualifiedPass']=$val['qualifiedPass'].'%';

        }
        $StrList = [
            'standardStr' => $standardStr,
            'qualifiedPass'=>$qualifiedPass
        ];
        //获取有效的考试项目
        $ExamList = $subjectStatisticsRepositories->GetExamList();
        $SubjectList = [];
        if(count($ExamList)>0){
            $SubjectList = $subjectStatisticsRepositories->subjectDownlist($ExamList->pluck('id'));
        }
        //ajax请求判断返回不同数据
        if ($request->ajax()) {
            return $this->success_data(['list' => $rew, 'StrList' => $StrList]);
        }

        return view('osce::admin.statisticalanalysis.statistics_subject_level', ['list' => $rew, 'subjectList' => $SubjectList, 'StrList' => $StrList]);
    }

    /**考站成绩分析列表
     * @method GET
     * @url /osce/admin/subject-statistics/station-grade-list
     * @access public
     * @param Request $request
     * @param SubjectStatisticsController $subjectStatisticsRepositories
     * @param SubjectStatisticsRepositories $subject
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function stationGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //获取考试列表信息
        $examList = $subjectStatisticsRepositories->GetExamList();
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

        $subjectList = $subjectStatisticsRepositories->subjectDownlist($examId);
        $subjectInfo = count($subjectList)?$subjectList:$subjectInfo;

        $subjectId = $request->input('subjectId',count($subjectInfo)>0?$subjectInfo[0]['id']:0);
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

        //将数据展示到页面
        return view('osce::admin.statisticalanalysis.statistics_examation', [
            'examInfo' =>$examInfo ,//考试列表
            'subjectInfo' =>$subjectInfo ,//科目列表
            'stationList'=>$datas, //考站成绩分析列表
            'StrList'=>$StrList,
            'examId' =>$examId,//考试ID
            'subjectId' =>$subjectId //科目id
        ]);
    }

    /**考站成绩分析详情
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function stationDetails(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){

        $examId = $request->input('examId',0); //获取考试id
        $subjectId = $request->input('subjectId',0); //获取科目id
        $stationId = $request->input('stationId',0); //获取考站id
        $data = $subjectStatisticsRepositories->GetStationDetails(361,88,49);
        $stationDetails = [];//详情数据
        $title = '';//详情头部数据
        if(count($data)>0){
            foreach($data as $k=>$v){
                $stationDetails[$k]['number'] = $k+1;//编号
                $stationDetails[$k]['studentName'] = $v->studentName;//考生名字
                $stationDetails[$k]['begin_dt'] = $v->begin_dt;//考试时间
                $stationDetails[$k]['time'] = $v->time;//耗时
                $stationDetails[$k]['score'] = $v->score;//成绩
                $stationDetails[$k]['teacherName'] = $v->teacherName;//评价老师
            }
            $title['examName'] = $data[0]['examName'];//考试名称
            $title['time'] = $data[0]['begin_dt'].'~'.date('H:i:s',strtotime($data[0]['end_dt']));//考试时间
            $title['subjectTitle'] = $data[0]['subjectTitle'];//科目名称
            $title['stationName'] = $data[0]['stationName'];//考站名称
        }
        dd($stationDetails);
        //将数据展示到页面
        return view('osce::admin.statisticalanalysis.statistics_examation', [
            'title' =>$title ,//头部数据
            'stationDetails' =>$stationDetails ,//考站详情
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
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardGradeList(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){
        //获取考试列表信息
        $examList = $subjectStatisticsRepositories->GetExamList();
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
        $subjectList = $subjectStatisticsRepositories->subjectDownlist($examId);
        $subjectInfo = count($subjectList)?$subjectList:$subjectInfo;

        $subjectId = $request->input('subjectId',count($subjectInfo)>0?$subjectInfo[0]['id']:0);

        //统计相关数据方便  下一步运算
        $rew = $subjectStatisticsRepositories->GetSubjectStandardStatisticsList($examId,$subjectId);

        $datas = [];
        $standardContent = '';//考核点
        $qualifiedPass = '';//合格率
        $number = 1;//序号
        //重构数组
        if(!empty($rew)){
            foreach($rew as $k => $v){
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
                foreach($rew as $key => $val){
                    //证明是同一个考核点下的子考核点
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
                //计算该考核点的合格率
                $rew[$k]['studentQualifiedPercentage'] = sprintf("%.4f",$rew[$k]['studentQualifiedCount']/$rew[$k]['studentCount'])*100;
                //获取该考核点名称
                $content = $subjectStatisticsRepositories->GetContent($v['pid']);
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
     * @param SubjectStatisticsController $subjectStatisticsRepositories
     * @return string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function standardDetails(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories){

        $standardPid = $request->input('standardPid',0);
        //查询考核点分析所需数据
        $result = $subjectStatisticsRepositories->GetStandardDetails($standardPid);//558
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
     * @method GET
     * @url /osce/admin/subject-statistics/get-subject
     * @access public
     * @param Request $request
     * @param SubjectStatisticsRepositories $subjectStatisticsRepositories
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@misrobot.com>
     * @date    2016年3月2日14:09:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSubject(Request $request,SubjectStatisticsRepositories $subjectStatisticsRepositories)
    {
        //验证
        $this->validate($request, [
            'exam_id'=>'sometimes|integer'
        ]);

        $examId = $request->input('exam_id',"");

        try {
            $subjectList = $subjectStatisticsRepositories->subjectDownlist($examId);

            return response()->json($this->success_data($subjectList->toArray()));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }



}