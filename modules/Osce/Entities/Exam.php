<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Illuminate\Container\Container as App;
use Modules\Osce\Entities\ExamArrange\ExamArrangeRepository;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;

class Exam extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'code',
        'name',
        'begin_dt',
        'end_dt',
        'status',
        'total',
        'create_user_id',
        'description',
        'sequence_cate',
        'sequence_mode',
        'rules',
        'address',
        'teacher_arrange',
        'arrangement',
        'same_time',
        'stage',
        'real_push',
        'archived'
    ];

    protected $statuValues = [
        0 => '未开考',
        1 => '正在考试',
        2 => '考试结束',
    ];
    public $gradationVals = [
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
        7 => '七',
        8 => '八',
        9 => '九',
        10 => '十',
        11 => '十一',
        12 => '十二',
        13 => '十三',
        14 => '十四',
        15 => '十五',
        16 => '十六',
        17 => '十七',
        18 => '十八',
        19 => '十九',
        20 => '二十',
    ];


    /**
     * 考试自能排考关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examPlan()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamPlan', 'exam_id', 'id');

    }

    /**
     * 考试与考站的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function examStation()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Station', 'exam_flow_station', 'exam_id', 'station_id');
    }

    /**
     * 考试场次关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examScreening()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamScreening', 'exam_id', 'id');
    }

    /**
     *  考生关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function students()
    {
        return $this->hasMany('\Modules\Osce\Entities\Student', 'exam_id', 'id');
    }

    /**
     * 考试流程节点关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function flows()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamFlow', 'exam_id', 'id');
    }

    /**
     * 考试阶段关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 3.3
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-04-05 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function gradation()
    {
        return $this->hasMany('\Modules\Osce\Entities\ExamGradation', 'exam_id', 'id');
    }

    /**
     * 展示考试列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showExamList($formData = '')
    {
        try {
            //不寻找已经被软删除的数据
            $builder = $this;//->where('status' , '<>' , 0);

            if ($formData) {
                $builder = $builder->where('name', 'like', $formData['exam_name'] . '%');
            }

            //寻找相似的字段
            $builder = $builder->select([
                'id',
                'name',
                'begin_dt',
                'end_dt',
                'description',
                'total',
                'status'
            ])->orderBy('begin_dt', 'desc');

            $builder->with('examPlan');

            return $builder->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除的方法
     * @param $id
     * @return bool
     */
    public function deleteData($id)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {

            //获得当前exam的实例
            $examObj = $this->findOrFail($id);

            //进入模型逻辑
            //删除与考场相关的流程
            $flowIds = ExamFlow::where('exam_id', $id)->select('flow_id')->get()->pluck('flow_id'); //获得流程的id
            $examScreening = ExamScreening::where('exam_id', $id);
            $examScreeningObj = $examScreening->select('id')->get();
            $examScreeningIds = $examScreeningObj->pluck('id');

            //如果该考试已经完成，那么就不能让他们删除
            if (!ExamResult::whereIn('exam_screening_id', $examScreeningIds)->get()->isEmpty()) {
                throw new \Exception('该考试已经考完，不能删除！');
            }

            if (!Invite::whereIn('exam_screening_id', $examScreeningIds)->get()->isEmpty()) {
                throw new \Exception('已经为sp老师发送邀请，不能删除！');
            }

            //删除考试考场学生表
            foreach ($examScreeningObj as $item) {
                if (!ExamScreeningStudent::where('exam_screening_id', $item->id)->get()->isEmpty()) {
                    if (!ExamScreeningStudent::where('exam_screening_id', $item->id)->delete()) {
                        throw new \Exception('删除考试考场学生关系表失败，请重试！');
                    }
                }
            }

            if ($examObj->students()->delete() === false) {
                throw new \Exception('删除考试学生表失败，请重试！');
            }

            //删除考场安排 相关信息
            $examDraftFlow = new ExamDraftFlow();
            $examDraftFlow ->delDraftDatas($id);

            //删除考试考场关联表
            $examScreenings = $examScreening->get();
            if (!$examScreenings->isEmpty()) {
                foreach ($examScreenings as $v) {
                    if (!$v->delete()) {
                        throw new \Exception('删除考试考场关系表失败，请重试！');
                    }
                }
            }

            //删除考试阶段 相关信息
            $examGradations = ExamGradation::where('exam_id', '=', $id)->get();
            if (!$examGradations->isEmpty()) {
                foreach ($examGradations as $examGradation) {
                    if (!$examGradation->delete()) {
                        throw new \Exception('删除考试阶段关系表失败，请重试！');
                    }
                }
            }

            //删除考试考场关联
            if (!ExamRoom::where('exam_id', $id)->get()->isEmpty()) {
                if (!ExamRoom::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }

            //删除考试考站关联
            if (!ExamStation::where('exam_id', $id)->get()->isEmpty()) {
                if (!ExamStation::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试考站关联失败，请重试！');
                }
            }

            //删除考试流程关联
            if (!ExamFlow::where('exam_id', $id)->get()->isEmpty()) {
                if (!ExamFlow::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试流程关联失败，请重试！');
                }
            }

            //通过考试流程-考站关系表得到考站信息
            switch ($examObj->sequence_mode)
            {
                case 1: //删除考试考场流程关联
                        if (!ExamFlowRoom::where('exam_id', $id)->get()->isEmpty()) {
                            if (!ExamFlowRoom::where('exam_id', $id)->delete()) {
                                throw new \Exception('删除考试考场流程关联失败，请重试！');
                            }
                        }
                        break;

                case 2: $station = ExamFlowStation::whereIn('flow_id', $flowIds);
                        $stationIds = $station->select('station_id')->get();
                        if (!$stationIds->isEmpty()) {
                            //删除考试流程-考站关系表信息
                            if (!$station->delete()) {
                                throw new \Exception('删除考试考站流程关联失败，请重试！');
                            }

                            //通过考站id找到对应的考站-老师关系表
                            if (!StationTeacher::where('exam_id', $id)->get()->isEmpty()) {
                                if (!StationTeacher::where('exam_id', $id)->delete()) {
                                    throw new \Exception('弃用考站老师关联失败，请重试！');
                                }
                            }
                        }
                        break;
                default:    throw new \Exception('System Errors');
            }

            //删除考试对应的资讯通知
            $informInfo = InformInfo::where('exam_id', $id)->get();
            if (count($informInfo) != 0) {
                foreach ($informInfo as $item) {
                    if (!$item->delete()) {
                        throw new \Exception('删除考试对应的资讯通知失败，请重试！');
                    }
                }
            }
            //删除考试本体
            if (!$result = $this->where('id', $id)->delete()) {
                throw new \Exception('删除考试失败，请重试！');
            }

            //如果有flow的话，就删除
            if (count($flowIds) != 0) {
                foreach ($flowIds as $flowId) {
                    if (!Flows::where('id', $flowId)->delete()) {
                        throw new \Exception('删除流程失败，请重试！');
                    }
                }
            }
            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 添加考试
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * array      $examData           考试数据(必须的)
     * * array      $examScreeningData  考场场次(必须的)
     * * string     $gradation          考试阶段(必须的)
     *
     * @return object
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addExam(array $examData, array $examScreeningData, $gradationMode, $gradation = 1, ExamArrangeRepository $examArrangeRepository)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {
            //将exam表的数据插入exam表
//            dd($examData);
            if (!$result = $this->create($examData)) {
                throw new \Exception('创建考试基本信息失败');
            }

            //处理 考试阶段关系 数据
            $this->handleGradation($result->id, $gradation, $gradationMode, $examArrangeRepository);

            //将考试对应的考次关联数据写入考试场次表中
            foreach ($examScreeningData as $value) {
                $value['exam_id']   = $result->id;
                $value['status']    = 0;
                if (!$examScreening = ExamScreening::create($value)) {
                    throw new \Exception('创建考试场次信息失败');
                }
            }
            $connection->commit();
            return $result;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 保存编辑考试 数据
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string     $exam_id            考试id(必须的)
     * * array      $examData           考试数据(必须的)
     * * array      $examScreeningData  考场场次(必须的)
     * * string     $gradation          考试阶段(必须的)
     *
     * @return object
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editExam($exam_id, array $examData, array $examScreeningData, $gradation, $gradationMode, ExamArrangeRepository $examArrangeRepository)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {
            //更新考试信息
            $exam = $this->doingExam($exam_id);

            //重置与当前考试相关的关联数据
            if ($exam->sequence_mode != $examData['sequence_mode'])
            {
                //如果排考模式变化 删除 已有 教师关联 和 排考计划
                if (!$examArrangeRepository->getExamManner($exam_id)) {

                    throw new \Exception('重置作废数据失败');
                }
                if (StationTeacher::where('exam_id', '=', $exam_id)->delete() === false) {
                    throw new \Exception('重置作废老师数据失败');
                }
                if (ExamRoom::where('exam_id', '=', $exam_id)->delete() === false) {
                    throw new \Exception('重置作废数据失败');
                }
                if (ExamStation::where('exam_id', '=', $exam_id)->delete() === false) {
                    throw new \Exception('重置作废数据失败');
                }
            }

            //如果考试顺序变化清空智能排考， 同时进出改变清空排考
            if ($exam->sequence_cate != $examData['sequence_cate'] || $exam->same_time != $examData['same_time'])
            {
                $DelExamArrange = $examArrangeRepository->resetSmartArrange($exam_id);
                //清空智能排考
                if (!$DelExamArrange && $DelExamArrange != null) {
                    throw new \Exception('重置作废智能排考数据失败');
                }
            }
            //处理 考试阶段关系 数据
            $this->handleGradation($exam_id, $gradation, $gradationMode, $examArrangeRepository);

            //处理 考试场次
            $this->handleExamScreening($exam_id, $examScreeningData, $examArrangeRepository);

            //修改考试基本信息
            foreach ($examData as $field => $item) {
                $exam->$field = $item;
            }
            if (!$exam->save()) {
                throw new \Exception('修改考试信息失败!');
            }

            $connection->commit();
            return $exam;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 处理考试阶段
     * @param $exam_id
     * @param $gradation
     * @param $examArrangeRepository
     *
     * @author Zhoufuxiang 2016-04-18
     * @throws \Exception
     */
    private function handleGradation($exam_id, $gradation, $gradationMode, ExamArrangeRepository $examArrangeRepository)
    {
        //查询原有的 考试阶段 个数
        $examGradation = ExamGradation::where('exam_id', '=', $exam_id)->get();
        $num = $examGradation->count();
        $keys = [];
        $key = 0;

        if (!is_array($gradationMode)) {
            $gradationMode = null;
        } else {
            $keys = array_keys($gradationMode);
            $key = array_pop($keys);
        }

        //比较 阶段个数 (不相等，则添加 或者 删除)
        if ($num != $gradation) {
            if ($num != 0){
                foreach ($examGradation as $a => $item)
                {
                    if ($key > 0 && $a + 1 > $key) {
                        $a = $key - 1;
                    }

                    //1、更新共同 拥有的
                    $item->gradation_number = $gradation;   //更新 当前考试阶段总数量
                    $item->sequence_cate = is_null($gradationMode) ? null : $gradationMode[$a + 1];
                    if (!$item->save()) {
                        throw new \Exception('更新考试阶段关系失败!');
                    }

                    //2、多余的删除
                    if ($item->order > $gradation) {
                        if (!$item->delete()) {
                            throw new \Exception('删除多余的考试阶段关系失败!');
                        }
                    }

                    //清空原考试安排数据
                    if (!$examArrangeRepository->getExamManner($exam_id))
                    {
                        throw new \Exception('重置作废数据失败');
                    }
                }
            }

            //3、少了，则添加
            if ($num < $gradation) {
                for ($i = $num + 1; $i <= $gradation; $i++) {
                    $gradationData = [
                        'exam_id'           => $exam_id,
                        'order'             => $i,
                        'gradation_number'  => $gradation,
                        'sequence_cate' => is_null($gradationMode) ? null : $gradationMode[$i],
                        'created_user_id'   => Auth::user()->id
                    ];
//                    dd($gradationData);
                    if (!ExamGradation::create($gradationData)) {
                        throw new \Exception('创建考试阶段关系失败！');
                    }
                }
                //清空原考试安排数据
                if (!$examArrangeRepository->getExamManner($exam_id))
                {
                    throw new \Exception('重置作废数据失败');
                }
            }
        } else {
            //重写cate字段
            foreach ($examGradation as $b => $item) {
                $item->sequence_cate = is_null($gradationMode) ? null : $gradationMode[$b + 1];
                if (!$item->save()) {
                    throw new \Exception('系统错误！');
                }
            }
        }

        return $examGradation;
    }


    //考生查询
    public function getList($formData = '')
    {
        $builder = $this->Join('student', 'student.exam_id', '=', 'exam.id');
        if ($formData['exam_name']) {
            $builder = $builder->where('exam.name', 'like', '%' . $formData['exam_name'] . '');
        }
        if ($formData['student_name']) {
            $builder = $builder->where('student.name', 'like', '%' . $formData['student_name'] . '');
        }

        $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.code as code',
            'student.idcard as idCard',
            'student.mobile as mobile',
            'student.user_id as user_id',
        ]);

        $builder->orderBy('exam.begin_dt', 'DESC');
        return $builder->paginate(config('msc.page_size'));
    }

    /**
     * 查询今日考试
     */
    public function getTodayList($status = '')
    {
        $time = time();
        $builder = $this->select(DB::raw(
            implode(',', [
                $this->table . '.id as id',
                $this->table . '.name as exam_name',
                $this->table . '.begin_dt as begin_dt',
                $this->table . '.end_dt as end_dt',
                $this->table . '.description as description',
                $this->table . '.status as status',
            ])
        )
        );
        $builder = $builder->whereRaw('unix_timestamp(date(begin_dt)) < ?', [$time]);
        $builder = $builder->whereRaw('unix_timestamp(date(end_dt))+86399 > ?', [$time]);

        if ($status) {
            $builder = $builder->where('status', '=', 1)->take(1);
        } else {
            $builder = $builder->where('status', '<>', 2);
        }
        $data = $builder->get();

        return $data;
    }

    public function getExamRoomData($exam_id)
    {
        try {
            return $this->leftJoin('exam_room',
                function ($join) {
                    $join->on($this->table . '.id', '=', 'exam_room.exam_id');
                })->leftJoin('exam_flow_room',
                function ($join) {
                    $join->on('exam_flow_room.room_id', '=', 'exam_room.room_id');
                })->leftJoin('room',
                function ($join) {
                    $join->on('room.id', '=', 'exam_room.room_id');
                })->where($this->table . '.id', '=', $exam_id)
                ->select([
                    'room.id',
                    'room.name',
                    'exam_flow_room.serialnumber as serialnumber',
                    'exam_flow_room.flow_id as flow_id'
                ])->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //获取候考教室列表
    public function getWriteRoom($exam_id)
    {
        $time = time();
        try {
            $builder = $this->Join('exam_room', 'exam.id', '=', 'exam_room.exam_id');
            $builder = $builder->Join('room', 'room.id', '=', 'exam_room.room_id');
            $builder = $builder->where('exam.id', $exam_id);
            $builder = $builder->whereRaw(
                'unix_timestamp(' . $this->table . '.begin_dt) > ?',
                [
                    $time
                ]
            );
        } catch (\Exception $ex) {
            throw new $ex;
        }
    }

    /**
     * 查询当天所有考试
     */
    public function selectExamToday($time = '')
    {
        if ($time == '') {
            $time = time();     //默认为今天
        }
        $today = strtotime(date('Y-m-d', $time));    //当天凌晨
            
        $result = $this->whereRaw('unix_timestamp(date_format(begin_dt, "%Y-%m-%d")) = ?
                                or unix_timestamp(date_format(end_dt, "%Y-%m-%d")) = ?
                                or (unix_timestamp(date_format(begin_dt, "%Y-%m-%d")) < ?
                                    and unix_timestamp(date_format(end_dt, "%Y-%m-%d")) > ?)',
            [$today, $today, $today, $today])
            ->with('examPlan')
            ->get();
        return $result;
    }

    //获取当前学生的所有考试
    public function Examname($examIds)
    {
        return $this->whereIn('id', $examIds)->get();
    }

    /**
     * @param string $examId
     * @param string $subjectId
     * @return mixed
     * @author Jiangzhiheng
     */
    public function CourseControllerIndex($examId = "", $subjectId = "")
    {
        //获取使用过的考站
        $stationIds = ExamResult::leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
//            ->where('exam_screening.exam_id','=',$examId)
            ->groupBy('exam_result.station_id')
            ->get()
            ->pluck('station_id')
            ->toArray();
        $builder = StationTeacher::leftJoin('station', 'station.id', '=', 'station_teacher.station_id')
            ->Join('exam_result',
                function ($join) {
                    $join->on('exam_result.station_id', '=', 'station_teacher.station_id');
                })
            ->Join('exam',
                function ($join) {
                    $join->on('exam.id', '=', 'station_teacher.exam_id');
                })
            ->Join('subject', 'subject.id', '=', 'station.subject_id');


        if ($examId != "") {
            $builder = $builder->where('exam.id', '=', $examId);
        }

        if ($subjectId != "") {
            $builder = $builder->where('subject.id', '=', $subjectId);
        }

        $builder = $builder->select(
            'exam.name as exam_name',
            'exam.id as exam_id',
            'exam.begin_dt as exam_begin_dt',
            'subject.id as subject_id',
            'subject.title as subject_name',
            'station.id as station_id'
        )
            ->where('exam.status', '<>', 0)
            ->whereNotNull('station_teacher.user_id')
            ->whereIn('station.id', $stationIds)
//            ->distinct()
            ->groupBy('subject.id')
            ->paginate(config('osce.page_size'));

        return $builder;
    }

    /**查询与监考老师相关的考试
     * @param string $userId
     * @return mixed
     * @author zhouqiang
     */
    public function getInvigilateTeacher($userId)
    {
        return $this->leftJoin('station_teacher',
            function ($join) {
                $join->on($this->table . '.id', '=', 'station_teacher.exam_id');
            })
            ->where('station_teacher.user_id', '=', $userId)
            ->select([
                'exam.name as exam_name',
                'station_teacher.exam_id as exam_id',
                'station_teacher.station_id as station_id',
            ])
            ->get();
    }

    /**
     * 查询当前正在进行的考试
     * @param null $examId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    static public function doingExam($examId = null)
    {
        try {
            if (is_null($examId)) {
                $exam = Exam::where('status', 1)->get();
                \Log::debug('考试信息',[$exam]);
                if ($exam->count() != 1) {
                    throw new \Exception('获取当前考试信息失败！', -9999);
                } else {
                    return $exam->first();
                }
            } else {
                return Exam::find($examId);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 开始一场考试 TODO 此为智能排考所用
     * @param $examId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    public function beginExam($examId)
    {
        try {
            $exam = $this->findOrFail($examId);
            if ($exam->status != 0) {
                throw new \Exception('当前的考试已经开始或已经结束！', -1);
            }
            $exam->status = 1;
            return $exam->save();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 结束一场考试 TODO 此为智能排考所用
     * @param $examId
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    public function endExam($examId)
    {
        try {
            $exam = $this->findOrFail($examId);
            if ($exam->status != 1) {
                throw new \Exception('当前的考试未开始或已经结束！', -2);
            }
            $exam->status = 2;
            return $exam->save();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 重置考试数据
     * @param   $id         //考试ID
     * @return  bool
     * @author  Zhoufuxiang <zhoufuxiang@misrobot.com>
     */
    public function emptyData($id)
    {
        try {
            //获得当前exam的实例
            $examObj = $this->findOrFail($id);
            //获取与考场相关的流程
            $examScreening    = ExamScreening::where('exam_id', '=', $id);
            $examScreeningObj = $examScreening->select('id')->get();
            $examScreeningIds = $examScreeningObj->pluck('id');

            $examResults      = ExamResult::whereIn('exam_screening_id', $examScreeningIds)->select('id')->get();
            $examResultIds    = $examResults->pluck('id');

            //清除腕表使用记录，修改腕表使用状态
            WatchLog::where('id','>',0)->delete();
            Watch::where('id','>',0)->update(['status'=>0]);
            //删除考试得分
            $examScores = ExamScore::whereIn('exam_result_id', $examResultIds)->get();
            if (!$examScores->isEmpty()) {
                foreach ($examScores as $valueS) {
                    $valueS->delete();
                }
            }
            //如果该考试已经完成，删除考试结果记录
            if (!$examResults->isEmpty()) {
                foreach ($examResults as $valueR)
                {
                    //删除考核点对应的图片、语音
                    $examAttachs = TestAttach::where('test_result_id', '=', $valueR->id)->get();
                    if(!$examAttachs->isEmpty()){
                        foreach ($examAttachs as $examAttach) {
                            $examAttach->delete();
                        }
                    }
                    //再删除对应考试结果数据
                    $valueR->delete();
                }
            }
            //删除替考记录
            $examMonitors = ExamMonitor::where('exam_id', '=', $id)->get();
            if(!$examMonitors->isEmpty()){
                foreach ($examMonitors as $examMonitor) {
                    $examMonitor->delete();
                }
            }

            //更改考试-场次-考站状态表 的状态
            $stationVideos = StationVideo::where('exam_id', '=', $id)->get();
            if(!$stationVideos->isEmpty()){
                foreach ($stationVideos as $stationVideo)
                {
                    if(!$stationVideo->delete()){
                        throw new \Exception('删除考试-锚点失败！');
                    }
                }
            }

            //修改考试考场学生表 (删除)
            foreach ($examScreeningObj as $item)
            {
                $examScreeningStudent = ExamScreeningStudent::where('exam_screening_id', '=', $item->id)->get();
                foreach ($examScreeningStudent as $value)
                {
                    if (!$value->delete()) {
                        throw new \Exception('删除考试场次学生失败！');
                    }
                }
            }

            //更改考试-场次-考站状态表 的状态
            $examStationStatus = ExamStationStatus::where('exam_id', '=', $id)->get();
            if(!$examStationStatus->isEmpty()){
                foreach ($examStationStatus as $item) {
                    $item->status = 0;
                    if(!$item->save()){
                        throw new \Exception('修改考试-场次-考站状态失败！');
                    }
                }
            }

            //更改考试场次状态
            $examScreenings = $examScreening->get();
            if (!$examScreenings->isEmpty()) {
                foreach ($examScreenings as $screening) {
                    $screening->update(['status' => 0]);       //TODO 更改状态为0
                }
            }
            //删除缺考
            ExamAbsent::where('exam_id', '=', $id)->delete();
            //删除考试队列
            ExamQueue::where('exam_id', '=', $id)->delete();
            //更改考生排序状态  TODO:（ExamOrder表中数据是在智能排考时添加进去的）
            ExamOrder::where('exam_id', '=', $id)->update(['status' => 0]);     //TODO 更改状态为0（0为未绑定腕表）
            //更改考试状态
            $result = $this->where('id', '=', $id)->update(['status' => 0]);    //TODO 更改状态为0（0为未开考）

            return $result;

        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * 处理考试场次时间
     * @param $examScreeningData
     * @param $user
     *
     * @author Zhoufuxiang 2016-04-18
     * @return array
     * @throws \Exception
     */
    public function handleScreeningTime($screeningData, $user)
    {
        //考试的最早时间（开始时间）、最晚时间（结束时间）
        $begin_dt = '';
        $end_dt   = '';
        $keyArr   = array_keys($screeningData);
        if (empty($keyArr)) {
            throw new \Exception('请添加时间！');
        }
        $firstKey = $keyArr[0];

        foreach ($screeningData as $key => $value)
        {
            $bd = $value['begin_dt'];   //开始时间
            $ed = $value['end_dt'];     //结束时间
            if (!strtotime($bd) || !strtotime($ed) || strtotime($ed) <= strtotime($bd)) {
                throw new \Exception('时间输入有误！');
            }

            if ($key > $firstKey && strtotime($screeningData[$key-1]['end_dt']) >= strtotime($bd))
            {
                throw new \Exception('后一场的开始时间必须大于前一场的结束时间！');
            }
            //获取最早开始时间，最晚结束时间
            if ($key == $firstKey) {
                $begin_dt = $bd;
            }
            if ($key == count($screeningData)) {
                $end_dt = $ed;
            }
            $screeningData[$key]['create_user_id'] = $user->id;
        }

        return [
            'begin_dt'          => $begin_dt,
            'end_dt'            => $end_dt,
            'examScreeningData' => $screeningData,
        ];
    }

    /**
     * 处理考试场次
     * @param $exam_id
     * @param $examScreeningData
     * @param ExamArrangeRepository $examArrangeRepository
     *
     * @author Zhoufuxiang 2016-04-18
     * @return static
     * @throws \Exception
     */
    public function handleExamScreening($exam_id, $examScreeningData, ExamArrangeRepository $examArrangeRepository)
    {
        $ExamScreening = new ExamScreening();
        $screening_ids = [];
        //判断输入的时间是否有误
        foreach ($examScreeningData as $value)
        {
            //不存在id,为新添加的数据
            if (!isset($value['id']))
            {
                $value['exam_id'] = $exam_id;
                $result = ExamScreening::create($value);
                if (!$result) {
                    throw new \Exception('添加考试场次信息失败');
                } else {
                    //清空原考试安排数据
                    if (!$examArrangeRepository->getExamManner($exam_id))
                    {
                        throw new \Exception('重置作废数据失败');
                    }
                }
                array_push($screening_ids, $result->id);

            } else {

                array_push($screening_ids, $value['id']);
                $result = $ExamScreening->updateData($value['id'], $value);
                if (!$result) {
                    throw new \Exception('更新考试场次信息失败');
                }
            }
        }

        //查询是否有要删除的考试场次
        $result = ExamScreening::where('exam_id', '=', $exam_id)->whereNotIn('id', $screening_ids)->get();
        if (count($result) != 0) {
            foreach ($result as $value)
            {
                if (!$res = ExamScreening::where('id', '=', $value['id'])->delete()) {
                    throw new \Exception('删除考试场次信息失败');
                } else {
                    //清空原考试安排数据
                    if (!$examArrangeRepository->getExamManner($exam_id))
                    {
                        throw new \Exception('重置作废数据失败');
                    }
                }
            }
        }

        return $result;
    }

}