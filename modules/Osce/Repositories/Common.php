<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 10:42
 */

namespace Modules\Osce\Repositories;


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
        $pathArray = explode('\\',get_class($this));
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
    static public function getRandStr($length,$word=''){
        $word   =   $word   ===''? '1234567890':$word;
        $str    =   '';

        for($i=0;$i<$length;$i++)
        {
            $randNum    =   rand(0,strlen($word))-1;
            $str        .=   $word[$randNum];
        }
        return $str;
    }
    static public function registerUser($data,$password){
        $form_user              =   $data;
        $form_user['username']  =   $data['username'];
        $form_user['mobile']    =   $data['username'];
        $form_user['openid']    =   '';
        $form_user['password']  =   bcrypt($password);
        $user=\App\Entities\User::create($form_user);
        if($user)
        {
            return $user;
        }
        else
        {
            throw new \Exception('创建用户失败');
        }
    }
}