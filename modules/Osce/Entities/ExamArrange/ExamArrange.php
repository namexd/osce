<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:35
 */

namespace Modules\Osce\Entities\ExamArrange;


use Modules\Osce\Entities\ExamArrange\Traits\SqlTraits;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Room;

class ExamArrange
{
    use SqlTraits;
    //清空考试安排
    public function getEmptyExamArrange($examId){
        
        //清除大站
        $ExamDraftFlowData = ExamDraftFlow::where('exam_id','=',$examId)->get();

        $FlowId = $ExamDraftFlowData->pluck('id');

        //删除小站
        if(ExamDraft::whereIn('exam_draft_flow_id',$FlowId)->delete()){
            foreach ($ExamDraftFlowData as $item){
                if(!$item -> delete()){
                    throw new \Exception('删除考试安排失败');
                }
            }
        }
        return true;
    }


    //清除考官安排
    public function getTeacherArrange($examId){
        $TeacherData  = StationTeacher::where('exam_id','=',$examId)->get();
        if($TeacherData){
           foreach ($TeacherData as $value){
               if(!$value -> delete()){
                   throw new \Exception('删除老师安排失败');
               }
           }
        }
        return true;
        
    }


    //归档考试邀请
    public function getTeacherInvite($examId){

        
        $TeacherInvite = Invite::where('exam_id','=',$examId)->get();
        foreach ($TeacherInvite as $item){
            $item->status =3;
            if(!$item -> save()){
                throw new \Exception('修改老师邀请失败');
            }
        }

        return true;
    }

    /**
     * 清除智能排考的数据
     * @param $examId
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15
     */
    public function resetSmartArrange($examId)
    {
        try {
            $this->emptyingPlan($examId);
            $this->emptyingPlanRecord($examId);
            $this->emptyingOrder($examId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    
    public function getInquireExamArrange($exam_id){

        $ExamDraftFlowData = ExamDraftFlow::where('exam_id','=',$exam_id)->get();
        $FlowId = $ExamDraftFlowData->pluck('id');
       $ExamDraft =  ExamDraft::whereIn('exam_draft_flow_id',$FlowId)->get();
        return [$ExamDraftFlowData ,$ExamDraft];
        
        


    }
    
    
    
}