<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7 0007
 * Time: 10:11
 */

namespace Modules\Osce\Entities;

use App\Entities\SysRoles;
use App\Entities\SysUserRole;
use Modules\Osce\Repositories\Common;
use App\Repositories\Common as AppComm;
use App\Entities\User;
use Auth;
use DB;
use App\Repositories\Common as AppCommon;
use Modules\Osce\Entities\CommonModel;
use Modules\Osce\Entities\Teacher;


class Student extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'name',
        'exam_id',
        'user_id',
        'idcard',
        'mobile',
        'code',
        'avator',
        'create_user_id',
        'description',
        'exam_sequence',
        'grade_class',
        'teacher_name'
    ];

    public function userInfo()
    {
        return $this->hasOne('\App\Entities\User', 'id', 'user_id');
    }

    public function absentStudent()
    {
        return $this->hasOne('\Modules\Osce\Entities\ExamAbsent', 'id', 'student_id');

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
                $Builder = $Builder->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%')
                        ->orWhere('idcard', 'like', '%' . $keyword . '%')
                        ->orWhere('mobile', 'like', '%' . $keyword . '%')
                        ->orWhere('code', 'like', '%' . $keyword . '%');
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
    public function deleteStudent($student_id, $exam_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //验证学生的删除
            $this->checkDelete($student_id, $exam_id);

            $examData = [
                'total' => count(Student::where('exam_id', $exam_id)->get())
            ];
            //更新考试信息
            $exam = new Exam();
            if (!$exam->updateData($exam_id, $examData)) {
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
     * 判断考生模板表头及列数 TODO: zhoufuxiang 2016-3-4
     */
    public function judgeTemplet($data)
    {
        try {
            $nameToEn = config('osce.importForCnToEn.student');
            foreach ($nameToEn as $index => $item) {
                $config[] = $index;
            }
//            $config = ['姓名','性别','学号','身份证号','联系电话','电子邮箱','头像','备注','准考证号','班级','班主任姓名'];
            foreach ($data as $value) {
                $key = 0;
                //模板列数
                if (count($value) != count($config)) {
                    throw new \Exception('模板列数有误');
                }
                foreach ($value as $index => $item) {
                    $key++;
                    if (!in_array($index, $config)) {
                        throw new \Exception('第' . $key . '列模板表头有误');
                    }
                }
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 性别处理
     */
    public function handleSex($sex)
    {
        if ($sex == '男') {
            return 1;
        } elseif ($sex == '女') {
            return 2;
        }
        return 0;
    }

    /**
     * 导入考生
     */
    public function importStudent($exam_id, $examineeData)
    {
        //考生角色ID
        $role_id = config('osce.studentRoleId', 2);
        $backArr = [];

        try {
            $total = 0;
            $sucNum = 0;    //导入成功的学生数
            $exiNum = 0;    //已经存在的学生数

            //将数组导入到模型中的addExaminee方法
            foreach ($examineeData as $key => $studentData) {
                $total++;       //获取处理过的考生总数
                //性别处理
                $studentData['gender'] = $this->handleSex($studentData['gender']);
                //姓名不能为空
                if (empty(trim($studentData['name']))) {
                    if (!empty($studentData['idcard']) && !empty($studentData['mobile'])) {
                        $backArr[] = ['key' => $key + 2, 'title' => 'name'];
                    }
                    continue;
                }
                //验证身份证号
                if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $studentData['idcard'])) {
                    throw new \Exception('第' . ($key + 2) . '行身份证号不符规格，请修改后重试！');
                }
                //验证手机号
                if (!preg_match('/^1[3|5|7|8]{1}[0-9]{9}$/', $studentData['mobile'])) {
                    throw new \Exception('第' . ($key + 2) . '行手机号不符规格，请修改后重试！');
                }
                //准考证号不能为空
                if (empty(trim($studentData['exam_sequence']))) {
                    throw new \Exception('第' . ($key + 2) . '行准考证号不能为空，请修改后重试！');
                }

                //根据条件：查找用户是否有账号和密码
                $user = User::where(['username' => $studentData['mobile']])->select(['id'])->first();
                if ($user) {
                    //根据用户ID和考试号查找考生
                    $student = $this->where('user_id', $user->id)->where('exam_id', $exam_id)->first();
                } else {
                    $student = false;
                }

                //考生存在,则 跳过
                if ($student) {
                    $exiNum++;
                    continue;
                }
                //用户数据
                $userData = [
                    'name'      => $studentData['name'],
                    'gender'    => $studentData['gender'],
                    'idcard'    => $studentData['idcard'],
                    'mobile'    => $studentData['mobile'],
                    'code'      => $studentData['code'],
                    'avatar'    => $studentData['avator'],
                    'email'     => $studentData['email']
                ];
                //考生数据
                $examineeData = [
                    'name'      => $studentData['name'],
                    'idcard'    => $studentData['idcard'],
                    'mobile'    => $studentData['mobile'],
                    'code'      => $studentData['code'],
                    'avator'    => $studentData['avator'],
                    'description'   => $studentData['description'],
                    'exam_sequence' => $studentData['exam_sequence'],
                    'grade_class'   => $studentData['grade_class'],
                    'teacher_name'  => $studentData['teacher_name']
                ];
                //添加考生
                if (!$this->addExaminee($exam_id, $examineeData, $userData, $role_id, $key + 2)) {
                    throw new \Exception('学生导入数据失败，请修改后重试');
                } else {
                    $sucNum++;      //添加成功的考生数
                }
            } /*循环结束*/

            $message = "成功导入{$sucNum}个学生";
            if ($exiNum) {
                $message .= "，有{$exiNum}个学生已存在";
            }
            //返回信息数组不为空
            if (!empty($backArr)) {
                $mes1 = '';
                foreach ($backArr as $item) {
                    if ($item['title'] == 'name') {
                        $mes1 .= $item['key'] . '、';
                    }
                }
                if ($mes1 != '') {
                    $message .= '，第' . rtrim($mes1, '、') . '行姓名不能为空！';
                }
            }
            if ($exiNum || isset($mes1)) {
                if (isset($mes1)) {
                    throw new \Exception(trim($message, '，'));
                }
                throw new \Exception(trim($message, '，'));
            }

            return $sucNum;     //返回导入成功的个数

        } catch (\Exception $ex) {
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
    public function addExaminee($exam_id, $examineeData, $userData, $role_id, $key = '')
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            $operator = Auth::user();
            if (empty($operator)) {
                throw new \Exception('未找到当前操作人信息');
            }

            //身份证号验证
            Common::checkIdCard($exam_id, $userData);

            //处理考生用户信息（基本信息、角色分配）
            $user = Common::handleUser($userData, $role_id);
//            $user = $this->handleUser($userData);

            //查询学号是否存在
            $code = $this->where('code', $examineeData['code'])->where('user_id', '<>', $user->id)->first();

            if (!empty($code)) {
                throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该学号已经有别人使用！');
            }
            //根据用户ID和考试号查找考生
            $student = $this->where('user_id', $user->id)->where('exam_id', $exam_id)->first();

            //存在考生信息,则提示已添加, 否则新增
            if ($student) {
                throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该考生已经存在，不能再次添加！');

            } else {

                $examineeData['exam_id'] = $exam_id;
                $examineeData['user_id'] = $user->id;
                $examineeData['create_user_id'] = $operator->id;
                //新增考试对应的考生
                $student = $this->create($examineeData);
                if (!$student) {
                    throw new \Exception('新增考生失败！');
                }

                //更新考试对应的考生数量
                $exam = new Exam();
                $examData = ['total' => count(Student::where('exam_id', $exam_id)->get())];
                if (!$result = $exam->updateData($exam_id, $examData)) {
                    throw new \Exception('修改考试信息失败!');
                }
            }

            $connection->commit();
            return $student;

        } catch (\Exception $ex) {

            if ($ex->getCode() == 23000) {
                throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该手机号码已经使用，请输入新的手机号');
            }
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 处理用户信息（基本信息、角色分配）
     * @param $userData
     *
     * @author Zhoufuxiang 2016-04-18
     * @return static
     * @throws \Exception
     */
    public function handleUser($userData)
    {
        //根据条件：查找用户是否有账号和密码
        $user = User::where(['username' => $userData['mobile']])->first();
        $role_id = config('osce.studentRoleId');
        $roles = SysRoles::where('id', '=', $role_id)->first();
        if (is_null($roles)) {
            throw new \Exception('没有对应的角色，请去新增对应角色，或者查看角色配置！');
        }

        //如果查找到了，对用户信息 进行编辑处理
        if (!is_null($user)) {
            //获取数据（姓名,性别,身份证号,手机号,学号,邮箱,照片）
            foreach ($userData as $field => $value) {
                $user->$field = $value;
            }

            if (!($user->save())) {      //跟新用户
                throw new \Exception('新增考生失败！');
            }
            //给用户分配角色
            $this->addUserRoles($user, $role_id);

        } else {      //如果没找到，新增处理,   如果新增成功，发短信通知用户

            //手机号未注册，查询手机号码是否已经使用
            $mobile = User::where(['mobile' => $userData['mobile']])->first();
            //该手机号码已经使用
            if ($mobile) {
                throw new \Exception('手机号已经存在，请输入新的手机号');
            }
            //注册 新用户
            $password = '123456';
            $user = $this->registerUser($userData, $password);
            $this ->sendRegisterEms($userData['mobile'], $password);
            //给用户分配角色
            $this->addUserRoles($user, $role_id);
        }

        return $user;
    }

    public function registerUser($data, $password)
    {
        $form_user = $data;
        $form_user['username'] = $data['mobile'];
        $form_user['openid'] = '';
        $form_user['password'] = bcrypt($password);
        $user = User::create($form_user);
        if ($user) {
            return $user;
        } else {
            throw new \Exception('创建用户失败');
        }
    }

    public function sendRegisterEms($mobile, $password)
    {
        //发送短消息
        Common::sendRegisterEms($mobile, $password);
    }

    /**
     * 给用户分配角色
     * @param $user
     * @param $role_id
     *
     * @author Zhoufuxiang 2016-04-18
     * @return object
     */
    private function addUserRoles($user, $role_id)
    {
        $superRole   = config('osce.superRoleId', 5);
        //查询用户是否是超级管理员
        $superUser   = SysUserRole::where('user_id','=',$user->id)->where('role_id','=',$superRole)->first();
        if (!is_null($superUser)){
            throw new \Exception('该用户为超级管理员，不能添加，请修改！');
        }
        //查询用户角色
        $sysUserRole = SysUserRole::where('user_id', '=', $user->id)->where('role_id', '=', $role_id)->first();
        //给用户分配角色
        if (is_null($sysUserRole)) {
            $sysUserRole = DB::table('sys_user_role')->insert(
                [
                    'role_id' => $role_id,
                    'user_id' => $user->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }
        return $sysUserRole;
    }

    /**
     * 考生身份验证
     * @param $watch_id
     * @return bool
     */
    public function studentList($stationId, $exam, $student_id)
    {
        
        //当前场次
        $examScreen = new ExamScreening();
        $roomMsg = $examScreen->getExamingScreening($exam->id);
        $roomMsg_two = $examScreen->getNearestScreening($exam->id);

        $exam_screening_id = null;

        if($roomMsg){
            $exam_screening_id=$roomMsg->id;
        }elseif($roomMsg_two){
            $exam_screening_id=$roomMsg_two->id;
        }

        if (is_null($exam_screening_id)) {
            throw new \Exception('未找到当前考试场次');
        }

        $queueing = Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })->leftjoin('station_teacher', function ($join) {
            $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
        })
            ->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.exam_id', '=', $exam->id)
            ->where('station_teacher.exam_id', $exam->id)
            ->where('exam_queue.student_id', $student_id)
            ->where('exam_queue.exam_screening_id', $exam_screening_id)
            ->where('exam_queue.status', '=', 2)
            ->first();

        if (is_null($queueing)) {//没有正在考试的
            // 查询当前考生信息
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('exam_queue.student_id', $student_id)
                ->where('station_teacher.exam_id', $exam->id)
                ->where('exam_queue.status', 1)
//                ->where('exam_queue.blocking', 1)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'exam_queue.station_id as station_id',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        } else {//被中断的学生继续考试
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('exam_queue.student_id', $student_id)
                ->where('station_teacher.exam_id', $exam->id)
                ->where('exam_queue.status', '=', 2)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'exam_queue.station_id as station_id',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        }


        // 查询考试是否结束 // edit by wangjiang 2016-03-29 for 查询考试是否结束
        $waitingList = Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })->leftjoin('station_teacher', function ($join) {
            $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
        })->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.status', '<>', 3)
            ->first();


        return [
            'nextTester' => $nextTester,
            'waitingList' => $waitingList,
        ];
    }

    /**获取学生信息
     * @method
     * @url /osce/
     * @access public
     * @param $stationId
     * @param $exam
     * @return array
     * @throws \Exception
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function studentListtwo($stationId, $exam)
    {

        //当前场次
        $examScreen = new ExamScreening();
        $roomMsg = $examScreen->getExamingScreening($exam->id);
        $roomMsg_two = $examScreen->getNearestScreening($exam->id);

        $exam_screening_id = null;

        if($roomMsg){
            $exam_screening_id=$roomMsg->id;
        }elseif($roomMsg_two){
            $exam_screening_id=$roomMsg_two->id;
        }

        if (is_null($exam_screening_id)) {
            throw new \Exception('未找到当前考试场次');
        }

        $queueing = Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })->leftjoin('station_teacher', function ($join) {
            $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
        })
            ->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.exam_id', '=', $exam->id)
            ->where('station_teacher.exam_id', $exam->id)
            ->where('exam_queue.exam_screening_id', $exam_screening_id)
            ->where('exam_queue.status', '=', 2)
            ->first();

        if (is_null($queueing)) {//没有正在考试的
            // 查询当前考生信息
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('station_teacher.exam_id', $exam->id)
                ->where('exam_queue.status', 1)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'exam_queue.station_id as station_id',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        } else {//被中断的学生继续考试
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('station_teacher.exam_id', $exam->id)
                ->where('exam_queue.status', '=', 2)
                ->where('exam_queue.exam_screening_id', $exam_screening_id)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'exam_queue.station_id as station_id',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        }


        // 查询考试是否结束 // edit by wangjiang 2016-03-29 for 查询考试是否结束
        $waitingList = Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })->leftjoin('station_teacher', function ($join) {
            $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
        })->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.status', '<>', 3)
            ->first();
        return [
            'nextTester' => $nextTester,
            'waitingList' => $waitingList,
        ];
    }

    /*
     * 获取待考学生
     *
     * */
    public function nextStudentList($stationId, $exam)

    {//\DB::connection('osce_mis')->enableQueryLog();
        // 查询下一个待考考生信息
        $nextTester = Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })
            ->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.exam_id', '=', $exam->id)
            ->where('exam_queue.status', 1)
            ->where('exam_queue.blocking', 1)
            ->orderBy('exam_queue.next_num', 'asc')
            ->orderBy('exam_queue.begin_dt', 'asc')
            ->orderBy('exam_queue.updated_at', 'asc')
            ->select([
                'student.name as student_name',
                'student.code as student_code', 'student.user_id as student_user_id',
                'student.idcard as student_idcard',
                'student.mobile as student_mobile',
                'student.avator as student_avator',
                'exam_queue.status as status',
                'student.id as student_id',
                'student.exam_sequence as exam_sequence', 'exam_queue.id as exam_queue_id', 'student.description as student_description',
            ])->get();
        // $queries = \DB::connection('osce_mis')->getQueryLog();

        return [
            'nextTester' => $nextTester
        ];
    }


    //考生查询
    public function getList($formData = '')
    {
        $builder = $this->leftJoin('exam', 'student.exam_id', '=', 'exam.id');
        if (isset($formData['exam_name']) && $formData['exam_name'] != '') {
            $builder = $builder->where('exam.name', 'like', '%\\' . $formData['exam_name'] . '%');
        }
        if (isset($formData['student_name']) && $formData['student_name'] != '') {
            $builder = $builder->where('student.name', 'like', '%\\' . $formData['student_name'] . '%');
        }

        $builder = $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.code as code',
            'student.idcard as idCard',
            'student.mobile as mobile',
            'student.user_id as user_id',
            'student.id as id',
        ]);

        $builder = $builder->orderBy('exam.begin_dt', 'desc');
        $builder = $builder->orderBy('student.id', 'desc');

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

    /**
     * 获取考生队列
     * @param $exam_id
     * @param $screen_id
     * @param $countStation
     * @return array
     */
    public function getStudentQueue($exam_id, $screen_id, $countStation)
    {
        //查询 该考试、该场次下，已绑定腕表的学生数量
        $buondNum = ExamOrder::where('exam_id', '=', $exam_id)->where('status', '=', 1)
                             ->where('exam_screening_id', '=', $screen_id)->get();
        $buondNum = count($buondNum);
        $num      = $countStation - $buondNum;
        if($num === 0 || $num < 0){
            return array();
        }

        /*$builder = $this->leftjoin('exam_order', function ($join) {
            $join->on('student.id', '=', 'exam_order.student_id');
        })->leftjoin('exam_queue',function($exam_queue){
            $exam_queue->on('exam_queue.exam_screening_id','=','exam_order.exam_screening_id');
        })->whereIn('exam_queue.status', [0,1])->where('exam_order.exam_id', '=', $exam_id)->where('exam_order.exam_screening_id', '=', $screen_id);
        $builder = $builder->where(function ($query) {
            $query->whereIn('exam_order.status',[0,4]);
        });*/

        //查询待考、考试中的学生列表
        $endStudentList = ExamQueue::where('exam_id', '=', $exam_id)
                                   ->where('exam_screening_id', '=', $screen_id)
                                   ->whereIn('status', [0,2,1])->get();

        if(count($endStudentList))
        {
            //查询 该考试、场次下，队列中考试结束的学生列表
            $studentList = ExamQueue::where('status', '=', 3)->where('exam_id', '=', $exam_id)
                                    ->where('exam_screening_id', '=', $screen_id)
                                    ->groupBy('student_id')->get()
                                    ->pluck('student_id')->toArray();
            //检查上述学生列表中，学生是否已真的结束考试（筛选出真正结束考试了的学生）
            $studentList = ExamScreeningStudent::where('exam_screening_id', '=', $screen_id)
                                ->whereIn('student_id', $studentList)
                                ->where('is_end', '=', 1)
                                ->groupBy('student_id')->get()
                                ->pluck('student_id')->toArray();

            $builder = $this->leftjoin('exam_order', function ($join) {     //TODO wt 未绑定时队列表没数据
                    $join->on('student.id', '=', 'exam_order.student_id');
                })
                ->where('exam_order.exam_id', '=', $exam_id)
                ->where('student.exam_id', '=', $exam_id)
                ->where('exam_order.exam_screening_id', '=', $screen_id);
            $builder = $builder->where(function ($query) {
                    $query->whereIn('exam_order.status', [0,2,4]);
                });

            if (count($studentList)) {
                $builder = $builder->whereNotIn('exam_order.student_id', $studentList);
            }

        }else
        {
            $builder = $this->leftjoin('exam_order', function ($join) {     //TODO wt 未绑定时队列表没数据
                    $join->on('student.id', '=', 'exam_order.student_id');
                })
                ->where('exam_order.exam_id', '=', $exam_id)
                ->where('student.exam_id', '=', $exam_id)
                ->where('exam_order.exam_screening_id', '=', $screen_id);
            $builder = $builder->where(function ($query) {
                    $query->whereIn('exam_order.status', [0,4]);
                });
        }

        $builder = $builder->select([
                'student.id as id',
                'student.name as name',
                'student.idcard as idcard',
                'student.code as code',
                'student.mobile as mobile',
                'exam_order.status as status',
                'exam_order.exam_screening_id as exam_screening_id',
            ])
            ->orderBy('exam_order.begin_dt','asc')->paginate(100);

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
        return Student::leftJoin('exam_result', 'exam_result.student_id', '=', 'student.id')
            ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
            ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
            ->leftJoin('station', 'station.id', '=', 'exam_result.station_id')
            ->where('exam.id', '=', $examId)
            ->where('exam.status', '<>', 0)
            ->where('station.subject_id', '=', $subjectId)
            ->orderBy('exam_result.score', 'desc')
            ->select(
                'student.name as student_name',
                'student.id as student_id',
                'exam_result.id as exam_result_id',
                'exam_result.score as exam_result_score',
                'exam_result.time as exam_result_time',
                'station.type as station_type'
            )
            ->paginate(config('osce.page_size'));
    }

    /**
     * 学生成绩统计的着陆页
     * @author Jiangzhiheng
     * @param $examId
     * @param $message
     */
    static public function getStudentScoreList($examId, $message)
    {
        $builder = Student::leftJoin('exam_result', 'exam_result.student_id', '=', 'student.id')
            ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
            ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id');

        if ($examId != "") {
            $builder = $builder->where('exam.id', '=', $examId);

            if ($message != "") {
                $builder = $builder->where('student.name', 'like', '%' . $message . '%')
                    ->orWhere('student.idcard', 'like', '%' . $message . '%');
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
            )->where('exam.status', '<>', 0);

            $builder = $builder->groupBy('exam_result.student_id')->orderBy('score_total', 'desc');

            return $builder->paginate(config('osce.page_size'));
        }
    }

    /**
     *查询一场考试下的所有生
     * @author zhongaing
     * @param $examId
     * @param $message
     */
    public function getExamStudent($examId)
    {
        $students = $this->where('exam_id', '=', $examId)->get();
        return $students;
    }

    static public function examStudent($examId)
    {
        return Student::where('exam_id', '=', $examId)->get();
    }

    /**
     * 删除学生的检测
     * @param $student_id
     * @param $exam_id
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-04 21:06
     */
    private function checkDelete($student_id, $exam_id)
    {
        try {
            $result = WatchLog::where('student_id', $student_id)->first();
            if ($result) {
                throw new \Exception('该考生已绑定，无法删除！', -111);
            }
            if (!$result = $this->where('id', $student_id)->delete()) {
                throw new \Exception('删除该考生失败', -112);
            }

            $examPlanRecord = ExamPlanRecord::where('exam_id', $exam_id)->get();
            if (!$examPlanRecord->isEmpty()) {
                foreach ($examPlanRecord as $item) {
                    if (!$item->delete()) {
                        throw new \Exception('删除该考生失败', -113);
                    }
                }
            }

            if (ExamPlan::where('student_id', $student_id)->first()) {
                if (!ExamOrder::where('student_id', $student_id)->delete()) {
                    throw new \Exception('删除该学生失败');
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //user表关联学生表
    public function screeningStudent()
    {
        return $this->hasMany('Modules\Osce\Entities\ExamScreeningStudent', 'student_id', 'id');
    }


    //获取用Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel户的信息及已报的考试
    public function getStudentExamInfo($userId, $examID)
    {
        //查找当前学生信息
        $studentInfo = $this->where('student.user_id', '=', $userId)->where('student.exam_id', '=', $examID)->orderBy('student.id','desc')->first();
        return $studentInfo;
    }

    //获取考生的详细信息

    /**
     * @method
     * @url /osce/
     * @access public
     * @param $studentId 学生id
     * @param $exam_id 考试Id
     * @param $exam_screening_id 当前场次Id
     * @return mixed
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExameeStatus($studentId,$exam_id,$exam_screening_id)
    {
        $builder = $this->where('student.id', $studentId)->where('exam_queue.exam_id',$exam_id)->where('exam_queue.exam_screening_id',$exam_screening_id)->leftjoin('exam_screening_student', function ($join) {
            $join->on('exam_screening_student.student_id', '=', 'student.id');
        })->leftjoin('exam_screening', function ($examScreening) {
            $examScreening->on('exam_screening.id', '=', 'exam_screening_student.exam_screening_id');
        })->leftjoin('exam_queue', function ($examQueue) {
            $examQueue->on('exam_queue.exam_screening_id', '=', 'exam_screening.id');
        })->select('exam_queue.status', 'exam_queue.id','exam_queue.station_id','exam_queue.room_id')->first();

        return $builder;
    }

    //统计考生的剩余考站
    public function getExameeStationsCount($studentId, $examId)
    {
        $DB = \DB::connection('osce_mis');
        $builder = $this->where('student.id', '=', $studentId)->where('student.exam_id', '=', $examId)->where('exam_queue.status', '!=', 3)->leftjoin('exam_screening_student', function ($join) {
            $join->on('exam_screening_student.student_id', '=', 'student.id');
        })->leftjoin('exam_screening', function ($examScreening) {
            $examScreening->on('exam_screening.id', '=', 'exam_screening_student.exam_screening_id');
        })->leftjoin('exam_queue', function ($examQueue) {
            $examQueue->on('exam_queue.exam_screening_id', '=', 'exam_screening.id');
        })->select($DB->raw('count(exam_queue.station_id) as num'))->first();

        return $builder;
    }


    //查找学生所报考试
    public function getExamings($userid)
    {
        $builder = $this->where('user_id', '=', $userid)->select('exam_id')->get();

        return $builder;
    }


    //获取考生信息
    public function getStudentInfo($stationId, $exam, $teacher_id)
    {
        $queueing= Student::leftjoin('exam_queue', function ($join) {
            $join->on('student.id', '=', 'exam_queue.student_id');
        })->leftjoin('station_teacher', function ($join) {
            $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
        })
            ->where('exam_queue.station_id', '=', $stationId)
            ->where('exam_queue.exam_id', '=', $exam->id)
            ->where('station_teacher.exam_id', $exam->id)
            ->where('exam_queue.status', '=', 2)
            ->first();
        if (is_null($queueing)) {//没有正在考试的
            // 查询当前考生信息
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('station_teacher.exam_id', $exam->id)
                ->whereIn('exam_queue.status', [1, 2])
                ->where('exam_queue.blocking', 1)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        } else {//被中断的学生继续考试
            $nextTester = Student::leftjoin('exam_queue', function ($join) {
                $join->on('student.id', '=', 'exam_queue.student_id');
            })->leftjoin('station_teacher', function ($join) {
                $join->on('exam_queue.station_id', '=', 'station_teacher.station_id');
            })
                ->where('exam_queue.station_id', '=', $stationId)
                ->where('exam_queue.exam_id', '=', $exam->id)
                ->where('station_teacher.exam_id', $exam->id)
                ->where('exam_queue.status', '=', 2)
                ->orderBy('exam_queue.begin_dt', 'asc')
                ->orderBy('exam_queue.next_num', 'asc')
                ->select([
                    'student.name as name',
                    'student.code as code',
                    'student.idcard as idcard',
                    'student.mobile as mobile',
                    'student.avator as avator',
                    'exam_queue.status as status',
                    'student.id as student_id',
                    'student.exam_sequence as exam_sequence',
                    'station_teacher.user_id as teacher_id',
                    'exam_queue.id as exam_queue_id'
                ])->first();

        }
        return $nextTester;
    }


    /**
     * 获取模型中的数据
     * @author zhouqing
     * @time 2016-04-21
     */

    public function getStudentsOpendIds($exam_id, array $data = [])
    {
        $list = ExamOrder::leftjoin('student', function ($join) {
            $join->on('exam_order.student_id', '=', 'student.id');
        })->leftjoin('exam', function ($join) {
            $join->on('exam_order.exam_id', '=', 'exam.id');
        })
            ->where('exam_order.exam_id', '=', $exam_id)
            ->select([
                'exam_order.student_id as student_id',
                'exam_order.begin_dt as student_begin_dt',
                'student.user_id as user_id',
                'student.mobile as mobile',
                'student.name as student_name',
                'exam.name as exam_name',

            ])
            ->get();


        if(!$list){
            throw new \Exception('请先给学生排考');
         }
        foreach ($list as $student) {
            if (is_null($student->userInfo)) {
                throw new \Exception('没有找到指定的考生用户信息');
            }

            $data[] = [
                'id' => $student->userInfo->id,
                'openid' => $student->userInfo->openid,
                'email' => $student->userInfo->email,
                'mobile' => $student->userInfo->mobile,
                'exam_name' => $student->exam_name,
                'student_begin_dt' => $student->student_begin_dt,
                'student_id' => $student->student_id,
                'student_name' => $student->student_name,
            ];
        }
        return $data;
    }




    /**
     * 学生短信通知方式
     * @author zhouqiang
     * @time 2016-04-21
     */
    public function sendSms($notice,$to,$url)
    {
        $timeArray  =   $notice->pluck('begin_dt');
        $timeData   =   [];
        foreach($timeArray as $time)
        {
            $timeData[] =   date('Y年m月d日H时i分',strtotime($time));
        }
        $smsContent = view('osce::admin.systemManage.student_inform',['notice'=>$notice->first(),'timeData'=>$timeData])->render();

        $smsContent =   $this->linshi($smsContent);

        try
        {

            AppCommon::sendSms($to,$smsContent,$this   ->  getEmsConfig());
//            $path   =   public_path('osce').'/sms.txt';
//            $f     =   fopen($path,'a');
//            fwrite($f,$smsContent);
//            fwrite($f,"\r\n");
//            fclose($f);
            //dd($path);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
        return true;
    }

    /**
     * 获取短信发送认证信息
     * @access private
     * @return array
     *
     * @version 3.6
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function getEmsConfig(){
        return [

        ];
    }

    private function linshi($smsContent){
        $ze =[
            "2016年05月18日08时44分",
            "2016年05月18日09时28分",
            "2016年05月18日10时12分",
            "2016年05月18日10时56分",
            "2016年05月18日11时40分",
            "2016年05月18日13时44分",
            "2016年05月18日14时28分",
            "2016年05月18日15时12分",
            "2016年05月18日15时56分",
            "2016年05月18日16时40分",
            "2016年05月18日17时24分",
        ];
        $re=[
            "2016年05月18日08时32分",
            "2016年05月18日09时04分",
            "2016年05月18日09时36分",
            "2016年05月18日10时08分",
            "2016年05月18日10时40分",
            "2016年05月18日13时32分",
            "2016年05月18日14时04分",
            "2016年05月18日14时36分",
            "2016年05月18日15时08分",
            "2016年05月18日15时40分",
            "2016年05月18日16时12分",
        ];
        //$str    =   '2016年5月18日09时28分';
        //dd(preg_replace($str,$ze,));
        $smsContent    =   str_replace($ze,$re,$smsContent);
        return $smsContent;
    }

    /**
     * 微信通知方式
     * @author zhouqing
     * @time 2016-04-21
     */

    public function sendWechat($notice,$to,$url){
        $msgData    =   [
            [
                'title' =>  '考试通知',
                'desc' =>   '你有一场考试为'.$notice['exam_name']. ' ' .'考试开始时间为'.$notice['student_begin_dt'].'请你准时参加!!',
                'url'   =>  $url
            ]
        ];
        $message    =   AppCommon::CreateWeiXinMessage($msgData);
        if(count($to)==1)
        {
            AppCommon::sendWeiXin($to[0],$message);
        }
        else
        {
            AppCommon::sendWeixinToMany($message,$to);
        }
    }



    /**
     *
     * @author zhouqing
     * @time 2016-04-21
     */
    public function sendPm($notice,$to,$url){
        $sender =   \App::make('messages.pm');
        foreach($to as $accept)
        {
            if(empty($accept))
            {
                continue;
            }


            $sender ->  send($accept,$url,$notice['exam_name']);
        }
    }

    /**
     * 邮件通知方式
     * @author zhouqing
     * @time 2016-04-21
     */

    public function sendEmail($notice,$to,$url){
        try {
            $sender =   \App::make('messages.email');
            $content=   [];
            $content[]  =   '亲爱的'.$notice['exam_name'].'同学:';
            $content[]  =   '你有一场考试为'.$notice['exam_name']. ' ' .'考试开始时间为'.$notice['student_begin_dt'];
            $content[]  =   '请你准时参加'.$url;
            $sender ->  send($to,implode('',$content));
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
        }
    }






    /**
     * 判断发送方式
     * @author zhouqing
     * @time 2016-04-21
     */
    public function sendMsg($examId ,$url='')
    {
        try {

//            $url = $this->makeUrl($notice);
            $sendType = config('osce.sys_param');
            if (empty($sendType['student'])) {
                throw new \Exception('请到系统设置中设置发送消息的方式');
            }
            
            try {
                $list   =   ExamOrder::where('exam_id','=',$examId)->with('student')->with('exam')->get();
                $studentList    =   $list->groupBy('student_id');
                foreach ($studentList as $studentId=>$value){
                    $studentOrder    =   $value->first();
                    if($sendType['student']['wechat'] == 1){
                        $this->sendWechat($value, $studentOrder->student->mobile, $url);
                    }

//                    $str    =   '今天上午不慎将系统调试过程的短信发送给大家，造成误解，请大家谅解，正式的考试时间通知将在稍后发出。【敏行医学】';
//                    $sender=\App::make('messages.sms');
//                    $sender->send($value->student->mobile,$str);

                    if($sendType['student']['sms'] == 1){
                        $this->sendSms($value, $studentOrder->student->mobile, $url);
                    }

//                    if($sendType['student']['mail'] == 1){
//                        $this->sendEmail($studentList[$value->student->id], $value->student->email, $url);
//                    }
//
//                    if($sendType['student']['mail'] == 1){
//                        $this->sendPm($studentList[$value->student->id], $value->student->id, $url);
//                    }
                }

                return true;
            } catch (\Exception $ex) {
                dd($ex);
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}