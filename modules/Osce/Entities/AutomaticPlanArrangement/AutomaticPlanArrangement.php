<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/16
 * Time: 13:58
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Student;

class AutomaticPlanArrangement
{
    //考试学生数量
    protected $_S_Count = 0;

    //考站数量
    protected $_T_Count = 0;

    //考站数组
    protected $_T = [];

    //考生数组
    protected $_S = [];

    //考试顺序规则
    protected $_TS = [];

    //已考过1科目或以上的学生，优先从这个队列获取数据
    protected $_S_ING = [];

    //考站队列
    protected $_Q_STA = [];

    /*
     * 该考试对应的考试场次
     */
    protected $_Screen = '';

    /*
     * 考试实例
     */
    protected $_Exam = '';

    function __construct($examId, ExamPlaceEntityInterface $examPlaceEntity, ExamInterface $exam)
    {
        /*
         * 初始化属性
         */
        $this->_Exam = Exam::findOrFail($examId);
        $this->_T_Count = count($examPlaceEntity->stationTotal($examId));
        $this->_T = $examPlaceEntity->stationTotal($examId);
        $this->_S_Count = count(Student::examStudent($examId));
        $this->_S = Student::examStudent($examId);
        $this->screen = $exam->screenList($examId);

        /*
         * 设置考试实体的状态为0
         */
        foreach ($this->_T as &$item) {
            $item['status'] = 0;
        }

        /*
         * 将考试实体进行分组
         */
        $this->_TS = $this->_T->groupBy('serialnumber');
    }

    /**
     * 智能排考
     * @author Jiangzhiheng
     * @time
     */
    function plan($examId)
    {
        /*
         * 依靠场次清单来遍历
         */
        foreach ($this->screen as $item) {
            $this->screenPlan($item);
        }
    }

    private function screenPlan($item) {
        /*
         * 获得场次的开始和结束时间
         */
        $beginDt = strtotime($item->begin_dt);
        $endDt = strtotime($item->end_dt);

        //得到流程所需的时间


        if () {

        }
    }
}