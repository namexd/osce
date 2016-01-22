<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:15
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;

class ExamFlow extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_flow';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','flow_id','created_user_id'];

    /**
     * 考试-流程节点-房间的关系
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function examFlowRoomRelation(){
        return $this->hasOne('\Modules\Osce\Entities\ExamFlowRoom','flow_id','flow_id');
    }

    public function examFlowStationRelation(){
        return $this->hasOne('\Modules\Osce\Entities\ExamFlowStation','flow_id','flow_id');
    }
    public function flow(){
        return $this->hasOne('\Modules\Osce\Entities\Flows','id','flow_id');
    }

    /**
     * 学生考试流程数量
     * @access public
     * @version 1.0
     * @author zhouqiang<zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function studentExamSum($examId)
    {
        //查到学生考试排序模式
        $SequenceMode = Exam::where('id', '=', $examId)->select('sequence_mode')->first();

        if ($SequenceMode->sequence_mode == 1) {
            //根据考场排序
            $studentExamSum = ExamFlowRoom::where('exam_id','=',$examId)->count();

        } else {
            //根据考站排序
            $studentExamSum = ExamFlowStation::where('exam_id','=',$examId)->count();

        }

        return $studentExamSum;

    }
}