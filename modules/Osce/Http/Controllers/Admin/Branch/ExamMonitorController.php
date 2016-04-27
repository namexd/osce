<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2016/04/01 10:53
 * Time: 14:30
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\Student;
use Modules\Osce\Http\Controllers\Api\IndexController;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Repositories\Common;

use Redis;

class ExamMonitorController  extends CommonController
{

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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-01 11:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorLateList () {
        $data=$this->getExamMonitorListByStatus(1);
        if(count($data)){
            $data=$data->toArray();
        }else{
            $data['data']=[];
        }
        $examControlModel = new ExamControl();
        $topMsg = $examControlModel->getDoingExamList();
        //dd($data['data']);
        return view('osce::admin.testMonitor.monitor_late', [
            'list'      =>$data['data'],'data'=>$topMsg
        ]);
    }
    /**
     * 迟到执行确认弃考
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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-11 11:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStopExam(Request $request)
    {
        $this->validate($request,[
            'examId'       => 'required|integer',//考试编号
            'studentId'    => 'required|integer',//考生编号
        ]);
        try {
            $examId = $request->input('examId'); //考试编号
            $studentId = $request->input('studentId'); //考生编号
            $examScreen = new ExamScreening();
            $roomMsg = $examScreen->getExamingScreening($examId);
            $roomMsg_two = $examScreen->getNearestScreening($examId);
            if ($roomMsg) {
                $screen_id = $roomMsg->id;
            } elseif ($roomMsg_two) {
                $screen_id = $roomMsg_two->id;
            } else {
                throw new \Exception('没有对应的考试场次');
            }

        $result=$this->getAbsentStudent($studentId, $examId, $screen_id); //插入缺考记录 学生已缺考


            if ($result == true) {
                $redis = Redis::connection('message');
                $redis->publish(md5($_SERVER['SERVER_NAME']) . 'watch_message', json_encode($this->success_data([], 1, '迟到确认弃考成功')));
                $redis->publish(md5($_SERVER['SERVER_NAME']) . 'pad_message', json_encode($this->success_data([], 1, '迟到确认弃考成功')));
                return response()->json(true);
            } else {
                return response()->json($result);
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
    private function getAbsentStudent($studentId, $examId, $screen_id)
    {
        $status = ExamOrder::where('student_id', $studentId)->where('exam_screening_id', $screen_id)
            ->where('exam_id',$examId)->select('status')->first()->status;
        if($status==4){
            $result = ExamOrder::where('student_id', $studentId)->where('exam_screening_id', $screen_id)
                ->where('exam_id',$examId)->update(['status'=>3]);
            if($result){
                // $screen_id=ExamScreening::where('exam_id',$examId)->where('status',1)->orderBy('begin_dt')->first()->id;
                $result = ExamAbsent::create([
                    'student_id'        => $studentId,
                    'exam_id'           => $examId,
                    'exam_screening_id' => $screen_id,
                ]);
                if($result){
                    //TODO zhoufuxiang
                    //获取该考试最后一位学生（按开始考试时间排序）, 若此学生与当前缺考学生是同一个，则将考试标为已结束
                    $examOrder = ExamOrder::where('exam_id', '=', $examId)->where('exam_screening_id', '=', $screen_id)
                        ->select(['begin_dt', 'student_id'])->orderBy('begin_dt', 'DESC')->first();
                    if($examOrder->student_id == $studentId){
                        //检查考试是否可以结束
                        $examScreening  = new ExamScreening();
                        $examScreening  ->getExamCheck();
                    }

                    return true;//缺考记录插入成功
                }
                return false;//缺考记录插入失败
            }
        }
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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-01 11:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorReplaceList () {
        $examControlModel = new ExamControl();
        $topMsg = $examControlModel->getDoingExamList();
        $data=$this->getExamMonitorListByStatus(2);
        $data=count($data)?$data:[];
        return view('osce::admin.testMonitor.monitor_replace', [
            'list'      =>$data,'data'=>$topMsg
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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-01 11:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorQuitList () {
        $data=$this->getExamMonitorListByStatus(3)->toArray();
        $examControlModel = new ExamControl();
        $topMsg = $examControlModel->getDoingExamList();
        return view('osce::admin.testMonitor.monitor_abandom', [
            'list'      =>$data['data'],'data'=>$topMsg]);
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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-01 11:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMonitorFinishList () {

        $data=$this->getExamMonitorListByStatus(4);
        if(count($data)){
            $data=$data->toArray();
        }else{
            $data['data']=[];
        }
        $examControlModel = new ExamControl();
        $topMsg = $examControlModel->getDoingExamList();
        return view('osce::admin.testMonitor.monitor_complete ', [
            'data'      =>$topMsg,'list'=>$data['data']

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
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-01 11:41
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function getExamMonitorHeadInfo (Request $request) {
         try {
        $this->validate($request,[
            'exam_id' => 'required|integer',
            'student_id' => 'required|integer',
        ]);
        //获取数据
        $examId = $request->input('exam_id');
        $studentId = $request->input('student_id');
        $stationId= ExamQueue::where('exam_id',$examId)->where('student_id',$studentId)->where('status',3)->get();//一个学生的所有考站
        if(count($stationId)) {
            foreach($stationId as $key=>$val){//获取对应考站的视频信息
                $stationId[$key]['name']=Station::where('id',$val->station_id)->pluck('name');
                $type=ExamMonitor::where('station_id',$val->station_id)->where('exam_id',$examId)->where('student_id',$studentId)->select('type')->first();//对应考站弃考替考类型
                $stationId[$key]['type']=empty($type)?'正常':($type->type==1?'替考':'弃考');
                $queueMsg=ExamQueue::where('station_id',$val->station_id)->where('exam_id',$examId)->where('student_id',$studentId)->where('status',3)->first();//考站考试时间
                if(!empty($queueMsg)){
                    $time=strtotime($queueMsg->end_dt)-strtotime($queueMsg->begin_dt);
                    $stationId[$key]['time']=$time>0?Common::handleTime($time):0;
                }
            }
        }else{
            throw new \Exception('没有对应的视频数据');
        }
        $topMsg=Student::leftJoin('exam', function($join){
            $join -> on('exam.id', '=', 'student.exam_id');
        })->select('student.id as student_id','student.name','exam.name as exam_name','student.exam_sequence','exam.id as exam_id')
         ->where('student.id',$studentId)->where('exam.id',$examId)->first();//页面顶部信息

        return view('osce::admin.testMonitor.monitor_check',['data'=>$topMsg,'list'=>$stationId]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
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
        /*$examScreen=new ExamScreening();
        $ExamScreening = $examScreen->getExamingScreening($exam_id);
        if (is_null($ExamScreening)) {
            $ExamScreening = $examScreen->getNearestScreening($exam_id);
        }*/
        $builder=ExamScreeningStudent::leftJoin('student', function($join){//弃考 已完成页面数据对象
            $join -> on('exam_screening_student.student_id', '=', 'student.id');
        })->select('student.name','student.exam_id', 'student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence','exam_screening_student.status');
        switch ($status){
            case 1://迟到
                return Student:: leftJoin('exam_order', function($join){
                    $join -> on('exam_order.student_id', '=', 'student.id');
                })->select('student.name','student.exam_id as examId', 'student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence')
                   // ->where('exam_absent.exam_screening_id',$ExamScreening->id)
                    ->where('student.exam_id',$exam_id)
                    //->where('exam_absent.exam_id',$exam_id)
                    ->where('exam_order.exam_id',$exam_id)
                    ->where('exam_order.status',4)
                    //->where('exam_screening_student.exam_screening_id',$ExamScreening->id)
                   // ->where('exam_screening_student.is_end',0)
                    ->paginate(config('osce.page_size'));


                break;
            case 2://替考
                $list=ExamMonitor::leftJoin('student', function($join){
                    $join -> on('exam_monitor.student_id', '=', 'student.id');
                })->select('student.name','student.exam_id','student.code','student.id as student_id','student.idcard','student.mobile','student.grade_class','student.teacher_name','student.exam_sequence')
                    ->where('exam_monitor.exam_id',$exam_id)
                   // ->where('exam_monitor.exam_screening_id',$ExamScreening->id)
                    ->where('exam_monitor.type',1)
                    ->where('exam_monitor.description',1)//已经确认替考的
                    ->groupBy('exam_monitor.student_id')
                    ->paginate(config('osce.page_size'));

                if(empty($list->toArray()['data'])){return [];}
                $list=$list->toArray()['data'];
                foreach($list as $key=>$v) { //替考学生
                    $replaceList=ExamMonitor::where('student_id',$v['student_id'])->where('exam_id',$exam_id)->where('exam_screening_id',$ExamScreening->id)->where('type',1)->get()->toArray();//上报停考信息
                    foreach($replaceList as $val){
                        $station_names=Station::where('id',$val['station_id'])->pluck('name');
                        if(!empty($station_names)) $station_name[]=$station_names;
                    }
                    $list[$key]['station_name']=count($station_name)?implode(',',$station_name):'';
                }
                return $list;
                break;
            case 3://弃考
                return $builder->where('exam_screening_student.status',1)
                               //->where('exam_screening_id',$ExamScreening->id)
                               ->where('student.exam_id',$exam_id)
                               ->paginate(config('osce.page_size'));
                break;
            case 4://已完成
                return $builder->where('exam_screening_student.is_end',1)
                               //->where('exam_screening_id',$ExamScreening->id)
                               ->where('student.exam_id',$exam_id)
                               ->paginate(config('osce.page_size'));
                break;
            default:
                return [];
                break;
        }
    }
}


















