<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/24 0024
 * Time: 11:33
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamSpecialScore;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\SubjectStandard;
use Modules\Osce\Http\Controllers\CommonController;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Illuminate\Http\Request;

class ExamScoreCorrectedController extends  CommonController
{
    //添加考核点里的折算率
    public  function getStandardCorrected ()
    {
        //拿到考试下所有的考站和科目
        $examId = 3;
        $examDraftModel = new ExamDraft();
        $examDraftData = $examDraftModel->getDraftFlowData($examId);
        foreach ($examDraftData as $item) {
            if ($item->station_type == 1) {
                //拿到考核点所有考核项
                $StandardData = StandardItem::where('standard_id', '=', $item->standard_id)->get();
                $pidScore = 0;
                $valueObject = [];
                foreach ($StandardData as $value) {
                    if ($value->pid == 0) {
                        //技能站折算率为20除以所有考点的和
                        $pidScore = $pidScore + $value->score;
                        $valueObject [] = $value;
                    }
                }
                //技能站折算率为20除以所有考点的和
                $coefficient = 20 / $pidScore;
                foreach ($valueObject as $object) {
                    $object->coefficient = $coefficient;
                    if (!$object->save()) {
                        throw new \Exception('新增折算率失败');
                    }
                }
            }
            // sp 考站分两个方面
            if ($item->station_type == 2) {
                $StandardData = StandardItem::where('standard_id', '=', $item->standard_id)->get();
                $data = [];
                foreach ($StandardData as $spValue) {
                    $data[$spValue->pid][] = $spValue;
                }
                $return = [];
                foreach ($data[0] as $proint) {
                    $prointData = $proint;
                    if (array_key_exists($proint->id, $data)) {
                        $prointData['test_term'] = $data[$proint->id];
                    } else {
                        $prointData['test_term'] = [];
                    }

                    $return[] = $prointData;
                }
                foreach ($return as $value){
                    if($value->sort == 1 || $value->sort == 2){

                    }

                }
            }


        }

    }



    //向exam_score表里添加折算成绩
    //url osce/admin/exam-score-corrected/grade-corrected
    public  function getGradeCorrected(Request $request){
        set_time_limit(0);
        $exam_id =3;
     //拿到考试下所有的考试科目
        $examRoomList= ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->select([
                'exam_draft.subject_id as subject_id',
            ])
            ->groupBy('exam_draft.station_id')
            ->get();
//    //拿到所评分详情
        foreach($examRoomList as $subjectId){

            if($subjectId->subject_id != null){
                //把科目下所有成绩详情取出
                $examScoreModel = new ExamScore();
                $examScoreData = $examScoreModel->getExamScoreData($subjectId->subject_id);
                foreach ($examScoreData as $item){
                    $standard = StandardItem::find($item->standard_item_id);
                    //拿到折算率
                    $convert =StandardItem::find($standard->pid);

                    //折算分数
                    $item ->convert_score = $item->score*$convert->coefficient;
                    if(!$item->save()){
                        throw  new \Exception('折算评分详情出错');
                    }

//            dd($convert->coefficient);
                }
            }

        }


        
    }

    //拿到总分的折算分
    //url osce/admin/exam-score-corrected/score-corrected
    public function getScoreCorrected(){
    //根据考试结果id拿到折算总分
        $resultId = ExamResult::all();

    //拿到评分详情里所有的结果

      foreach ($resultId as $item){

       $ExamScore =  ExamScore::where('exam_result_id','=' ,$item->id)->get()->pluck('convert_score')->toArray();
        $station = Station::find($item->station_id);
          $CorrectedScore = 0;
        if($station ->type ==3){
            $CorrectedScore =$item->score;
        }else{
            foreach ($ExamScore as $value){
                $CorrectedScore += $value;
            }
        }
          //拿到特殊评分项
          $special = ExamSpecialScore::where('exam_result_id','=',$item->id)->get();
         if(!$special->isEmpty()){
            //拿到该科目的折算率
             $subjectId = $special->first();
             $Standard = SubjectStandard::where('subject_id','=',$subjectId->subject_id)->first();
             $convert = StandardItem::where('standard_id','=',$Standard->standard_id)->where('pid','=',0)->first();
             $CorrectedScore = ($item->score)*$convert->coefficient;
         }
          $item ->convert_score = round($CorrectedScore,2);
          if(!$item ->save()){
              throw  new \Exception('折算总分出错');
          }
      }


    }





}