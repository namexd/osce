<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:35
 */

namespace Modules\Osce\Entities\ExamArrange;


use Modules\Osce\Entities\Invite;
use Modules\Osce\Entities\StationTeacher;

class ExamArrange
{





    //清空考试安排
    public function getEmptyExamArrange($examId){
        



    }


    //清除考官安排
    public function getTeacherArrange($examId){
        
        if(!StationTeacher::where('exam_id','=',$examId)->delete()){
            throw new \Exception('删除老师安排失败');
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