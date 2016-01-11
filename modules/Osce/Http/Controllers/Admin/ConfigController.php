<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/8
 * Time: 18:48
 */

namespace modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use modules\Osce\Entities\Config;
use Modules\Osce\Http\Controllers\CommonController;
use DB;


class ConfigController extends CommonController
{
    /**
     * 配置的着陆页
     * @api GET /osce/admin/invigilator/index
     * @access public
     * @return redirect
     * @internal param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     *
     * @internal param Teacher $teacher
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-11 11：48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getIndex()
    {
        //从文件获取配置数组
        $tempConfig = config(message);
        //从数据库获取配置
        $tempDB = Config::all();
        return view('', ['tempConfig' => $tempConfig, 'tempDB' => $tempDB]);
    }

    /**
     * 插入配置数据
     * @api POST /osce/admin/invigilator/store
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     *
     * @param Config $config
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @internal param Teacher $teacher
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-09 16：48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStore(Request $request, Config $config)
    {
        try {
            DB::beginTransaction();
            //验证
            $this->validate($request, [
                'type' => 'array',
                'share' => 'array',
                'sms_cnname' => 'required',
                'sms_url' => 'required|url',
                'sms_username' => 'required',
                'sms_password' => 'required',
                'wechat_use_alias' => 'required',
                'wechat_app_id' => 'required',
                'wechat_secret' => 'required',
                'wechat_token' => 'required',
                'wechat_encoding_key' => 'required',
                'email_server' => 'required',
                'email_port' => 'required|int',
                'email_ssl' => 'required|boolean',
                'email_username' => 'required',
                'email_password' => 'required'
            ]);

            //获取输入值
            $formData = $request->only('type', 'share');
            $file = $request->only('sms_cnname', 'sms_url', 'sms_username', 'sms_password', 'wechat_use_alias',
                'wechat_app_id'
                , 'wechat_secret', 'wechat_token', 'wechat_encoding_key', 'email_server', 'email_port', 'email_ssl',
                'email_username', 'email_password');

            //将拿到的数组分别作处理
            //如果是要插入数据库的就插入数据库
            $result =  $config->store($formData);
            if (!$result) {
                DB::rollBack();
            }

            //如果是要插入配置文件
            $result = $config->config();
            if (!$result) {
                DB::rollBack();
            }

            DB::commit();
            return redirect()->route('');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex);
        }
    }
}