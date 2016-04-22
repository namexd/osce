<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 10:42
 */

namespace Modules\Osce\Repositories;


use App\Entities\SysUserRole;
use App\Entities\SysRoles;
use App\Entities\User;
use DB;

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
        $form_user['username'] = $data['username'];
        $form_user['mobile'] = $data['username'];
        $form_user['openid'] = '';
        $form_user['password'] = bcrypt($password);
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
        $sender = \App::make('messages.sms');
        $sender->send($mobile, '恭喜你已经成功注册OSCE考试系统，请使用手机号进行登录，登录密码:' . $password . ',请不要轻易将密码告诉他人【敏行医学】');
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
        $fileData = [];
        foreach ($fileArray as $item) {
            $ensue = explode('=', $item);
            $fileData[$ensue[0]] = $ensue[1];
        }
        if(!in_array('table ',$fileData)){
            $fileData ['table'] = 0;
        }

//        dd($fileArray,$fileData);
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

            $mobile = $userData['mobile'];
            //根据条件：查找用户是否有账号和密码
            $user   = User::where('username', '=', $mobile)->first();

            if(is_null($user)){
                //设置密码
                $password = (config('config.debug') == true)? '123456': Common::getRandStr(6);
                //注册用户
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


}