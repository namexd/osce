<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:50
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Repositories\Common;
use App\Entities\SysUserRole;
use App\Entities\SysRoles;
use App\Entities\User;
use Auth;
use DB;

class Teacher extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'teacher';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['id','name', 'code', 'type', 'case_id','status', 'create_user_id','description'];

    private   $excludeId    = [];

    protected $type_values  =   [
        '1' =>  '监考老师',
        '2' =>  'SP病人',
        '3' =>  '巡考老师',
    ];

    /**
     * 用户关联
     */
    public function userInfo(){
        return $this    ->  hasOne('\App\Entities\User','id','id');
    }

    public function subjects(){
//        return $this    ->  hasMany('Modules\Osce\Entities\Subject','id',);
        return $this->  belongsToMany('Modules\Osce\Entities\Subject','teacher_subject','teacher_id','subject_id','id');
    }
    /**
     * 通过老师id去寻找对应的应该在的考站id
     * @param $id
     * @param $exam
     * @return
     */
    static public function stationIds($id, $exam)
    {
        return StationTeacher::where('exam_id', $exam->id)
            ->where('user_id', $id)
            ->get()
            ->pluck('station_id');
    }


    /**
     * 通过老师id找到对应的room
     * @param $id
     * @param $exam
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-03-23
     */
    static public function room($id, $exam)
    {
        return StationTeacher::leftJoin('room_station', 'room_station.station_id', '=', 'station_teacher.station_id')
            ->leftJoin('room', 'room.id', '=', 'room_station.room_id')
            ->where('station_teacher.exam_id', $exam->id)
            ->where('station_teacher.user_id', $id)
            ->select(
                'room.id as room_id',
                'station_teacher.station_id',
                'room.name as room_name')
            ->first();
    }



    /**
     * 获取是否为SP老师的值
     * @access public
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 16:56
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getIsSpValues(){
        return $this    ->  type_values;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function station()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\station','station_sp','user_id','station_id');
    }




    //邀请sp老师的数据
    public  function invitationContent($teacher_id){
        $builder=$this;
        try{
//            if ($teacher_id !== null) {
//                $this->excludeId = $teacher_id;
//            }
//            $excludeId = $this->excludeId;
//            $excludeIds = (explode(",",$teacher_id));

//            $builder = $builder->leftJoin('cases',function($join){
//                $join    ->  on('cases.id','=', 'teacher.case_id');
//            })->whereIn($this->table.'.id', $teacher_id);
            if (is_array($teacher_id)) {
                $builder = $builder->whereIn($this->table.'.id', $teacher_id);
            }else{
                $builder = $builder->where($this->table.'.id' , '=',$teacher_id);
            }
//            $data=$builder->select('teacher.name','teacher.id','cases.name as cname','cases.id as caseId')->get()->toArray();
            $data=$builder->select('teacher.name','teacher.id')->get()->toArray();
            $list=[];
            foreach($data as $k=>$Teacher){
                $list[]=[
                    'teacher_id'=>$Teacher['id'],
                    'teacher_name'=>$Teacher['name'],
//                    'case_name'=>$Teacher['cname'],
//                    'case_id'=>$Teacher['caseId'],
                ];
                $userInfo   = Teacher::find($Teacher['id'])->userInfo;
                if(is_null($userInfo))
                {
                    throw new \Exception('没有找到对应的用户信息');
                }
                $list[$k]['openid']=$userInfo->openid;
            }

            return $list;

        }catch (\Exception $ex) {
            throw $ex;
        }

    }




    /**
     * SP老师的查询
     * @param $caseId
     * @param $spteacherId
     * @param $teacherType
     * @return mixed
     * @throws \Exception
     */
    public function showTeacherData($stationId, array $spteacherId)
    {
        try {
            //将传入的$spteacherId插进数组中
            if (count($spteacherId) != 0) {
                $this->excludeId = $spteacherId;
            }

            if ($stationId === null) {
                throw new \Exception('系统发生了错误，请重试！');
            }

            //通过传入的$station_id得到病例id
            $case = StationCase::where('station_case.station_id', '=', $stationId)
                ->select('case_id');
            if ($case->get()->isEmpty()) {
                throw new \Exception('未找到对应的病例');
            } else {
                $case_id = $case->first()->case_id;
            }

            $builder = $this->where('type' , '=' , 2); //查询教师类型为指定类型的教师
            $builder = $builder->where('case_id' , '=' , $case_id); //查询符合病例的教师

            //如果$excludeId不为null，就说明需要排查这个id
            $excludeId = $this->excludeId;
            if (count($excludeId) !== 0) {
                $builder = $builder->whereNotIn('id', $spteacherId);
            }
            $connection = DB::connection($this->connection);
            $connection->enableQueryLog();
            return $b=$builder->select([
                'id',
                'name',
                'status'
            ])->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }




    /**
     * 获取sp老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 10:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilatorList(){
        return  $this   ->  where('type','=',2)
                        ->  where('archived','=',0)
                        ->  paginate(config('osce.page_size'));
    }

    public function getSpInvigilatorInfo(){
//        return  $this   ->  where('type','=',2)
//            ->leftjoin('cases',function($join){
//                $join ->on('cases.id','=',$this->table.'.case_id');
//            })
//            ->select([$this->table.'.*', 'cases.name as case_name'])
//            ->  paginate(config('osce.page_size'));
        return $this    ->  where('type','=',2)
                        ->  where('archived','=',0)
                        ->  with('subjects')
                        ->  paginate(config('osce.page_size'));
    }

    /**
     * 获取非SP监考老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 16:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getInvigilatorList($type = 1){
        return  $this->where('type','=',$type)->where('archived','=',0)->paginate(config('osce.page_size'));
    }

    /**
     * 添加老师（监考、巡考）
     * @access public
     *
     * @param array $data
     * * string         $role_id            角色ID(必须的)
     * * array          $userData           用户信息(必须的)
     * * array          $teacherData        老师信息(必须的)
     * * array          $subjects           考试项目
     *
     * @return object
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-03-30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addInvigilator($role_id, $userData , $teacherData, $subjects = [])
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //处理老师用户信息（基本信息、角色分配）
            $user   = Common::handleUser($userData, $role_id);
//            $user = $this->handleUser($userData, $role_id);

            //查询老师是否存在
            $teacher= $this ->where('id','=',$user->id)->first();
            //判断老师是否已归档
            if(!is_null($teacher) && $teacher->archived==1)
            {
                //开档（重新启用老师）
                $this->openData($teacher);

            } else {

                //查询教师编号是否已经被别人使用
                $code = $this->where('code', $teacherData['code'])->where('id','<>',$user->id)->first();
                if(!empty($code)){
                    throw new \Exception('该教师编号已经有别人使用！');
                }

                if($teacher){
                    throw new \Exception('该教职员工已经存在');

                } else{
                    $teacherData['id'] = $user -> id;
                    if(!($teacher = $this -> create($teacherData))){
                        throw new \Exception('教职员工创建失败');
                    }
                }
            }

            //插入老师-考试项目 关系 TODO:Zhoufuxiang 2016-3-30
            $teacherSubject = $this->handleTeacherSubject($subjects, $user->id, $teacherData['create_user_id']);

            $connection->commit();
            return $user;

        } catch(\Exception $ex){
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
    private function handleUser($userData, $role_id)
    {
        try{
            $roles = SysRoles::where('id', '=', $role_id)->first();
            if (is_null($roles)) {
                throw new \Exception('没有对应的角色，请去新增对应角色，或者查看角色配置！');
            }

            $mobile = $userData['mobile'];
            //根据条件：查找用户是否有账号和密码
            $user   = User::where('username', '=', $mobile)->first();

            if(!$user){
                if(config('debug')==true){
                    $password   =   123456;
                }else{
                    $password   =   Common::getRandStr(6);
                }

                $user = $this -> registerUser($userData, $password);
                $this -> sendRegisterEms($mobile, $password);
                //给用户分配角色
                $this -> addUserRoles($user, $role_id);

            }else{
                foreach($userData as $feild=> $value) {
                    $user    ->  $feild  =   $value;
                }
                if(!$result = $user -> save()) {
                    throw new \Exception('用户修改失败，请重试！');
                }
                //给用户分配角色
                $this->addUserRoles($user, $role_id);
            }

            return $user;

        } catch (\Exception $ex){

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
        $sysUserRole = SysUserRole::where('user_id','=',$user->id)->where('role_id','=',$superRole)->first();
        if (!is_null($sysUserRole)){
            throw new \Exception('该用户为超级管理员，不能添加，请修改！');
        }
        //查询用户角色
        $sysUserRole = SysUserRole::where('user_id','=',$user->id)->where('role_id','=',$role_id)->first();
        //给用户分配角色
        if(is_null($sysUserRole)){
            $sysUserRole = DB::table('sys_user_role')->insert(
                [
                    'role_id'   => $role_id,
                    'user_id'   => $user->id,
                    'created_at'=> date('Y-m-d H:i:s'),
                    'updated_at'=> date('Y-m-d H:i:s'),
                ]
            );
        }
        return $sysUserRole;
    }

    /**
     * 处理 老师、考试项目 关联关系
     * @param $subjects         //考试项目数组
     * @param $teacher_id       //对应老师ID
     * @param $operator         //操作人员
     *
     * @author Zhoufuxiang 2016-04-18
     * @throws \Exception
     */
    public function handleTeacherSubject($subjects, $teacher_id, $operator)
    {
        $teacherSubjects = TeacherSubject::where('teacher_id','=',$teacher_id)->whereNotNull('subject_id')->get();

        $subjectArr  = $teacherSubjects->pluck('subject_id')->toArray();    //取出考试项目数组
        $delSubjects = array_diff($subjectArr, $subjects);     //原来有，现在没有（删除）
        $addSubjects = array_diff($subjects, $subjectArr);     //原来没有，现在有（添加）

        //原来没有，现在有（添加）
        if (!empty($addSubjects))
        {
            foreach ($addSubjects as $addSubject)
            {
                $subjectData = [
                    'teacher_id'        => $teacher_id,
                    'subject_id'        => $addSubject,
                    'created_user_id'   => $operator,
                ];
                $teacherSubjects = TeacherSubject::create($subjectData);
                if(!$teacherSubjects){
                    throw new \Exception('添加老师-考试项目关系失败！');
                }
            }

        }
        //原来有，现在没有（删除）
        if (!empty($delSubjects)){
            foreach ($delSubjects as $delSubject)
            {
                $teacherSubjects = TeacherSubject::where('teacher_id','=',$teacher_id)->where('subject_id','=',$delSubject)->first();

                if(!$teacherSubjects->delete()){
                    throw new \Exception('删除老师-考试项目关系失败！');
                }
            }
        }

        return $teacherSubjects;
    }

    /**
     * 编辑非SP教务人员
     * @access public
     *
     * @param int       $id     教务人员ID
     * @param string    $name   教务人员姓名
     * @param string    $mobile 教务人员手机号
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editInvigilator($id, $userData, $teacherData, $subjects)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            //教务人员信息变更
            $teacher    =   $this   ->  find($id);

            if(!$teacher){
                throw new   \Exception('没有找到该教务人员');
            }

            //查询教师编号是否已经被别人使用
            $code = $this->where('code', $teacherData['code'])->where('id','<>',$id)->first();
            if(!empty($code)){
                throw new \Exception('该教师编号已经有别人使用！');
            }

            foreach($teacherData as $feild => $value) {
                $teacher    ->  $feild  =   $value;
            }
            if(!$teacher->save()){
                throw new   \Exception('教务人员信息变更失败');
            }

            //教务人员，用户信息变更
            $userInfo   =   $teacher->userInfo;
            foreach($userData as $feild => $value)
            {
                $userInfo    ->  $feild  =   $value;
            }
            if(!$userInfo->save()){
                throw new   \Exception('教务人员用户信息变更失败');
            }

            //处理 老师、考试项目 关联关系 TODO:Zhoufuxiang 2016-3-30
            $teacherSubject = $this->handleTeacherSubject($subjects, $id, $user);

            $connection->commit();
            return $teacher;

        } catch(\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }

    }

    public function editSpInvigilator($id, $userData, $teacherData,$subjects){
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //教务人员信息变更
            $teacher    =   $this   ->  find($id);
            if(!$teacher){
                throw new   \Exception('没有找到该教务人员');
            }
            foreach($teacherData as $feild => $value) {
                $teacher    ->  $feild  =   $value;
            }
            if(!$teacher->save()){
                throw new   \Exception('教务人员信息变更失败');
            }

            //查询教师编号是否已经被别人使用
            $code = $this->where('code', $teacherData['code'])->where('id','<>',$id)->first();
            if(!empty($code)){
                throw new \Exception('该教师编号已经有别人使用！');
            }
            //教务人员用户信息变更
            $userInfo   =   $teacher->userInfo;
//            $roleId     =   $userData['type'];
            foreach($userData as $feild => $value) {
                $userInfo    ->  $feild  =   $value;
            }
            if(!$userInfo->save()){
                throw new   \Exception('教务人员用户信息变更失败');
            }
//            $connection = DB::connection('sys_mis');
//            $connection->table('sys_user_role')->where('user_id', $id)->where('role_id', $roleId)->update(['role_id'=>Common::getRoleIdByTeacherType($teacher['type'])]);
            
            //插入老师-考试项目 关系 TODO:zhouqiang 2016-3-30
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            if(count($subjects)>0){
                foreach ($subjects as $subject) {
                    $result = TeacherSubject::where('teacher_id','=',$id)->where('subject_id','=',$subject)->first();
                    if($result){
                        continue;   //存在，则跳过

                    }else{
                        $subjectData = [
                            'teacher_id'        => $id,
                            'subject_id'        => $subject,
                            'created_user_id'   => $user->id,
                        ];
                        if(!TeacherSubject::create($subjectData)){
                            throw new \Exception('老师-考试项目关系绑定失败！');
                        }
                    }
                }
            }
            $connection->commit();
            return $teacher;

        } catch(\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 获取监考老师列表
     * @return mixed
     * @throws \Exception
     */
    public function getTeacherList($formData)
    {
        try{
            $teacher = $this->where('type', 1);
                if(!empty($formData)){
                    if(count($formData) == 1){
                        //$teacher->where('id', '<>', implode(',', $formData));
                        $teacher    ->  whereNotIn('id',$formData);
                    }else{
                        $teacher->whereNotIn('id', $formData);
                    }
                }
            $teacher = $teacher->select(['id', 'name'])->get();
            return $teacher;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获得与考站想关联的老师
     * @param $exam_id
     * @return mixed
     * @internal param array $stationIds
     */
    public function stationTeacher($exam_id)
    {
        return $this
            -> leftJoin('exam_station','exam_station.exam_id','=','station_teacher.exam_id')
            -> leftJoin('station_teacher',
                function ($join) {
                    $join->on('station_teacher.user_id' , '=' , $this->table . '.id');
                })
            -> leftJoin('station',
                function ($join) {
                    $join->on('station.id','=','exam_station.station_id');
                })

            -> where('exam_station.exam_id' , $exam_id)
            -> select([
                $this->table . '.id as teacher_id',
                $this->table . '.name as teacher_name',
                $this->table . '.type as teacher_type',
                $this->table . '.status as teacher_status',
                'station.id as station_id',
                'station.name as station_name',
                'station.type as station_type',
                'station.code as station_code',
            ])
            -> get();
    }


    /**
     * 微信端学生成绩查询相关的sp老师
     * @param $exam_id
     * @return mixed
     * @internal param array $stationIds
     *  @author   zhouqiang
     */

    public function getSpTeacher($station,$examId)
    {
        $spTeacher =  Teacher::leftJoin('station_teacher', function($join){
                    $join -> on('teacher.id', '=', 'station_teacher.user_id');
                })
                ->where('station_teacher.station_id', $station)
                ->where('station_teacher.exam_id', $examId)
                ->where('teacher.type', 2)
                ->first();

        return $spTeacher;
    }

    /**
     * 判断老师模板表头及列数 TODO: zhoufuxiang 2016-03-21
     */
    public function judgeTemplet($data, $type)
    {
        switch ($type){
            case 3: $name = 'osce.importForCnToEn.patrolTeacher';
                    $nameToEn = config($name);                      //巡考老师
                    break;
            case 2:                                                 //SP
            case 1: $name = 'osce.importForCnToEn.teacher';
                    $nameToEn = config($name);                      //监考老师
                    break;
            default:
                    throw new \Exception('老师类型不对');
        }

        try {
            //获取中英文对照表头
            foreach ($nameToEn as $index => $item) {
                $config[] = $index;
            }

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

            return $name;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 导入老师
     * @param $teacherDatas
     * @param $operator
     * @param $type
     *
     * @author Zhoufuxiang 2016-03-30
     * @return int
     * @throws \Exception
     */
    public function importTeacher($teacherDatas, $operator, $type)
    {
        $backArr = [];

        try {
            $sucNum = 0;    //导入成功的老师数
            $exiNum = 0;    //已经存在的老师数
            //将数组导入到模型中的addInvigilator方法
            foreach ($teacherDatas as $key => $teacherData)
            {
                //性别处理
                $teacherData['gender'] = $this->handleSex($teacherData['gender']);
                //老师类别处理
                if($type ==3){
                    $teacherData['type'] = $type;
                }else{
                    $teacherData['type'] = $this->handleType($teacherData['type']);
                }

                //根据老师类型获取对应的角色ID
                $role_id = Common::getRoleIdByTeacherType($teacherData['type']);

                //姓名不能为空
                if (empty(trim($teacherData['name']))) {
                    if (!empty($teacherData['idcard']) && !empty($teacherData['mobile'])) {
                        $backArr[] = ['key' => $key + 2, 'title' => 'name'];
                    }
                    continue;
                }
                //验证身份证号
                if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $teacherData['idcard'])) {
                    throw new \Exception('第' . ($key + 2) . '行身份证号不符规格，请修改后重试！');
                }
                //验证手机号
                if (!preg_match('/^1[3|5|7|8]{1}[0-9]{9}$/', $teacherData['mobile'])) {
                    throw new \Exception('第' . ($key + 2) . '行手机号不符规格，请修改后重试！');
                }

                //根据条件：查找用户是否有账号和密码
                $user = User::where(['username' => $teacherData['mobile']])->select(['id','idcard'])->first();
                if ($user) {
                    if($user->idcard != $teacherData['idcard']){
                        $result = User::where('idcard','=',$teacherData['idcard'])->first();
                        if($result){
                            throw new \Exception('第' . ($key + 2) . '行身份证号已经存在，请修改后重试！');
                        }
                    }
                    //根据用户ID查找老师 是否已经存在
                    $teacher = $this->where('id', $user->id)->first();
                } else {
                    $result = User::where('idcard','=',$teacherData['idcard'])->first();
                    if($result){
                        throw new \Exception('第' . ($key + 2) . '行身份证号已经存在，请修改后重试！');
                    }
                    $teacher = false;
                }

                //老师已存在,则 跳过
                if ($teacher) {
                    $exiNum++;   continue;
                }
                //用户数据
                $userData = [
                    'name'   => $teacherData['name'],
                    'gender' => $teacherData['gender'],
                    'idcard' => $teacherData['idcard'],
                    'mobile' => $teacherData['mobile'],
                    'code'   => $teacherData['code'],
                    'avatar' => $teacherData['avatar'],
                    'email'  => $teacherData['email']
                ];
                //老师数据
                $teacherData = [
                    'name'      => $teacherData['name'],
                    'type'      => $teacherData['type'],
                    'code'      => $teacherData['code'],
                    'case_id'   => null,
                    'status'    => 0,
                    'create_user_id' => $operator->id,
                    'description'    => $teacherData['description']
                ];

                //添加老师
                if (!$this->addInvigilator($role_id, $userData, $teacherData,[])) {
                    throw new \Exception('老师导入数据失败，请修改后重试');
                } else {
                    $sucNum++;      //添加成功的老师个数
                }

            } /*循环结束*/

            $message = "成功导入{$sucNum}个老师";
            if ($exiNum) {
                $message .= "，有{$exiNum}个老师已存在( 可能为其他类型)";
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
     * 老师类别处理
     */
    public function handleType($type)
    {
        if ($type == '巡考老师') {
            return 3;
        }
        return 1;
    }

    public function openData($teacher){
        $teacher->archived  =   0;
        if(!$teacher->save())
        {
            throw new \Exception('重新启用老师失败');
        }
        return $teacher;
    }
}