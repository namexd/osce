<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/15
 * Time: 16:34
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;


use Modules\Osce\Entities\ExamPlanRecord;

class Student implements StudentInterface
{
    /*
     * 姓名
     */
    public $name = '';

    /*
     * 编号
     */
    public $code = '';

    /*
     * 考生状态 1=考试中  0=空闲  2=考试已安排（准备中）
     */
    protected $status = 0;

    /*
     * 记录$examPlanRecord实例
     */
    protected $examPlanRecord = '';

    /*
     * 考试科目
     */
    protected $exam_progress=[];

    /**
     * 构造函数，初始化某个学生的基本信息
     * Student constructor.
     * @param $name
     * @param $code
     * @param ExamPlanRecord $examPlanRecord
     */
    function __construct($name, $code, ExamPlanRecord $examPlanRecord)
    {
        $this->code = $code;
        $this->name = $name;
        $this->examPlanRecord = $examPlanRecord;
    }

    /**
     * 获取学生状态 此应该需要从数据表中找到当前学生是否正在考试中
     * @author Jiangzhiheng
     * @time
     */
    function getStatus()
    {

    }

    //设置学生状态
    function setStatus()
    {

    }

    //获取学生考试进度
    function getProgress()
    {

    }

    //设置学生考试初始科目
    function setIniProgress()
    {

    }

    /*
     * 设置学生考试开始
     * 更新学生考试进度也需要写在这个方法里
     */
    function setExamStart()
    {

    }

    //设置学生考试结束
    function setExamEnd()
    {

    }
}