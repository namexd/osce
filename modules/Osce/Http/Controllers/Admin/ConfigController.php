<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/8
 * Time: 18:48
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Area;
use Modules\Osce\Entities\Config;
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
        $tempConfig = config('message');
        //从数据库获取配置
        $tempDB = Config::all();
        if (count($tempDB) != 0) {
            $tempDB[0]['value'] = json_decode($tempDB[0]->value);
        } else {
            $tempDB[0]['value'] = ['0' => '1'];
        }
        return view('osce::admin.sysmanage.system_settings_media', ['tempConfig' => $tempConfig, 'tempDB' => $tempDB]);
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
            DB::connection('osce_mis')->beginTransaction();
            //验证
            $this->validate($request, [
                'message_type' => 'array',
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
                'email_protocol' =>'required',
                'email_ssl' => 'required',
                'email_username' => 'required',
                'email_password' => 'required'
            ]);

            //获取输入值
            $formData = $request->only('message_type');
            $file = $request->only('sms_cnname', 'sms_url', 'sms_username', 'sms_password', 'wechat_use_alias',
                'wechat_app_id'
                , 'wechat_secret', 'wechat_token', 'wechat_encoding_key', 'email_server', 'email_port', 'email_protocol', 'email_ssl',
                'email_username', 'email_password');

            //将拿到的数组分别作处理
            //如果是要插入数据库的就插入数据库
            $result =  $config->store($formData);

            if (!$result) {
                DB::connection('osce_mis')->rollBack();
            }

            //如果是要插入配置文件
            $result = $config->config($file);
            if (!$result) {
                DB::connection('osce_mis')->rollBack();
            }

            DB::connection('osce_mis')->commit();
            return redirect()->route('osce.admin.config.getIndex');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 场所类型的配置的着陆页
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
    public function getArea()
    {
        //从数据库之中获取配置
        $data = Area::all();

        return view('osce::admin.sysmanage.system_settings_room', ['data' => $data]);

    }

    /**
     * 场所类型的配置的添加着陆页
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
    public function getAreaStore()
    {
        return view('osce::admin.sysmanage.system_add');
    }

    /**
     * 场所类型的配置的添加逻辑
     * @api GET /osce/admin/invigilator/index
     * @access public
     * @param Request $request
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
    public function postAreaStore(Request $request)
    {
        //验证
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'cate' => 'required|integer',
            'code' => 'required'
        ]);

        //接受数据
        $formData = $request->all();
        $formData['created_user_id'] = \Auth::user()->id;
        try {
            if (!Area::create($formData)) {
                throw new \Exception('数据保存失败！请重试');
            }

            return redirect()->route('osce.admin.config.getArea');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

}