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
            $tempDB = [];
            $tempDB[0]['value'] = [];
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
            $this->getSetWechat($file['wechat_use_alias'],$file['wechat_app_id'],$file['wechat_secret'],$file['wechat_token'],$file['wechat_encoding_key']);
            if (!$result) {
                DB::connection('osce_mis')->rollBack();
            }

            //如果是要插入配置文件
            $result = $config->config($file);
            $this->getSetMail(
                $file['email_server'],
                $file['email_port'],
                $file['email_protocol'],
                $file['email_ssl'],
                $file['email_username'],
                $file['email_password']);
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
            'name'  => 'required|unique:osce_mis.area,name',
            'description' => 'required',
            'cate'  => 'required|integer|unique:osce_mis.area,cate',
            'code'  => 'required'
        ],[
            'name.unique'   =>  '名称必须唯一',
            'cate.unique'   =>  '类别必须唯一',
        ]);

        //接受数据
        $formData = $request->all();
        $formData['created_user_id'] = \Auth::user()->id;
        try {
            if(Area::where('cate', $formData['cate'])->first()){
                throw new \Exception('该数字 类别已存在，请重新填写！');
            }
            if (!Area::create($formData)) {
                throw new \Exception('数据保存失败！请重试');
            }

            return redirect()->route('osce.admin.config.getArea');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 删除考试区域
     * @url GET /osce/admin/config/postDelArea
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        考试区域id(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelArea(Request $request, Area $area){
        //验证
        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = intval($request->get('id'));
        try{
            $result = $area->deleteArea($id);
            if($result ==true){
                return $this->success_data('删除成功！');
            }

        } catch(\Exception $ex){
            return $this->fail($ex);
        }
    }

    /**
     * 判断名称是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
            'title'     => 'required',
            'name'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $title  = $request  -> get('title');
        $name   = $request  -> get('name');
        
        //实例化模型
        $model =  new A;
        //查询 该名字 是否存在
        if(empty($id)){
            $result = $model->where('name', $name)->first();
        }else{
            $result = $model->where('name', $name)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }

    private function getSetWechat($use_alias,$app_id,$secret,$token,$encoding_key){
        $data   =    [
            'use_alias'    =>   $use_alias,
            'app_id'       =>   $app_id,
            'secret'       =>   $secret,
            'token'        =>   $token,
            'encoding_key' =>   $encoding_key,
            ];
        $str    =    view('osce::admin.sysmanage.wechat_config',$data)->render();
        $str    =   '<?php '.$str;
        try
        {
            if(!is_writable(WECHAT_CONFIG))
            {
                throw new \Exception('config/wechat.php文件不可写');
            }
            file_put_contents(WECHAT_CONFIG,$str);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    private function getSetMail($emailServer,$emailPort,$emailProtocol,$emailSsl,$emailUsername,$emailPassword) {
        //将协议选项上的true或者是false变成ssl或者是null
        if ($emailSsl == 'flase') {
            $emailSsl = 'NULL';
        } else {
            $emailSsl = "'ssl'";
        }
        $data   =    [
            'email_server'      =>   $emailServer,
            'email_port'        =>   $emailPort,
            'email_protocol'    =>   $emailProtocol,
            'email_ssl'         =>   $emailSsl,
            'email_username'    =>   $emailUsername,
            'email_password'    =>   $emailPassword
        ];

        $str = view('osce::admin.sysmanage.mail_config',$data)->render();
        $str = '<?php ' . $str;

        try
        {
            if(!is_writable(MAIL_CONFIG))
            {
                throw new \Exception('config/mail.php文件不可写');
            }
            file_put_contents(MAIL_CONFIG,$str);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }


    /**
     * 微信帮助设置
     * @method GET
     * @url /osce/admin/config/weChat-help
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * @return view
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWeChatHelp(Request $request){
        $current_url    =   $_SERVER['HTTP_HOST'];
        return view('osce::admin.sysmanage.system_help',['url'=>$current_url]);

    }
}