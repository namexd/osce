<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2016/04/01 10:53
 * Time: 14:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\ExamOrder;
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
        $data=$this->getExamMonitorListByStatus(1)->toArray();

        return view('osce::admin.testMonitor.monitor_late', [
             'data'      =>$data['data']
        ]);
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
        $data=$this->getExamMonitorListByStatus(2);
        return view('osce::admin.testMonitor.monitor_replace', [
             'data'      =>$data['data']
        ]);
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
        $data=$this->getExamMonitorListByStatus(3)->toArray();
        return view('osce::admin.testMonitor.monitor_abandom', [
            'data'      =>$data['data']

        ]);
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
        $data=$this->getExamMonitorListByStatus(4)->toArray();
        return view('osce::admin.testMonitor.monitor_complete ', [
            'data'      =>$data['data']

        ]);

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
        $exam_id=Exam::where('status',1)->pluck('id');//正在考试id
        if(empty($exam_id)) return [];

            switch ($status){
                case 1://迟到
                   return Student::leftJoin('exam_order', function($join){
                        $join -> on('exam_order.student_id', '=', 'student.id');
                    })-> leftJoin('exam_screening_student', function($join){
                        $join -> on('exam_screening_student.student_id', '=', 'student.id');
                    })->select('student.name', 'student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence','exam_screening_student.status')
                        ->where('exam_order.status',4)->where('student.exam_id',$exam_id)->where('exam_order.exam_id',$exam_id)->paginate(config('osce.page_size'));
                    break;
                case 2://替考
                    $list=ExamScreeningStudent::leftJoin('student', function($join){
                        $join -> on('exam_screening_student.student_id', '=', 'student.id');
                    })->select('student.name','student.exam_id','student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence','exam_screening_student.status')
                      ->where('student.exam_id',$exam_id)
                      ->where('exam_screening_student.is_replace',1)
                      ->groupBy('exam_screening_student.student_id')
                      ->paginate(config('osce.page_size'));
                    if(empty($list->toArray()['data'])){return [];}
                    $list=$list->toArray()['data'];
                    foreach($list as $key=>$v){ //替考学生
                        $replaceList=ExamScreeningStudent::where('student_id',$v['student_id'])->get()->toArray();
                        $station_name=[];
                            foreach($replaceList as $val){
                                $station_names=ExamScreeningStudent::leftJoin('exam_screening', function ($join) {
                                                $join->on('exam_screening_student.exam_screening_id', '=', 'exam_screening.id');
                                            })->leftJoin('station_teacher', function ($join) {
                                                $join->on('exam_screening_student.exam_screening_id', '=', 'station_teacher.exam_screening_id');
                                            })->leftJoin('station', function ($join) {
                                                $join->on('station.id', '=', 'station_teacher.station_id');
                                            })->where('exam_screening_student.exam_screening_id',$val['exam_screening_id'])
                                              ->where('station_teacher.exam_id',$v['exam_id'])->select('station.name')
                                              ->first();
                                if(!empty($station_names)) $station_name[]=$station_names->toArray()['name'];
                            }

                        $list[$key]['station_name']=count($station_name)?implode(',',$station_name):'';
                        //\DB::connection('osce_mis')->enableQueryLog();
                     //  $queries = \DB::connection('osce_mis')->getQueryLog();
                    }
                        return $list;
                    break;
                case 3://弃考
                    $builder=ExamScreeningStudent::leftJoin('student', function($join){
                        $join -> on('exam_screening_student.student_id', '=', 'student.id');
                    })->leftJoin('exam_station', function($join){
                        $join -> on('exam_station.exam_id', '=', 'student.exam_id');
                    })->leftJoin('station', function($join){
                        $join -> on('exam_station.station_id', '=', 'station.id');
                    })->select('student.name','student.exam_id','station.id as station_id', 'student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence','exam_screening_student.status','station.name as station_name');

                    return $builder->where('exam_screening_student.status',1)->paginate(config('osce.page_size'));
                    break;
                case 4://已完成
                    //return $builder->where('exam_screening_student.is_end',2)->paginate(config('osce.page_size'));
                    break;
                default:
                    return [];
                    break;
            }
    }
}


















