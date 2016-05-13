<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 10:42
 */

namespace Modules\Osce\Repositories;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamAbsent;
use Modules\Osce\Entities\ExamOrder;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\ExamSpecialScore;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\QuestionBankEntities\ExamMonitor;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Student;
use App\Entities\SysUserRole;
use App\Entities\SysRoles;
use App\Entities\User;
use DB;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;

class Common
{
    /**
     * 拼装leftJoin的方法
     * @param array $tableName
     * @param array $param
     */
    protected function sqlBuilder(array $tableName, array $param = [])
    {
        //如果参数为空，就手动给一个空数组
        if (empty($param)) {
            $param = [
                'where' => [],
                'whereIn' => [],
                'orWhere' => [],
                'whereRaw' => [],
                'order' => [],
            ];
        }

        //获取当前的moulde名字
        $pathArray = explode('\\', get_class($this));
        $thisMoulde = array_pop($pathArray);    //删除数组中的最后一个元素
        $modelNameToTableNameArray = [
            $thisMoulde => $this->table
        ];
        dd($modelNameToTableNameArray);

        //获取模型名和数据表的关联清单
//        foreach ($tableName as $item) {
//            $model =
//        }
    }

    static public function getRandStr($length, $word = '')
    {
        $word = $word === '' ? '1234567890' : $word;
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $randNum = rand(0, strlen($word) - 1);
            $str .= $word[$randNum];
        }
        return $str;
    }

    static public function registerUser($data, $password)
    {
        $form_user = $data;
        $form_user['username']  = $data['username'];
        $form_user['mobile']    = $data['username'];
        $form_user['openid']    = '';
        $form_user['password']  = bcrypt($password);
        $user = \App\Entities\User::create($form_user);
        if ($user) {
            return $user;
        } else {
            throw new \Exception('创建用户失败');
        }
    }

    public function getUserList()
    {
        $noAdminRole = [
            config('config.teacherRoleId'),
            config('config.examineeRoleId'),
            config('config.spRoleId'),
            config('config.superRoleId'),
            config('config.patrolRoleId')
        ];

        return User::select('users.id', 'users.username', 'users.name', 'users.gender', 'users.mobile', 'users.lastlogindate')
            ->leftJoin('sys_user_role', function ($join) {
                $join->on('users.id', '=', 'sys_user_role.user_id');
            })
//                -> where('sys_user_role.role_id','=',config('osce.adminRoleId',3))
            ->whereNotIn('sys_user_role.role_id', $noAdminRole)
            ->paginate(config('osce.page_size'));
    }

    public function createAdminUser($data)
    {
        if (config('APP_DEBUG')) {
            $password = 123456;
        } else {
            $password = 123456;
//            $password   =   Common::getRandStr(6);
        }

        DB::beginTransaction();
        try {
            $user = Common::registerUser(['username' => $data['mobile'],], $password);
            if (is_null($user)) {
                throw new \Exception('创建用户失败');
            }
            $user->name = $data['name'];
            $user->gender = $data['gender'];

            DB::table('sys_user_role')->insert([
                'role_id' => config('osce.adminRoleId', 3),
                'user_id' => $user->id,
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            if (!$result = $user->save()) {
                throw new \Exception('初始化资料失败');
            }

            DB::commit();
            $this->sendRegisterEms($data['mobile'], $password);
            return $result;

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function relativeAdminUser($user)
    {
        if (DB::table('sys_user_role')->where('role_id', '=', config('osce.adminRoleId', 3))->where('user_id', '=',
            $user->id)->count()
        ) {
            throw new \Exception('该管理员已经添加了');
        }
        DB::table('sys_user_role')->insert([
            'role_id' => config('osce.adminRoleId', 3),
            'user_id' => $user->id,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function updateAdminUser($id, $data)
    {
        try {
            //查询手机号是否已经存在
            $result = User::where('mobile', $data['mobile'])->where('id', '<>', $id)->first();
            if ($result) {
                throw new \Exception('手机号已经存在');
            }
            $user = User::find($id);
            foreach ($data as $feild => $value) {
                $user->$feild = $value;
            }
            return $user->save();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    static public function sendRegisterEms($mobile, $password)
    {
        $message = '恭喜你已经成功注册OSCE考试系统，请使用手机号进行登录，登录密码:' . $password . ',请不要轻易将密码告诉他人【敏行医学】';
        \App\Repositories\Common::sendSms($mobile, $message);

//        $sender = \App::make('messages.sms');
//        $sender->send($mobile, '恭喜你已经成功注册OSCE考试系统，请使用手机号进行登录，登录密码:' . $password . ',请不要轻易将密码告诉他人【敏行医学】');
    }

    /**
     * 判断对象集合是否为空
     * 如果为空，报错
     * @param object $obj
     * @param string $message
     * @param int $code
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-10 15:01
     */
    static public function objIsEmpty($obj, $code = -999, $message = '系统错误')
    {
        if (is_object($obj)) {
            if (!$obj->isEmpty()) {
                return true;
            } else {
                throw new \Exception($message, $code);
            }
        } else {
            throw new \Exception('系统错误，请重试');
        }
    }

    /**
     * 判断对象是否为空
     * 如果为空，报错
     * @param $value
     * @param string $message
     * @param int $code
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-10 15:19
     */
    static public function valueIsNull($value, $code = -999, $message = '系统错误')
    {
        if (!is_null($value)) {
            return true;
        } else {
            throw new \Exception($message, $code);
        }
    }

    /**
     * 获取 不为null的值
     * @param $object
     * @param $value
     * @param $item
     *
     * @author Zhoufuxiang 2016-04-14
     * @return object
     */
    public static function getNotNullValue($object, $value, $item){
        if(!is_null($item->$value)){
            $object -> $value = $item->$value;
        }
        return $object;
    }

    /**
     * 求一维数组的最大公约数
     * @param array $arrays
     * @param null $temp
     * @return mixed|null
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-21 17:52
     */
    static public function mixCommonDivisor(array $arrays, $temp = null)
    {
        if (is_null($temp)) {
            $arrays = array_unique($arrays);
            sort($arrays);
            $temp = $arrays[0];
            self::valueIsNull($temp);
        }

        for ($i = $temp; $i > 1; $i--) {
            $result = 0;
            foreach ($arrays as $array) {
                if ($array % $i) {
                    $result++;
                }
            }
            if ($result == 0) {
                break;
            }
        }
        return $i;
    }

    static public function getRoleIdByTeacherType($type)
    {
        $relation = [
            1 => config('osce.invigilatorRoleId'),
//            2=>config('osce.studentRoleId'),
//            3=>config('osce.adminRoleId'),
            2 => config('osce.spRoleId'),
//            5=>config('osce.superRoleId'),
            3 => config('osce.patrolRoleId'),
        ];
        return $relation[$type];
    }

    /**
     * 通过角色ID，获取对应的老师的类型
     * @param $role_id
     * @author Zhoufuxiang 2016-3-30
     * @return int
     */
    static public function getTeacherTypeByRoleId($role_id)
    {

        switch ($role_id) {
            case config('osce.invigilatorRoleId') :
                return 1;
                break;
            case config('osce.spRoleId')          :
                return 2;
                break;
            case config('osce.patrolRoleId')      :
                return 3;
                break;
        }
    }

//    static public function handleTime($time)
//    {
//        $h = floor($time / 3600);
//        $m = floor(($time % 3600) / 60);
//        $s = $time % 60;
//
//        $h = ($h > 10) ? "$h" : "0$h";
//        $m = ($m > 10) ? "$m" : "0$m";
//        $s = ($s > 10) ? "$s" : "0$s";
//
//        $time = $h . ':' . $m . ':' . $s;
//
//        return $time;
//    }


    static public function handleRedirect($request, $result)
    {
        $data = $request->headers->all()['referer'][0];

        $fileNameArray = explode('?', $data);
        if (count($fileNameArray)<2){
            return false;
        }

        $fileArray = explode('&', $fileNameArray[1]);
        if (is_null($fileArray)) {
            return false;
        }

//        dd($fileArray);
        $fileData = [];
        foreach ($fileArray as $item) {

            $ensue = explode('=', $item);
            $fileData[$ensue[0]] = $ensue[1];
        }
        if(!array_key_exists('table',$fileData)){
            $fileData['table'] = '';
        }
        foreach ($fileArray as $value)
            if ($value == 'status=1') {
                return view('osce::admin.index.layer_success', [
                    'result' => $result,
                    'fileArray' => $fileData,
                ]);
            }
    }


        /**
         * 删除关联关系，删除失败，报错
         *
         * @param $value
         * @param string $message
         * @param int $code
         * @return bool
         * @throws \Exception
         * @author Zhoufuxiang
         * @time 2016-04-13 09:55
         */
        static public function delRelation($subject, $values, $message = '系统错误', $code = -999)
    {
        if (!$subject->$values->isEmpty()) {
            //删除对应关联关系
            foreach ($subject->$values as $value) {
                $pivot = $value->pivot;
                if (!is_null($pivot)) {
                    if (!$pivot->delete()) {
                        throw new \Exception($message, $code);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 归档
     * @param $model
     * @param $id
     * @param string $message
     * @param array $params
     *
     * @author Zhoufuxiang 2016-04-13
     * @return bool
     * @throws \Exception
     */
    public static function archived($model, $id, $message = '系统错误', $params = [])
    {
        $item = $model->where('id', '=', $id)->first();
        if (!is_null($item)) {
            $item->archived = 1;
            //其他字段参数不为空
            if (!empty($data)) {
                foreach ($params as $key => $param) {
                    $item->$key = $param;
                }
            }

            if (!$item->save()) {
                throw new \Exception($message);
            }
        }

        return true;
    }
    /**
     * 还原归档
     * @param $model
     * @param $id
     * @param string $message
     * @param array $params
     *
     * @author Zhoufuxiang 2016-04-13
     * @return bool
     * @throws \Exception
     */
    public static function resetArchived($model, $id, $message = '系统错误', $params = [])
    {
        $item = $model->where('id', '=', $id)->first();
        if (!is_null($item)) {
            $item->archived = 0;    //重置归档

            //其他字段参数不为空
            if (!empty($data)) {
                foreach ($params as $key => $param) {
                    $item->$key = $param;
                }
            }

            if (!$item->save()) {
                throw new \Exception($message);
            }
        }

        return true;
    }

    /**
     *
     * @param $time
     * @return string
     *
     * @author Zhoufuxiang
     * @date   2016-03-22 11:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function handleTime($time){
        $h = floor($time / 3600);
        $m = floor(($time%3600)/60);
        $s = $time % 60;

        $h = ($h>10)? "$h" : "0$h";
        $m = ($m>10)? "$m" : "0$m";
        $s = ($s>10)? "$s" : "0$s";

        $time = $h.':'.$m.':'.$s;

        return $time;
    }

    /**
     * 处理用户 账户、角色
     * @param $userData
     * @param $role_id
     * @return static
     * @throws \Exception
     *
     * @author Zhoufuxiang
     * @date   2016-04-22 11:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function handleUser($userData, $role_id)
    {
        $connection = DB::connection('sys_mis');
        $connection ->beginTransaction();
        try{
            //查询是否有对应角色
            $roles = SysRoles::where('id', '=', $role_id)->first();
            if (is_null($roles)) {
                throw new \Exception('没有对应的角色，请去新增对应角色，或者查看角色配置！');
            }

            $mobile = trim($userData['mobile']);
            //根据条件：查找用户是否有账号和密码
            $user   = User::where('username', '=', $mobile)->first();

            if(is_null($user)){
                //设置密码
                $password = (config('osce.debug') == true)? '123456': Common::getRandStr(6);
                //注册用户
                $userData['username'] = $mobile;
                unset($userData['mobile']);
                $user = Common::registerUser($userData, $password);
                //给用户发送短信
                Common::sendRegisterEms($mobile, $password);
                //给用户分配角色
                Common::addUserRoles($user, $role_id);

            }else{
                //给用户分配角色
                Common::addUserRoles($user, $role_id);

                //修改用户基本信息
                foreach($userData as $feild=> $value) {
                    $user    ->  $feild  =   $value;
                }
                if(!$user -> save()) {
                    throw new \Exception('用户修改失败，请重试！');
                }
            }

            $connection->commit();
            return $user;

        } catch (\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 用户角色处理
     * @param $user
     * @param $role_id
     * @return mixed
     * @throws \Exception
     *
     * @author Zhoufuxiang
     * @date   2016-04-22 11:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function addUserRoles($user, $role_id)
    {
        $superRole   = config('osce.superRoleId', 5);
        //查询用户是否是超级管理员
        $superUser   = SysUserRole::where('user_id','=',$user->id)->where('role_id','=',$superRole)->first();
        if (!is_null($superUser)){
            throw new \Exception('该用户为超级管理员，不能添加，请修改！');
        }
        //查询用户角色（是否已经拥有了该角色）
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
     * 身份证号验证
     * @param $examId
     * @param $data
     * @return bool
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-5-11 10:10
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function checkIdCard($examId, $data)
    {
        $idCard = trim($data['idcard']);
        $mobile = trim($data['mobile']);
        //1、验证身份证的正确性
//        if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $idCard) || !preg_match('/^((s?[A-Za-z])|([A-Za-z]{2}))d{6}((([0-9aA]))|([0-9aA]))$/', $idCard)) {
//            throw new \Exception('第' . ($key) . '行身份证号不符规格，请修改后重试！');
//        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $idCard)) {
            throw new \Exception('身份证号不符规格，请修改后重试！');
        }

        //2、查询同一场考试中，身份证号是否已经存在
        $result = Student::where('exam_id', '=', $examId)->where('idcard','=', $idCard)->first();
        if(!is_null($result)){
            throw new \Exception('身份证号已经存在，请修改后重试！');
        }
        //3、查询用户表中身份证号是否重复
          //(1)、查询同组数据中，是否已存在对应用户
        $user = User::where('username', '=', $mobile)->select(['id', 'idcard'])->first();
        if(!is_null($user))
        {
            //查询其余人是否已经使用该身份证号
            $result = User::where('id', '<>', $user->id)->where('idcard', '=', $idCard)->first();
            if(!is_null($result)){
                throw new \Exception('身份证号已经存在，请修改后重试！');
            }
        }else
        {
            $user = User::where('idcard', '=', $idCard)->first();
            if(!is_null($user)){
                throw new \Exception('身份证号已经存在，请修改后重试！');
            }
        }
        return true;
    }

    /**
     * 获取当前考试场次
     * @param $exam_id
     * @return $screening_id
     *
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-05-13
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function getScreeningId($exam_id)
    {
        $ExamScreening = new ExamScreening();
        $screenObject  = $ExamScreening->getExamingScreening($exam_id);
        //获取当前场次
        if(is_null($screenObject))
        {
            $screenObject = $ExamScreening->getNearestScreening($exam_id);
            if(is_null($screenObject)){
                throw new \Exception('当前没有正在进行的考试场次');
            }
        }

        return $screenObject->id;
    }

    /**
     * 清空考试 对应考试数据（不包括排考数据）
     * @param $id
     * @return int|string
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-04-14 15:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public static function emptyExamData($id)
    {
        $connection = DB::connection('osce_mis');
        $connection ->beginTransaction();
        try {
            $Exam = new Exam();
            //获得当前exam的实例
            $examObj = $Exam->doingExam($id);
            if(is_null($examObj)){
                throw new \Exception('没有找到对应考试！');
            }
            //获取与考场相关的流程
            $examScreening    = ExamScreening::where('exam_id', '=', $id);
            $examScreeningObj = $examScreening->select('id')->get();
            $examScreeningIds = $examScreeningObj->pluck('id');
            //获取考试结果信息
            $examResults      = ExamResult::whereIn('exam_screening_id', $examScreeningIds)->select('id')->get();
            $examResultIds    = $examResults->pluck('id');

            //清除腕表使用记录
            $watchLog = WatchLog::where('id','>',0)->get();
            if(!$watchLog->isEmpty())
            {
                $watchLog = WatchLog::where('id','>',0)->delete();
                if(!$watchLog){
                    throw new \Exception('删除腕表使用记录失败！');
                }
            }
            //修改腕表使用状态
            $watchStatus = Watch::where('status', '<>', 0)->get();
            if(!$watchStatus->isEmpty())
            {
                foreach ($watchStatus as $watchStatu) {
                    $watchStatu->status = 0;
                    if(!$watchStatu->save()){
                        throw new \Exception('修改腕表状态失败！');
                    }
                }
            }
            //删除考试得分
            $examScores = ExamScore::whereIn('exam_result_id', $examResultIds)->get();
            if (!$examScores->isEmpty()) {
                foreach ($examScores as $valueS) {
                    if(!$valueS->delete()){
                        throw new \Exception('删除考试得分失败！');
                    }
                }
            }
            //删除考试特殊评分项扣分
            $examSpecialScore = ExamSpecialScore::whereIn('exam_result_id', $examResultIds)->get();
            if (!$examSpecialScore->isEmpty()) {
                foreach ($examSpecialScore as $valueSs) {
                    if(!$valueSs->delete()){
                        throw new \Exception('删除考试特殊评分项扣分失败！');
                    }
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
                            if(!$examAttach->delete()){
                                throw new \Exception('删除考核点对应的图片、语音失败！');
                            }
                        }
                    }
                    //再删除对应考试结果数据
                    if(!$valueR->delete()){
                        throw new \Exception('删除对应考试结果数据失败！');
                    }
                }
            }
            //删除替考记录
            $examMonitors = ExamMonitor::where('exam_id', '=', $id)->get();
            if(!$examMonitors->isEmpty()){
                foreach ($examMonitors as $examMonitor) {
                    if(!$examMonitor->delete()){
                        throw new \Exception('删除替考记录失败！');
                    }
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
            $examStationStatus = ExamStationStatus::where('exam_id', '=', $id)->where('status', '<>', 0)->get();
            if(!$examStationStatus->isEmpty()){
                foreach ($examStationStatus as $item) {
                    $item->status = 0;
                    if(!$item->save()){
                        throw new \Exception('修改考试-场次-考站状态失败！');
                    }
                }
            }
            //更改考试场次状态
            $examScreenings = $examScreening->where('status', '<>', 0)->get();
            if (!$examScreenings->isEmpty()) {
                foreach ($examScreenings as $screening) {
                    $screening->update(['status' => 0]);       //TODO 更改状态为0
                }
            }
            //删除缺考
            $examAbsent = ExamAbsent::where('exam_id', '=', $id)->get();
            if(!$examAbsent->isEmpty()){
                foreach ($examAbsent as $examAbsen) {
                    if(!$examAbsen->delete()){
                        throw new \Exception('删除缺考失败！');
                    }
                }
            }
            //删除考试队列
            $examQueue = ExamQueue::where('exam_id', '=', $id)->get();
            if(!$examQueue->isEmpty())
            {
                foreach ($examQueue as $examQueu) {
                    if(!$examQueu->delete()){
                        throw new \Exception('删除考试队列失败！');
                    }
                }
            }
            //更改考生排序状态  TODO:（ExamOrder表中数据是在智能排考时添加进去的）
            $examOrder = ExamOrder::where('exam_id', '=', $id)->where('status', '<>', 0)->get();
            if(!$examOrder->isEmpty())
            {
                //TODO 更改状态为0（0为未绑定腕表）
                foreach ($examOrder as $examOrde) {
                    $examOrde->status = 0;
                    if(!$examOrde->save()){
                        throw new \Exception('修改考生排序状态 失败！');
                    }
                }
            }
            //更改考试状态
            if($examObj->status != 0)
            {
                //TODO 更改状态为0（0为未开考）
                $examObj->status = 0;
//                $result = $this->where('id', '=', $id)->update(['status' => 0]);
                if(!$examObj->save()){
                    throw new \Exception('修改考试状态 失败！');
                }
            }

            $connection->commit();
            return 11111;

        } catch (\Exception $ex)
        {
            $connection->rollBack();
            return $ex->getMessage();
        }
    }
}