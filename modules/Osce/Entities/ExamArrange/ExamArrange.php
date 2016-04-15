<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:35
 */

namespace Modules\Osce\Entities\ExamArrange;


use Modules\Osce\Entities\ExamArrange\Traits\SundryTraits;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Room;

class ExamArrange
{
    use SundryTraits;
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
        
        $TeacherInvite = Invite::where('exam_id'.'=',$examId)->get();
        foreach ($TeacherInvite as $item){
            $item->status =3;
            if(!$item -> save()){
                throw new \Exception('修改老师邀请失败');
            }
        }

        return true;
    }

    /**
     * 寻找相同的考站或考场
     * @param $result
     * @param $field
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-15 10:24
     */
    public function checkSameEntity($result, $field)
    {
        foreach ($result as $item) {
            $entityIds = $item->pluck($field);
            $uniEntityIdsIds = $entityIds->unique();
            if (count($entityIds) != count($uniEntityIdsIds)) {
                $entityId = $this->getDiff($entityIds, $uniEntityIdsIds);
                switch ($field) {
                    case 'station_id':
                        $entityName = Station::findOrFail($entityId)->name;
                        break;
                    case 'room_id':
                        $entityName = Room::findOrFail($entityId)->name;
                        break;
                    default:
                        throw new \Exception('系统异常，请重试');
                        break;
                }
                throw new \Exception('当前考试安排中' . $entityName . '出现了多次');
            }
        }
        return true;
    }
}