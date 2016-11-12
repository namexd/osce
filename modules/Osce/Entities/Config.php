<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/11
 * Time: 13:55
 */

namespace Modules\Osce\Entities;


class Config extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'config';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'cate', 'type', 'value', 'description'];

    /**
     * 将数据插入到配置数据库中
     * @param array $formData 此为一个二维数组
     * @return static
     * @throws \Exception
     */
    public function store(array $formData)
    {
        try {
            //$key就是字段名,$item为键值数组，有必要再遍历一次，将每个值都插入数据库
            foreach ($formData as $key => $item) {
                //如果$item是数组的话，就说明是多选
                $cate = strstr($key, '_', true);  //从传入的字段表里获取cate
                $nameLen = strpos($key, '_'); //获取_在字符串中第一次出现的位置
                $name = substr($key, $nameLen + 1); //获取_后的字符串
                if (is_array($item)) {
                    $type = '多选';
                    $item = json_encode($item);
                } elseif (is_string($item)) {
                    $type = '字符串';
                } elseif (is_bool($item)) {
                    $type = '布尔值';
                }
                //将数据插入数据库
                $data = [
                    'name' => $name,
                    'cate' => $cate,
                    'type' => $type,
                    'value' => $item,
                ];
                //先判断是修改还是添加
                if (Config::where('name', $name)->where('cate', $cate)->get()->isEmpty()) { //如果是空，就证明是添加
                    if ($result = $this->create($data)) {
                        return $result;
                    } else {
                        throw new \Exception('配置插入数据库失败！');
                    }
                } else {
                    //否则就是编辑
                    return $this->saveDBConfig($data);
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 具体的服务器数据的修改方法
     * @param $data
     * @return bool
     * @return bool
     * @throws \Exception
     * @auther Jiangzhiheng <Jiangzhiheng@misrobot.com>
     */
    private function saveDBConfig($data)
    {
        try {
            $config = $this->where('name', $data['name'])->where('cate', $data['cate'])->first();
            $config->value = $data['value'];

            if (!$result = $config->save()) {
                throw new \Exception('保存失败，请重试！');
            }
            return $result;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 将配置项写进文件
     * @param array $formData
     * @throws \Exception
     */
    public function config(array $formData)
    {
        try {
            //获取配置文件的内容
            $config = include MESSAGE_CONFIG;

            //将每一项改写读取到的数组
//          $config['default'] = 'env(\'MESSAGE_DRIVER\'， \'' . $formData['default'] . '\'' . "),\n";
            $config['messages']['sms']['cnname'] = $formData['sms_cnname'];
            $config['messages']['sms']['url'] = $formData['sms_url'];
            $config['messages']['sms']['username'] = $formData['sms_username'];
            $config['messages']['sms']['password'] = $formData['sms_password'];

            $config['messages']['wechat']['use_alias'] =
                <<<EOT
env('WECHAT_USE_ALIAS', $formData[wechat_use_alias])
EOT;
            $config['messages']['wechat']['app_id'] = "env('WECHAT_APP_ID','" . $formData['wechat_app_id'] . "')," . '//必填';
            $config['messages']['wechat']['secret'] = "env('WECHAT_SECRET','" . $formData['wechat_secret'] . "')," . '//必填';
            $config['messages']['wechat']['token'] = "env('WECHAT_TOKEN','" . $formData['wechat_token'] . "')," . '// 必填,http://good-doctor.cn/hx/wx_sample.php';
            $config['messages']['wechat']['encoding_key'] = "env('WECHAT_ENCODING_KEY','" . $formData['wechat_encoding_key'] . "')," . '// 加密模式需要，其它模式不需要';

            $config['messages']['email']['server'] = $formData['email_server'];
            $config['messages']['email']['port'] = $formData['email_port'];
            $config['messages']['email']['ssl'] = $formData['email_ssl'];
            $config['messages']['email']['username'] = $formData['email_username'];
            $config['messages']['email']['password'] = $formData['email_password'];


            //将修改后的数据重新写回文件
            $str = "<?php\nreturn " . var_export($config, true) . ";";
            $str = str_replace("'env(", 'env(', $str);
            $str = str_replace(")',", '),', $str);

            $str = stripslashes($str);
            if (!file_put_contents(MESSAGE_CONFIG, $str)) {
                throw new \Exception('配置插入文件失败！');
            };
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}