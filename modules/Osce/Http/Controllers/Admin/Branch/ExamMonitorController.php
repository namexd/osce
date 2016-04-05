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
use Modules\Osce\Entities\ExamScreeningStudent;

class ExamMonitorController  extends CommonController
{
    /**
     * 获得正在考试的考试监控列表
     * @method GET
     * @url /osce/admin/exam-monitor/normal
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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
     * 获得迟到的考试监控列表
     * @method GET
     * @url /osce/admin/exam-monitor/late
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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
     * 获得替考的考试监控列表
     * @method GET
     * @url /osce/admin/exam-monitor/replace
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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
     * 获得弃考的考试监控列表
     * @method GET
     * @url /osce/admin/exam-monitor/quit
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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
     * 获得已完成的考试监控列表
     * @method GET
     * @url /osce/admin/exam-monitor/finish
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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
     * 获得考试监控表头信息
     * @method GET
     * @url /osce/admin/
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
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

    /**
     * 根据状态获得考试监控列表
     * @method GET
     * @url /osce/admin/
     * @access public
     * @param $status 1迟到 2替考 3弃考 4已完成
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return array
     *
     * @version 3.3a
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function getExamMonitorListByStatus($status){
        /*$builder=ExamScreeningStudent:: leftJoin('student', function($join){
            $join -> on('standard.id', '=', 'exam_score.standard_id');
        })-> leftJoin('station', function($join){
            $join -> on('station.subject_id', '=', 'exam_score.subject_id');
        })-> leftJoin('exam_result', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        });*/
            switch ($status){
                case 1://迟到
                   // ExamScreeningStudent::
                    break;
                case 2://替考

                    break;
                case 3://弃考

                    break;
                case 4://已完成

                    break;
                default:
                    return [];
                    break;
            }
    }
}


















