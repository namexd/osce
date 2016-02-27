<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7 0007
 * Time: 10:11
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Repositories\Common;
use App\Entities\User;
use Auth;
use DB;
class Student extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'exam_id', 'user_id', 'idcard', 'mobile', 'code', 'avator', 'create_user_id', 'description','exam_sequence'];

    public function userInfo(){
        return $this->hasOne('\App\Entities\User','id','user_id');
    }

    public function absentStudent(){
        return $this->hasOne('\Modules\Osce\Entities\ExamAbsent','id','student_id');

    }
    /**
     * 展示考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showStudentList()
    {
        try {
            $student = $this->select([
                'id',
                'name',
                'idcard',
                'exam_id'
            ]);

            return $student->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 展示 考试 对应的考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function selectExamStudent($exam_id, $keyword)
    {
        try {
            $Builder = $this->where('exam_id', '=', $exam_id);

            //如果keyword不为空，那么就进行模糊查询
            if (($keyword != '') && (isset($keyword))) {
                $Builder = $Builder->where(function ($query) use ($keyword){
                                $query->orWhere('name',   'like', '%' . $keyword . '%')
                                      ->orWhere('idcard', 'like', '%' . $keyword . '%')
                                      ->orWhere('mobile', 'like', '%' . $keyword . '%')
                                      ->orWhere('code',   'like', '%' . $keyword . '%');
                            });
            }
            return $Builder->paginate(10);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除考生的方法
     * @param $id
     * @return bool
     */
    public function deleteStudent($student_id,$exam_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            $result = WatchLog::where('student_id', $student_id)->first();
            if($result){
                throw new \Exception('该考生已绑定，无法删除！');
            }
            if (!$result = $this->where('id', $student_id)->delete()){
                throw new \Exception('该考生已绑定，无法删除！');
            }

            if (ExamPlan::where('student_id',$student_id)->first()) {
                if (!ExamOrder::where('student_id',$student_id)->delete()) {
                    throw new \Exception('删除该学生失败');
                }
            }
            $examData   = [
                'total' => count(Student::where('exam_id', $exam_id)->get())
            ];
            //更新考试信息
            $exam = new Exam();
            if (!$result = $exam->updateData($exam_id, $examData)) {
                throw new \Exception('修改考试信息失败!');
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 导入考生
     */
    public function importStudent($exam_id, $examineeData)
    {
        $backArr = [];

        try{
            //将数组导入到模型中的addInvigilator方法
            foreach($examineeData as $key => $studentData)
            {
                if($studentData['gender'] == '男'){
                    $studentData['gender'] = 1;
                }elseif($studentData['gender'] == '女'){
                    $studentData['gender'] = 2;
                }else{
                    $studentData['gender'] = 0;
                }
                //姓名不能为空
                if(empty(trim($studentData['name']))){
                    if(!empty($studentData['idcard']) && !empty($studentData['mobile'])){
                        $backArr[] = ['key'=> $key+2, 'title'=>'name'];
                    }
                    continue;
//                    throw new \Exception('第'.($key+2).'行姓名不能为空，请修改后重试！');
                }
                //验证身份证号
                if(!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/',$studentData['idcard'])){
                    throw new \Exception('第'.($key+2).'行身份证号不符规格，请修改后重试！');
                }
                //验证手机号
                if(!preg_match('/^1[3|5|7|8]{1}[0-9]{9}$/',$studentData['mobile'])){
                    throw new \Exception('第'.($key+2).'行手机号不符规格，请修改后重试！');
                }
                //准考证号不能为空
                if(!isset($studentData['exam_sequence'])){
                    throw new \Exception('缺少准考证号列，请添加');
                }
                if(empty(trim($studentData['exam_sequence']))){
                    throw new \Exception('第'.($key+2).'行准考证号不能为空，请修改后重试！');
                }

                //根据条件：查找用户是否有账号和密码
                $user = User::where(['username' => $studentData['mobile']])->select(['id'])->first();
                if($user){
                    //根据用户ID和考试号查找考生
                    $student = $this->where('user_id', '=', $user->id)
                        ->where('exam_id', '=', $exam_id)->first();
                }else{
                    $student = false;
                }

                //考生存在,则 跳过
                if($student){
                    $backArr[] = ['key'=> $key+2, 'title'=>'exist'];
                    continue;
                }
                //添加考生
                if(!$this->addExaminee($exam_id, $studentData, $key+2))
                {
                    throw new \Exception('学生导入数据失败，请修改后重试');
                }
            }

            //返回信息数组不为空
            if(!empty($backArr)){
                $message = '第';
                $mes1 = '';     $mes2 = '';
                foreach ($backArr as $item) {
                    if($item['title']=='name'){
                        $mes1 .= $item['key'].'、';
                    }elseif($item['title']=='exist'){
                        $mes2 .= $item['key'].'、';
                    }
                }
                if($mes1 != '' && $mes2 != ''){
                    $message .= rtrim($mes1,'、').'行姓名不能为空，第'.rtrim($mes2,'、').'行考生已存在，请修改后重试！';
                }elseif($mes1 != ''){
                    $message .= rtrim($mes1,'、').'行姓名不能为空，请修改后重试！';
                }else{
                    $message .= rtrim($mes2,'、').'行考生已存在，请修改后重试！';
                }
                throw new \Exception($message);
            }

            return true;

        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 单个添加考生
     * @param $exam_id
     * @param $examineeData
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function addExaminee($exam_id, $examineeData,$key = '')
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $operator   =   Auth::user();
            if(empty($operator)){
                throw new \Exception('未找到当前操作人信息');
            }

            //根据条件：查找用户是否有账号和密码
            $user = User::where(['username' => $examineeData['mobile']])->first();

            //如果查找到了，对用户信息 进行编辑处理
            if(count($user) != 0){
                $user -> name   = $examineeData['name'];    //姓名
                $user -> gender = $examineeData['gender'];  //性别
                $user -> mobile = $examineeData['mobile'];  //手机号
                $user -> avatar = $examineeData['avator'];  //头像
                $user -> idcard = $examineeData['idcard'];  //身份证号
                $user -> email  = $examineeData['email'];   //邮箱
                if(!($user->save())){      //跟新用户
                    throw new \Exception('新增考生失败！');
                }

            }else{      //如果没找到，新增处理,   如果新增成功，发短信通知用户
                //手机号未注册，查询手机号码是否已经使用
                $mobile = User::where(['mobile' => $examineeData['mobile']])->first();

                if(!empty($mobile)){

                    throw new \Exception('手机号已经存在，请输入新的手机号');
                }
                $password   =   '123456';
                $user       =   $this   ->  registerUser($examineeData,$password);
                $this       ->  sendRegisterEms($examineeData['mobile'],$password);
            }
            //查询学号是否存在
            $code = $this->where('code', $examineeData['code'])->where('user_id','<>',$user->id)->first();

            if(!empty($code)){
                throw new \Exception((empty($key)?'':('第'.$key.'行')).'该学号已经有别人使用！');
            }
            //根据用户ID和考试号查找考生
            $student = $this->where('user_id', '=', $user->id)
                ->where('exam_id', '=', $exam_id)->first();

            //存在考生信息,则更新数据, 否则新增
            if($student){
                throw new \Exception((empty($key)?'':('第'.$key.'行')).'该考生已经存在，不能再次添加！');

            } else{
                $examineeData['exam_id'] = $exam_id;
                $examineeData['user_id'] = $user->id;
                $examineeData['create_user_id'] = $operator->id;

                if(!$result = $this->create($examineeData)){
                    throw new \Exception('新增考生失败！');
                }
                //更新考试信息
                $exam = new Exam();
                $examData = ['total' => count(Student::where('exam_id', $exam_id)->get())];
                if (!$result = $exam->updateData($exam_id, $examData)) {
                    throw new \Exception('修改考试信息失败!');
                }
            }

            $connection->commit();
            return true;

        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    public function registerUser($data,$password){
        $form_user=$data;
        $form_user['username']  =   $data['mobile'];
        $form_user['openid']    =   '';
        $form_user['password']  =   bcrypt($password);
        $user=User::create($form_user);
        if($user)
        {
            return $user;
        }
        else
        {
            throw new \Exception('创建用户失败');
        }
    }
    public function sendRegisterEms($mobile,$password){
        //发送短消息
        Common::sendRegisterEms($mobile,$password);
    }

    /**
     * 考生身份验证
     * @param $watch_id
     * @return bool
     */


    public  function studentList($stationId){
        return Student::leftjoin('exam_queue',function($join){
            $join ->on('student.id','=','exam_queue.student_id');
        })->leftjoin('station_teacher',function($join){
            $join ->on('exam_queue.station_id','=','station_teacher.station_id');
        })->where('exam_queue.station_id','=',$stationId)
            ->whereIn('exam_queue.status',[1,2])
            -> orderBy('exam_queue.begin_dt','asc')
            ->select([
                'student.name as name',
                'student.code as code',
                'student.idcard as idcard',
                'student.mobile as mobile',
                'student.avator as avator',
                'exam_queue.status as status',
                'student.id as student_id',
                'student.exam_sequence as exam_sequence',
            ])
            ->first();
    }

    //考生查询
    public function getList($formData=''){
        $builder=$this->leftJoin('exam','student.exam_id','=','exam.id');
        if($formData['exam_name']){
            $builder=$builder->where('exam.name','like','%'.$formData['exam_name'].'%');
        }
        if($formData['student_name']){
            $builder=$builder->where('student.name','like','%'.$formData['student_name'].'%');
        }

        $builder    =   $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.code as code',
            'student.idcard as idCard',
            'student.mobile as mobile',
            'student.user_id as user_id',
            'student.id as id',
        ]);

        $builder    =   $builder    ->  orderBy('exam.begin_dt','desc');
        $builder    =   $builder    ->  orderBy('student.id','desc');

        return $builder->paginate(config('osce.page_size'));
    }

//    public  function studentList($watch_id){
//        return Student::leftjoin('watch_log',function($join){
//            $join ->on('student.id','=','watch_log.student_id');
//        })->where('watch_log.id','=',$watch_id)
//          ->select([
//              'student.name as name',
//              'student.code as code',
//              'student.idcard as idcard',
//              'student.mobile as mobile'
//          ])
//            ->get();
//    }

    public function getStudentQueue($exam_id,$screen_id,$countStation)
    {
        $buondNum=ExamOrder::where('exam_id', $exam_id)->where('exam_screening_id', $screen_id)->where('status',1)->select()->get();
        $buondNum=count($buondNum);
        $num=$countStation-$buondNum;
        if($num===0 || $num<0){
          return array();
        }
        $builder= $this->leftjoin('exam_order',function($join){
                    $join ->on('student.id','=','exam_order.student_id');
                })->where('exam_order.exam_id','=',$exam_id)->where('exam_order.exam_screening_id','=',$screen_id);
        $builder= $builder->where(function($query){
                    $query->where('exam_order.status','=',0)->orWhere('exam_order.status','=',4);
                });

        //查询本场考试中 已考试过的 学生 ，用于剔除//TODO zhoufuxiang
        $students = $this->leftjoin('exam_screening_student',function($join){
            $join ->on('student.id', '=', 'exam_screening_student.student_id');
        })
            ->where('exam_screening_student.exam_screening_id', '=', $screen_id)
            ->where('exam_screening_student.is_end', '=', 1)
            ->select(['exam_screening_student.student_id'])->get();
        $studentIds = [];   //用于保存已经考试的学生ID
        if(count($students)){
            foreach ($students as $index => $student) {
                array_push($studentIds, $student->student_id);
            }
        }
        //剔除 已经考试过的学生
        if(count($studentIds)){
            $builder = $builder->whereNotIn('exam_order.student_id', $studentIds);
        }

        $builder= $builder->select([
                'student.id as id',
                'student.name as name',
                'student.idcard as idcard',
                'student.code as code',
                'student.mobile as mobile',
                'exam_order.status as status',
                'exam_order.exam_screening_id as exam_screening_id',
            ])->orderBy('exam_order.begin_dt')->paginate(100);

        return $builder;
    }

    /**
     * 根据考试id和科目id找到对应的考生以及考生的成绩信息
     * @param $examId
     * @param $subjectId
     * @author Jiangzhiheng
     */
    static public function getStudentByExamAndSubject($examId, $subjectId)
    {
        return Student::leftJoin('exam_result','exam_result.student_id','=','student.id')
            ->leftJoin('exam_screening','exam_screening.id','=','exam_result.exam_screening_id')
            ->leftJoin('exam','exam.id','=','exam_screening.exam_id')
            ->leftJoin('station','station.id','=','exam_result.station_id')
            ->where('exam.id','=',$examId)
            ->where('exam.status','<>',0)
            ->where('station.subject_id','=',$subjectId)
            ->orderBy('exam_result.score','desc')
            ->select(
                'student.name as student_name',
                'student.id as student_id',
                'exam_result.id as exam_result_id',
                'exam_result.score as exam_result_score',
                'exam_result.time as exam_result_time'
            )
            ->paginate(config('osce.page_size'));
    }

    /**
     * 学生成绩统计的着陆页
     * @author Jiangzhiheng
     * @param $examId
     * @param $message
     */
    static public function getStudentScoreList($examId,$message) {
        $builder = Student::leftJoin('exam_result','exam_result.student_id','=','student.id')
                    ->leftJoin('exam_screening','exam_screening.id','=','exam_result.exam_screening_id')
                    ->leftJoin('exam','exam.id','=','exam_screening.exam_id');

        if ($examId != "") {
            $builder = $builder->where('exam.id','=',$examId);

            if ($message != "") {
                $builder = $builder->where('student.name','like','%'. $message .'%')
                    ->orWhere('student.idcard','like','%'. $message .'%');
            }

            $builder = $builder->select(DB::raw(implode(',',
                [
                    'student.id as student_id',
                    'student.name as student_name',
                    'student.code as student_code',
                    'exam.name as exam_name',
                    'sum(exam_result.score) as score_total',
                    'count(*) as station_total'
                ]))
            )->where('exam.status','<>',0);

            $builder = $builder->groupBy('exam_result.student_id')->orderBy('score_total','desc');

            return $builder->paginate(config('osce.page_size'));
        }
    }

    /**
     *查询一场考试下的所有生
     * @author zhongaing
     * @param $examId
     * @param $message
     */
    public function getExamStudent($examId){
        $students= $this->where('exam_id','=',$examId)->get();
        return   $students;
    }

    static public function examStudent($examId)
    {
       return Student::where('exam_id','=',$examId)->get();
    }

}