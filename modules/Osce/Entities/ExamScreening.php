<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 19:24
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamScreeningStudent;

class ExamScreening extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_screening';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['id', 'exam_id', 'room_id', 'begin_dt', 'end_dt', 'create_user_id', 'status',
                               'sort', 'total', 'nfc_tag', 'real_start_dt', 'gradation_order'];
    protected $statuValues  = [
        1 => '等候考试',
        2 => '正在考试',
        3 => '考试结束',
        4 => '未知状态',
    ];

    //关联考试表
    public function  ExamInfo()
    {
        return $this->belongsTo('Modules\Osce\Entities\Exam', 'exam_id', 'id');
    }

    public function roomsRelation()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamRoom', 'exam_id', 'exam_id');
    }

    public function invite()
    {
        return $this->hasMany('\Modules\Osce\Entities\Invite', 'exam_screening_id', 'id');
    }

    /**
     * todo 智能排考所用，请勿删除或修改 开始场次
     * @param $examId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-02-16 15:30:11
     */
    public function beginScreen($examId)
    {
        try {
            //获得当前需要开始的screen的实例
            $screen = $this->getNearestScreening($examId);
            //修改其状态值
            $screen->status = 1;
            //save it
            return $screen->save();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * todo 智能排考所用，请勿删除或修改 结束场次
     * @param $examId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-02-16 15:30:54
     */
    public function endScreen($examId)
    {
        try {
            //获得当前正在进行的screen的实例
            $screen = $this->getExamingScreening($examId);
            //修改其状态
            $screen->status = 2;
            //save it
            return $screen->save();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //根据考试id获得考站和老师数据
    public function getStationList($examId)
    {


//        $builder = $this->leftJoin (
//            'room',
//            function ($join) {
//                $join->on('room.id','=',$this->table.'.room_id');
//
//            }
//        )   ->  leftJoin (
//            'station',
//            function ($join) {
//                $join->on('station.room_id','=','room.id');
//            }
//        )   ->  leftJoin (
//            'station_case',
//            function ($join) {
//                $join->on('station_case.station_id','=','station.id');
//            }
//        )   ->  leftJoin (
//            'cases',
//            function ($join) {
//                $join->on('cases.id','=','station_case.case_id');
//            }
//        )   ->  leftJoin (
//            'station_sp',
//            function ($join) {
//                $join->on('station_sp.case_id','=','cases.id');
//            }
//        )->  leftJoin (
//            'teacher',
//            function ($join) {
//                $join->on('teacher.id','=','station_sp.user_id');
//            }
//        )
//            ->  where($this->table.'.id','=',$examId)
//            ->  select([
//                'station.id as station_id',
//                'station.name as station_name',
//                'teacher.name as teacher_name',
//                'teacher.id as teacher_id'
//            ]);
//        return $builder->get();

    }

    public function closeExam($exam_id)
    {

    }

    /**
     * 获取考试对应的场次列表 TODO 不一定会使用
     * @param $examId
     * @return mixed
     * @author Jiangzhiheng
     * @time
     */
    public function screeningList($examId)
    {
        return $this->where('exam_id', $examId)
            ->orderBy('begin_dt', 'asc')
            ->get();
    }

    public function getNearestScreening($exam_id)
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        $exam     = Exam::doingExam($exam_id);
        //\Log::debug('获取最近场次调试',[$exam_id,$exam]);


        if($exam->id != $exam_id){
            throw new \Exception('开考考试不对！');
        }
        $screenId = ExamPlan::where('exam_id', '=', $exam->id)->groupBy('exam_screening_id')->get()
                            ->pluck('exam_screening_id')->toArray();

        $result = $this->where('exam_id', '=', $exam_id)
                ->whereRaw("UNIX_TIMESTAMP(begin_dt) > UNIX_TIMESTAMP('$todayStart')
                          AND UNIX_TIMESTAMP(end_dt) < UNIX_TIMESTAMP('$todayEnd')")
                ->where('status', '=', 0)
                ->whereIn('id', $screenId)
                ->OrderBy('begin_dt', 'asc')
                ->first();

        return $result;
    }

    public function getExamingScreening($exam_id)
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        $result     = $this->where('exam_id', '=', $exam_id)
                    ->whereRaw("UNIX_TIMESTAMP(begin_dt) > UNIX_TIMESTAMP('$todayStart')
                              AND UNIX_TIMESTAMP(end_dt) < UNIX_TIMESTAMP('$todayEnd')")
                    ->where('status', '=', 1)       //等候考试
                    ->OrderBy('begin_dt', 'asc')
                    ->first();
        return $result;
    }



    /**
     *  结束考试
     * @method GET
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return json
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamCheck()
    {
        //取得考试实例
        $exam = Exam::doingExam();
        if (is_null($exam)) {
            throw new \Exception('没有找到考试');
        }

        //获取到当考试场次id
        $ExamScreening = $this->getExamingScreening($exam->id);
        if (is_null($ExamScreening)) {
            $ExamScreening = $this->getNearestScreening($exam->id);

        }
        if(is_null($ExamScreening)){
            throw new \Exception('所有考试场次已结束，请进行下一场考试',-7);
        }
        //根据考试场次id查询计划表所有考试学生
        $examPianModel = new ExamPlan();
        $exampianStudent = $examPianModel->getexampianStudent($ExamScreening->id,$exam->id);

        //获取考试场次迟到的人数
//        $examAbsentStudent = ExamAbsent::where('exam_screening_id', '=', $ExamScreening->id)
//            ->where('exam_id','=',$exam->id)
//            ->lists('student_id')
//            ->unique()
//            ->count();

        //获取考试场次已考试完成的人数
        $examFinishStudent = ExamScreeningStudent::where('is_end', 1)
            ->where('exam_screening_id', '=', $ExamScreening->id)
            ->lists('student_id')
            ->unique()
            ->count();
        if ($examFinishStudent >= $exampianStudent) {
            $ExamScreening->status = 2;
            if (!$ExamScreening->save()) {
                throw new \Exception('场次结束失败', -5);
            }
            //判断结束空场次
            $senseExamScreening = ExamScreening::where('exam_id','=',$exam->id)->get();
            foreach ($senseExamScreening as $value){
                if(!ExamPlan::where('exam_screening_id','=',$value->id)->first()){
                    $value->status =2;
                    if(!$value ->save()){
                        throw new \Exception('空场次结束失败', -7);
                    }
                }
            }
            if ($exam->examScreening()->where('status', '=', 0)->count() == 0) {
                $exam->status = 2;
                if (!$exam->save()) {
                    throw new \Exception('考试结束失败', -6);
                }
            }
        }
    }

    //查找学生考试队列exam_queue
    public function examQueue(){
        return $this->hasMany('Modules\Osce\Entities\ExamQueue', 'exam_screening_id', 'id');
    }

    /**
     * 获取当前考试场次
     * @param $exam_id
     * @return object
     *
     * @author wt <wangtao@misrobot.com>
     * @date   2016-05-7
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getScreenID($exam_id){
        $screenObject=$this->getExamingScreening($exam_id);
        if(!is_null($screenObject)){//获取当前场次
            $screenId=$screenObject->id;
        }else{
            $screenObject=$this->getNearestScreening($exam_id);
            if(is_null($screenObject)){
                throw new \Exception('今天没有正在进行的考试场次');
            }
            $screenId=$screenObject->id;
        }
        return $screenId;
    }

}