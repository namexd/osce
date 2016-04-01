<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2016/04/01 10:53
 * Time: 14:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Illuminate\Http\Request;
use Modules\Msc\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Repositories\TestScoreRepositories;
use Modules\Osce\Repositories\SubjectStatisticsRepositories;

class ExamMonitorController  extends CommonController
{
    /**
     * ������ڿ��ԵĿ��Լ���б�
     * @method GET
     * @url /osce/admin/exam-monitor/normal
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>get�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     *
     * @return view
     *
     * @version 3.3a
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-04-01 11:28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorNormalList () {
        
    }
}


















