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

    /**
     * ��óٵ��Ŀ��Լ���б�
     * @method GET
     * @url /osce/admin/exam-monitor/late
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
     * @date 2016-04-01 11:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorLateList () {

    }

    /**
     * ����濼�Ŀ��Լ���б�
     * @method GET
     * @url /osce/admin/exam-monitor/replace
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
     * @date 2016-04-01 11:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorReplaceList () {

    }

    /**
     * ��������Ŀ��Լ���б�
     * @method GET
     * @url /osce/admin/exam-monitor/quit
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
     * @date 2016-04-01 11:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorQuitList () {

    }

    /**
     * �������ɵĿ��Լ���б�
     * @method GET
     * @url /osce/admin/exam-monitor/finish
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
     * @date 2016-04-01 11:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorFinishList () {

    }

    /**
     * ��ÿ��Լ�ر�ͷ��Ϣ
     * @method GET
     * @url /osce/admin/
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>get�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     *
     * @return array
     *
     * @version 3.3a
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2016-04-01 11:41
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function getExamMonitorHeadInfo () {

    }
}


















