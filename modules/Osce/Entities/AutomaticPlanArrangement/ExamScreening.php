<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/15
 * Time: 14:58
 */

namespace Modules\Osce\Entities\AutomaticPlanArrangement;

use Modules\Osce\Entities\ExamScreening as Screen;

class ExamScreening implements ExamScreeningInterface
{
    /*
     * 注入的$examScreening的实例
     */
    protected $screen = '';

    /*
     * 构造方法，将传入的$examScreening的实例保存
     */
    function __construct(Screen $examScreening)
    {
        $this->screen = $examScreening;
    }



    /*
     * 获取当前正在进行场次
     */
    function screening($examId)
    {
        return $this->screen->getExamingScreening($examId);
    }

    /*
     * 开始场次
     * 修改状态
     */
    function beginScreen($examId)
    {
        try {
            return $this->screen->beginScreen($examId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /*
     * 结束场次
     * 修改状态
     */
    function endScreen($examId)
    {
        try {
            return $this->screen->endScreen($examId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}