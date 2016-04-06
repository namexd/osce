<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;


/**考试监控
 * Class ExamControlController
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class ExamControlController extends CommonController
{
    /**正在考试列表
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamlist()
    {
        $examControlModel = new ExamControl();
        $data = $examControlModel->getDoingExamList();
       // dd($data);
        return view('osce::admin.testMonitor.monitor_test', [
            'data'      =>$data,
        ]);
    }


















}