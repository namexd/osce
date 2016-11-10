<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/26
 * Time: 19:22
 */

namespace Modules\Osce\Entities;


use App\Entities\User;
use App\Repositories\Common;
use \Modules\Osce\Repositories\Common as osce_Common;
use DB;

class ExamResult extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_result';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'student_id', 'exam_screening_id', 'station_id', 'end_dt',   'begin_dt',  'time',    'score',
        'score_dt',   'create_user_id',    'teacher_id', 'evaluate', 'operation', 'skilled', 'patient',
        'affinity',   'original_score',    'flag',   'subject_id'
    ];

    public function examScreening(){
        return $this->hasOne('\Modules\Osce\Entities\ExamScreening','id','exam_screening_id');
    }

    public function examScore(){
        return $this->hasMany('\Modules\Osce\Entities\ExamScore','exam_result_id','id');
    }

    public function student(){
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    public function teacher(){
        return $this->hasOne('\Modules\Osce\Entities\Teacher','id','teacher_id');
    }

    /**
     * 查询考试成绩列表
     * @param $examId
     * @param $stationId
     * @param $name
     * @return mixed
     */
    public function getResultList($examId, $stationId, $name)
    {
        $builder = $this->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
                        ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
                        ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                        ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id');

        if($examId){
            $builder = $builder->where('exam.id', '=', $examId);
//            //查询子考试
//            $sonExams = Exam::where('pid', '=', $examId)->select(['id AS exam_id'])->get();
//            if(!$sonExams->isEmpty())
//            {
//                $exam_ids = $sonExams->pluck('exam_id')->toArray();     //取子考试ID数组
//                $exam_ids = array_push($exam_ids, $examId);             //将父考试放入数组中
//
//                $builder = $builder->whereIn('exam.id', $exam_ids)
//                                   ->where();
//            }else
//            {
//                $builder = $builder->where('exam.id', '=', $examId);
//            }
        }
        if($stationId){
            $builder = $builder->where('station.id', '=', $stationId);
        }
        if($name){
            $builder = $builder->where('student.name', 'like', '%'.$name.'%');
        }

        $builder = $builder->select([
            'exam_result.id as id',
            'exam.name as exam_name',
            'exam_result.begin_dt as begin_dt',
            'exam_result.end_dt as end_dt',
            'exam_result.time as time',
            'exam_result.score as score',
            'exam_result.flag as flag',
            'student.name as student_name',
            'student.id as student_id',
            'station.name as station_name',
            'station.type as station_type',
        ])->paginate(config('osce.page_size'));

//        //查询子考试
//        if($examId)
//        {
//            $sonExams = Exam::where('pid', '=', $examId)->select(['id AS exam_id'])->get();
//            if(!$sonExams->isEmpty()){
//                $exam_ids = $sonExams->pluck('exam_id')->toArray();     //取子考试ID数组
//                foreach ($builder as $index => $item) {
//
//                }
//            }
//        }

        return $builder;
    }

    /**
     * @return array
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-06-16 14:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getSonExamResult($exam_ids, $user_id)
    {
        $result = $this->leftJoin('student', 'student.id', '=', 'exam_result.student_id')
                       ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
                       ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                       ->whereIn('exam_screening.exam_id', $exam_ids)
                       ->where('student.user_id', '=', $user_id)
                       ->orderBy('exam_result.', 'desc')->first();
        return $result;
    }

    /**
     * 考试成绩实时推送
     * @param $student_id
     * @param $screening_id
     * @param $stationId
     * @param $studentExamScreeningIdArr
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-03-14 21:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function examResultPush($student_id, $screening_id, $stationId,$studentExamScreeningIdArr)
    {
        try {
            //考生信息
            $student  = Student::where('id', $student_id)->select(['user_id','exam_id', 'name'])->first();
            if(!$student){
                throw new \Exception(' 没有找到该考生信息！');
            }
            //对应考试信息
            $exam = Exam::where('id', $student->exam_id)->select(['name'])->first();
            if(!$exam){
                throw new \Exception(' 没有找到对应考试信息！');
            }
            //用户信息
            $userInfo = User::where('id', $student->user_id)->select(['name', 'openid'])->first();
            if($userInfo){
                
                if(!empty($userInfo->openid)){

                    //查询该场考试该考生的总成绩
                    $testResult = new TestResult();
                    $examResult = $testResult->AcquireExam($student_id,$studentExamScreeningIdArr);

                    //成绩详情url地址
                    $url = route('osce.wechat.student-exam-query.getExamDetails',['exam_screening_id'=>$screening_id,'station_id'=>$stationId]);

                    $msgData = [
                        [
                            'title' => '考试成绩查看',
                            'desc'  => $userInfo->name.'同学的 '.$exam->name.' 考试的总成绩为：'.$examResult.'分',
                            'url'   => $url,
                        ],
                    ];
                    $message = Common::CreateWeiXinMessage($msgData);
                    Common::sendWeiXin($userInfo->openid, $message);    //单发

                }else{
                    throw new \Exception($userInfo->name.' 没有关联微信号');
                }
            }else{
                throw new \Exception(' 没有找到该考生对应的用户信息！');
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取考试结果详细信息
     * @param $result_id
     * @return mixed
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-07-08 14:30     TODO:整理
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getResultDetail($result_id)
    {
        $DB = \DB::connection('osce_mis');
        $builder= $this->select([
                    'exam.id as exam_id',   'exam.name as exam_name',
                    'exam_result.id',
                    $DB->raw('sec_to_time(exam_result.time) as time'),      //耗时时间处理，转换为时分秒
                    'exam_result.begin_dt', 'exam_result.end_dt',
                    'exam_result.score',    'exam_result.original_score',
                    'exam_result.station_id',
                    'exam_result.student_id', 'exam_result.teacher_id',
                    'exam_result.evaluate', 'exam_result.patient',
                    'exam_result.affinity',
                    'subject.id as subject_id', 'subject.title as subject_title'
                ])
                ->leftJoin('subject', 'exam_result.subject_id', '=', 'subject.id')
                ->leftJoin('exam_screening', 'exam_result.exam_screening_id', '=', 'exam_screening.id')
                ->leftJoin('exam', 'exam_screening.exam_id', '=', 'exam.id')
                ->where('exam_result.id', '=', $result_id)->first();

        return $builder;
    }


    /**
     *  微信端学生每一个考站成绩查询
     * @param Request $request
     * @author zhouqiang
     * @return \Illuminate\View\View
     */


    public function stationInfo($studentId,$examScreeningId){
     $builder=$this->leftJoin('station', function($join){
         $join -> on('station.id', '=', 'exam_result.station_id');
     })-> leftJoin('teacher', function($join){
         $join -> on('teacher.id', '=', 'exam_result.teacher_id');
     })
      ->where('exam_result.student_id',$studentId)
         ->whereIn('exam_result.exam_screening_id',$examScreeningId)
         ->select([
         'exam_result.id as exam_result_id ',
         'exam_result.station_id as id',
         'exam_result.score as score',
         'exam_result.time as time',
         'teacher.name as grade_teacher',
         'station.type as type',
         'station.name as station_name',
         'exam_result.exam_screening_id as exam_screening_id',
         'station.id as station_id'
     ])
//        ->groupBy('exam_result.student_id','=',$studentId)
         ->get();
         return $builder;
    }

    /**
     *  pc端学生成绩查询
     * @param $studentId
     * @return \Illuminate\View\View
     * @author zhouqiang
     */

    public function getStudentData($studentId){

        $builder=$this->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })-> leftJoin('teacher', function($join){
            $join -> on('teacher.id', '=', 'exam_result.teacher_id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam.id', '=', 'student.exam_id');
        })
            ->leftJoin('exam_score','exam_score.exam_result_id','=','exam_result.id')
            ->leftJoin('station','station.id','=','exam_result.station_id')
            ->leftJoin('subject','subject.id','=','exam_score.subject_id')

            ->leftjoin('exam_paper','exam_paper.id','=','station.paper_id');
        $builder=$builder->where('exam_result.student_id',$studentId);
        $builder=$builder->select([
            'exam_result.station_id as id',
            'exam_result.score as score',
            'exam_result.time as time',
            'exam_result.flag as flag',
            'exam_result.begin_dt as begin',
            'exam_result.id as result_id',
            'teacher.name as grade_teacher',
            'student.id as student_id',
            'student.name as student_name',
            'student.code as student_code',
            'exam.id as exam_id',
            'exam.name as exam_name',
            'exam.begin_dt as begin_dt',
            'exam.end_dt as end_dt',
            'subject.title as title',
            'station.id as station_id',
            'station.type as station_type',
            'exam_paper.name as paper'
        ])
            ->orderBy('station.id')
            ->groupBy('exam_score.exam_result_id')
            ->paginate(config('osce.page_size'));
        return $builder;

    }

    /**
     * 根据条件获取 所有成绩数据
     * @param null $exam_id
     * @return mixed
     *
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-25 14:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAllResult($exam_id = null)
    {
        //2、获取成绩数据
        $datas = ExamResult::leftJoin('student', 'student.id', '=', 'exam_result.student_id')
                           ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
                           ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                           ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
                           ->leftJoin('exam_draft', 'exam_draft.station_id', '=', 'exam_result.station_id')
                           ->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                           ->leftJoin('subject', 'subject.id', '=', 'exam_draft.subject_id');

        if(!is_null($exam_id)){
            $datas = $datas->where('exam_screening.exam_id', '=', $exam_id)->where('exam_draft_flow.exam_id', '=', $exam_id);
        }

        $datas = $datas->select(['student.grade_class', 'student.code', 'student.name as student_name', 'student.id as student_id', //考生信息
                                 'exam.name as exam_name', 'exam_screening.exam_id',                                                //考试、场次信息
                                 'station.name as station_name', 'station.type as station_type', 'station.id as station_id',        //考站信息
                                 'exam_draft_flow.name as flow_name', 'exam_draft_flow.order', 'exam_draft.room_id',                //站信息(考场安排中的第几站)
                                    'subject.id as subject_id',     'subject.title as subject_name',
                                 'exam_result.score', 'exam_result.original_score'])                                                 //考试结果信息



                       ->orderBy('exam.id', 'asc')
                       ->orderBy('student.id', 'asc');

        return $datas->get();
    }

    /**
     * 获取成绩表头
     * @param null $exam_id
     * @return mixed
     *
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-25 17:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getScoreHeader($exam_id = null)
    {
        //1、获取表头（根据站）
        $header = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                           ->leftJoin('room', 'room.id', '=', 'exam_draft.room_id')
                           ->leftJoin('station', 'station.id', '=', 'exam_draft.station_id')
                           ->where('exam_draft_flow.exam_id', '=', $exam_id)
                           ->select([
                               'exam_draft_flow.id', 'exam_draft_flow.name as flow_name', 'exam_draft_flow.order',
                               'exam_draft.room_id', 'room.name as room_name',
                               'exam_draft.station_id', 'station.type as station_type',
                           ])
                           ->groupBy('exam_draft.station_id')
                           ->orderBy('exam_draft_flow.order')
                           ->get();

        $arr    = [];
        $result = [];
        if(!$header->isEmpty())
        {
            foreach ($header as $index => $item)
            {
                //重复的就无需在添加
                if(!in_array($item->flow_name, $result)){
                    $result[] = $item->flow_name;
                }

//                //SP考站，考场名全为（sp考场考试）
//                if($item->station_type == 2){
//                    $item->room_name = 'sp考场考试';
//                }
//                //重复的就无需在添加
//                if(!in_array($item->room_name, $arr)){
//                    $arr[] = $item->room_name;
//                }
            }
        }

        return $result;
    }

    /**
     *从缓存中获取下一组
     * @access public
     * @param Student $student
     * @param array $params
     * @return object
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */

    public function NextExaminee($examId,$screeningId,$roomId,$teacherId,$stationId,$abnormal = 1){
        //从缓存中 获取当前组考生队列
        $key = 'next_room_id' . $roomId .'_exam_id'.$examId;
        //从缓存中取出 当前组考生队列
        $NextExamQueue = \Cache::get($key);
        //检查是否有异常考生
        if(!empty($NextExamQueue)){
            foreach ($NextExamQueue as $item){
                //如果有一个学生不是异常就跳出 反之就把这一组学生队列全部结束
                if($item->controlMark == -1){
                    $abnormal = 2;
                    break;
                }
            }
            //结束下一组所有队列
            if($abnormal ==1){
                foreach ($NextExamQueue as $value){
                    $Queue =  ExamQueue::whereStudentId($value->student_id)
                        ->where('room_id', $roomId)
                        ->whereExamScreeningId($value->exam_screening_id)
                        ->orderBy('begin_dt', 'asc')
                        ->first();
                    if(is_null($Queue)){
                        throw  new \Exception('结束考试下一组异常考生结束失败',1008);
                    }else{
                        $Queue->status = 3;
                        $Queue->station_id = $stationId;
                        if(!$Queue->save()){
                            throw  new \Exception('结束考试下一组异常考生结束失败',1009);
                        }else{
                            //创建成绩
                            $this-> AbnormalScore($examId,$screeningId,$value->student_id,$teacherId,$stationId);
                        }
                    }
                }
                //更新所有考试缓存
                osce_Common::updateAllCache($examId, $screeningId,$push =true);
            }
        }
        return $NextExamQueue;
    }
    
    
    //给异常考生创建成绩 todo zhouqiang 2016/6/12

    public function  AbnormalScore($examId,$screeningId,$studentId,$teacherId,$stationId)
    {
        //查询考试具体异常操作
        $ExamScreeningStudent = ExamScreeningStudent::where('exam_screening_id','=',$screeningId)
            ->where('student_id','=',$studentId)->first();
        if(is_null($ExamScreeningStudent)){
            throw  new \Exception('没有找到场次与学生关联');

        }
        if($ExamScreeningStudent ->description == 1){
            $ExamScreeningStudent ->description =2;
        }
        if($ExamScreeningStudent ->description == 2){
            $ExamScreeningStudent ->description =3;
        }
        if($ExamScreeningStudent ->description == 3){
            $ExamScreeningStudent ->description =4;
        }
        //查看该考试下所有的场次
        $ExamScreeningIds =ExamScreening::where('exam_id','=',$examId)->get()->pluck('id')->toArray();
        //拿到该场次下所有的成绩
        $ExamResult = ExamResult::whereIn('exam_screening_id',$ExamScreeningIds)->where('student_id','=',$studentId)->get();
        if(!$ExamResult->isEmpty()){
            foreach ($ExamResult as $item){
                if($item->flag == 0){
                    $item->flag = $ExamScreeningStudent ->description;
                    if(!$item->save()){
                        throw new \Exception('作废考生这次考试以前的成绩失败');
                    }
                }
            }
        }
        // 向考试结果记录表(exam_result)插入数据未考考试分数
        $examResultData=array(
            'student_id'=>$studentId,
            'exam_screening_id'=>$screeningId,
            'station_id'=>$stationId,
            'time'=>0,
            'score'=>0,
            'original_score'=>0,
            'begin_dt' => date('Y-m-d H:i:s', time()),
            'end_dt' => date('Y-m-d H:i:s', time()),
            'teacher_id'=>$teacherId,
            'flag'=>$ExamScreeningStudent ->description,
        );
        if(!ExamResult::create($examResultData)){
            \Log::alert('异常接口数据创建成绩',[$examResultData]);
            throw new \Exception(' 插入考试结果记录表失败！',-105);
        }
        //更新所有考试缓存
        osce_Common::updateAllCache($examId, $screeningId,$push =true);
        return true;
    }


    /**
     * 获取无效成绩标识
     * @access public
     * @param $resultId
     * @return mixed
     * @author GaoDapeng <gaodapeng@misrobot.com>
     * @time 2016-06-29
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getInvalidSign($resultId)
    {
        $DB = \DB::connection('osce_mis');
        //通过考卷记录与科目记录获取异常成绩的标识
        $info = self::leftjoin('exam_monitor','exam_result.student_id','=','exam_monitor.student_id')
                ->leftjoin('exam_paper_formal','exam_paper_formal.student_id','=','exam_result.student_id')
                ->leftjoin('exam_score','exam_score.exam_result_id','=','exam_result.id')
                ->where('exam_result.id',$resultId)
                ->select(
                'exam_monitor.description as description',
                $DB->raw('count(exam_paper_formal.id) as paper_count'),
                $DB->raw('count(exam_score.id) as subject_count')
                )
                ->first()
                ->toarray();
        return $info;
    }
}