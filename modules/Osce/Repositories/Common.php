<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 10:42
 */

namespace Modules\Osce\Repositories;


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

    static  public function getRoleIdByTeacherType($type){
        $relation  =   [
            1=>config('osce.invigilatorRoleId'),
//            2=>config('osce.studentRoleId'),
//            3=>config('osce.adminRoleId'),
            2=>config('osce.spRoleId'),
//            5=>config('osce.superRoleId'),
            3=>config('osce.patrolRoleId'),
        ];
        return  $relation[$type];
    }

    /**
     * 通过角色ID，获取对应的老师的类型
     * @param $role_id
     * @author Zhoufuxiang 2016-3-30
     * @return int
     */
    static  public function getTeacherTypeByRoleId($role_id){

        switch ($role_id){
            case config('osce.invigilatorRoleId') : return 1;
                                                    break;
            case config('osce.spRoleId')          : return 2;
                                                    break;
            case config('osce.patrolRoleId')      : return 3;
                                                    break;
        }
    }

    static public function handleTime($time){
        $h = floor($time / 3600);
        $m = floor(($time%3600)/60);
        $s = $time % 60;

        $h = ($h>10)? "$h" : "0$h";
        $m = ($m>10)? "$m" : "0$m";
        $s = ($s>10)? "$s" : "0$s";

        $time = $h.':'.$m.':'.$s;

        return $time;
    }


    static  public function handleRedirect($request,$result){
            $data= $request->headers->all()['referer'][0];
            $fileNameArray =     explode('?',$data);
            $fileArray =     explode('&',$fileNameArray[1]);
            dump($fileArray);
        foreach ($fileArray as $item) {
            dump(strstr($item, '=', true));
            dump(strstr($item, '='));
        }
        dd(123);
            foreach ($fileArray as $value)
                    if($value=='status=1'){




                        return $fileArray;
                        
//                    return view('osce::admin.index.layer_success',[
//                        'data'=>$result,
////                        'table'=>,
//                        'tr'=>'item-id-0',
//
//                    ]);

                }else{
                    return false;
                }
    }
}