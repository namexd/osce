<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/26 0026
 * Time: 10:45
 */

namespace Modules\Osce\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

class StudentExamQueryController extends  CommonController
{
    /**
     * 获取考试 ，还回数据个页面
     * @method GET
     * @url /osce/wechat/student-exam-query/results-query-index
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public  function getResultsQueryIndex(Request $request){
        try{
            $user= Auth::user();
            if(empty($user)){
                throw new \Exception('当前用户未登陆');
            }
            //根据用户获得考试id
            $ExamIdList= Student::where('user_id','=',$user->id)->select('exam_id')->get();
            $list=[];
            foreach($ExamIdList as $key=>$data){
                $list[$key]=[
                      'exam_id'=>$data->exam_id,
                ];
            }
            $examIds = array_column($list, 'exam_id');
            $ExamModel = new Exam();
            $ExamList= $ExamModel->Examname($examIds);
            //根据考试id获取所有考试
            //dd($ExamList);
            return view('osce::wechat.resultquery.examination_list',['ExamList'=>$ExamList]);
        }catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取每场考试下的所有考站信息
     * @method GET
     * @url /osce/wechat/student-exam-query/every-exam-list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return json
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */



    public function getEveryExamList(Request $request){

        $this->validate($request,[
            'exam_id'=>'required|integer'
        ]);
        $examId =Input::get('exam_id');
         //获取到考试的时间
        try{


        $examTime =Exam::where('id',$examId)->select('begin_dt','end_dt')->first();

        //根据考试id找到对应的考试场次
        $examScreeningId=  ExamScreening::where('exam_id','=',$examId)->select('id')->get();
        $examScreening=[];
        foreach($examScreeningId as $data){
            $examScreening[]=[
                'id'=>$data->id,
            ];
        }

        $examScreeningIds = array_column($examScreening, 'id');
        //根据场次id查询出考站的相关考试结果
        $ExamResultModel= new ExamResult();
        $stationList =$ExamResultModel->stationInfo($examScreeningIds);

        $stationData=[];
        foreach($stationList as $stationType){

            if($stationType->type == 2){
                 //获取到sp老师信息
                $teacherModel= new Teacher();
                $spteacher = $teacherModel->getSpTeacher($stationType->station_id);
            }
//
            $stationData[]=[
                'exam_result_id'=>$stationType->exam_result_id,
                'station_id'=>$stationType->id,
                'score'=>$stationType->score,
                'time'=>$stationType->time,
                'grade_teacher'=>$stationType->grade_teacher,
                'type'=>$stationType->type,
                'station_name'=>$stationType-> station_name,
                'sp_name'=>is_null($spteacher->name)? '-':$spteacher->name,
                'begin_dt'=>$examTime->begin_dt,
                'end_dt'=>$examTime->end_dt,
                'exam_screening_id'=>$stationType->exam_screening_id
            ];
        }
        return response()->json(
            $this->success_data($stationData,1,'数据传送成功')
        );
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 考生成绩查询详情页根据考站id查询
     * @method GET
     * @url /osce/wechat/student-exam-query/exam-details
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */



    public function  getExamDetails(Request $request)
    {

        $this->validate($request, [
            'exam_screening_id' => 'required|integer'
        ]);

        $examresultId=  intval(Input::get('exam_screening_id'));

         //根据考试场次id查询出该结果详情
        $examresultList=ExamResult::where('exam_screening_id','=',$examresultId)->first();


         //查询出详情列表
        $examscoreModel= new ExamScore();
        $examScoreList=$examscoreModel->getExamScoreList($examresultList->id);
        if(!$examScoreList){
            dd(111111);
            throw new \Exception('没有找到该考站成绩详情');

        }
        $groupData  =   [];
        foreach($examScoreList as $examScore){
            $groupData[$examScore->standard->pid][] =   $examScore;
        }
        $indexData  =   [];
        foreach($groupData[0] as $group)
        {
            $groupInfo  =   $group;
            $groupInfo['child'] =  $groupData[$group->standard->id];  //排序array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $data);
            $indexData[]    =   $groupInfo;
        }
        $list   =   [];

        foreach($indexData as $goupData)
        {
            $childrens  =   is_null($goupData['child'])? []:$goupData['child'];
            unset($goupData['child']);
            $list[] =   $goupData;

            foreach($childrens as $children)
            {
                $list[] =   $children;
            }
        }
//        dd($list);

        return view('osce::wechat.resultquery.examination_detail',['examScoreList'=>$list],['examresultList'=>$examresultList]);
    }











}