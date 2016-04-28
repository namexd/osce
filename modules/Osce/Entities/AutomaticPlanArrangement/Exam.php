<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/15
 * Time: 15:48
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;

use Modules\Osce\Entities\Exam as ExamModel;

class Exam implements ExamInterface
{
    /**
     * 保存考试的实例
     */
    protected $exam = '';

    /**
     * 构造方法，保存传入的实例
     * Exam constructor.
     * @param ExamModel $exam
     */
//    function __construct(ExamModel $exam)
//    {
//        $this->exam = $exam;
//    }

    /** 
     * 获取场次列表
     * @param $examId
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-02-15 15:55:55
     */
    function screenList($examId)
    {
        return \Modules\Osce\Entities\Exam::findOrFail($examId)->examScreening;
    }

    /**
     * 获取当前正在进行考试
     * @author Jiangzhiheng
     * @time 2016-02-15 15:59:41
     */
    static function getExaming($examId)
    {
        return ExamModel::doingExam($examId);
    }

    /*
     * 开始考试
     * 修改状态
     */
    function beginExam($examId)
    {
        try {
            return $this->exam->beginExam($examId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /*
     * 结束考试
     * 修改状态
     */
    function endExam($examId)
    {
        try {
            return $this->exam->endExam($examId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}