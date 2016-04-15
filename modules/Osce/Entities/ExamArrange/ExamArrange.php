<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:35
 */

namespace Modules\Osce\Entities\ExamArrange;


use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\StationTeacher;

class ExamArrange
{
    //清空考试安排
    public function getEmptyExamArrange($examId){
        
        //清除大站
        $ExamDraftFlowData = ExamDraftFlow::where('exam_id','=',$examId)->get();
        $FlowId = $ExamDraftFlowData->pluck('id');
        //删除小站
        if(ExamDraft::whereIn('exam_draft_flow_id',$FlowId)->get()){
            foreach ($ExamDraftFlowData as $item){
                if(!$item -> save()){
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
               if(!$value -> save()){
                   throw new \Exception('删除老师安排失败');
               }
           }
        }
        return true;
        
    }


    //归档考试邀请
    public function getTeacherInvite($examId){
        
        $TeacherInvite = Invite::where('exam_id'.'=',$examId)->get();
        foreach ($TeacherInvite as $item){
            $item->status =3;
            if(!$item -> save()){
                throw new \Exception('修改老师邀请失败');
            }
        }

        return true;
    }
}