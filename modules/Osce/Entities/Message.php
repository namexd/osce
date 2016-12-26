<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/27 0027
 * Time: 14:41
 */

namespace Modules\Osce\Entities;

class Message extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'message';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['to', 'content', 'decode_type', 'access','template' ,'status'];


    /**
     * 发送未发送的信息（短信、邮件）
     * @return bool
     * @author fandian <fandian@sulida.com>
     * @date    2016-04-27
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function sendMessage()
    {
        //查询没有发送的消息（短信、邮件）
        $messages = $this->where('status', '=', 0)->get();
        $msgNum   = 0;     $emailNum = 0;      $wechatNum= 0;

        if(!$messages->isEmpty())
        {
            foreach($messages as $message)
            {
                //json解析
                $to      = json_decode($message->to);
                $content = json_decode($message->content);
                $access  = json_decode($message->access);
                $template = json_decode($message->template);


                //根据解码方式 处理信息('msg', 'email', 'wechat')
                switch($message->decode_type)
                {
                    case 'email'  : $this->sendEmail($to, $content, $access);   //2、发送邮件
                                    $emailNum++;
                                    break;
                    case 'wechat' : $this->sendWechat($to, $content, $access);  //3、发送微信
                                    $wechatNum++;
                                    break;
                    //case 'msg'    :                                             //1、发送短信（调用发送短信方法）
                    default       :
                                    //$content    .=  config('osce.sms_signature','【华西临床技能中心】');
                                    $app_key = config('message.messages.sms.app_key');
                                    $app_secret = config('message.messages.sms.app_secret');
                                    $request_uri = config('message.messages.sms.request_uri');
                                    $request_host = config('message.messages.sms.request_host');
                                    $request_method = 'GET';
                                    //签名名称
                                     $SingName = config('message.messages.sms.SingName');
                                    //模板CODE
                                    $TemplateCode1 = config('message.messages.sms.TemplateCode1');
                                    $TemplateCode2 = config('message.messages.sms.TemplateCode2');
                                    $TemplateCode3 = config('message.messages.sms.TemplateCode3');
                                    $TemplateCode4 = config('message.messages.sms.TemplateCode4');
                                    if($template->mb =="mb1"){
                                        $TemplateCode = $TemplateCode1;
                                        $name1 = $template->name1;
                                        $content = "{\"name\":\"$name1\"}";
                                    }
                                    if($template->mb =="mb2"){
                                        $TemplateCode = $TemplateCode2;
                                        $name1 = $template->name1;
                                        $content = "{\"name\":\"$name1\"}";
                                    }

                                    if($template->mb =="mb3"){
                                        $TemplateCode = $TemplateCode3;
                                        $name1 = $template->name1;
                                        $name2 = $template->name2;
                                        $content = "{\"name\":\"$name1\",\"val\":\"$name2\"}";
                                    }
                                    if($template->mb =="mb4"){
                                        $TemplateCode = $TemplateCode4;
                                        $name1 = $template->name1;
                                        $name2 = $template->name2;
                                        $name3 = $template->name3;
                                        $name4 = $template->name4;
                                        $content = "{\"name\":\"$name1\",\"val\":\"$name2\",\"val1\":\"$name3\",\"val2\":\"$name4\"}";
                                    }


                                   $request_paras = array(
                                        'ParamString' => $content,
                                        'RecNum' => $to,
                                        'SignName' =>$SingName,
                                        'TemplateCode' =>$TemplateCode
                                    );
                                    $info = "";

                                    //$this->sendShortMsg($to, $content);         //默认发送短信
                                    //$hello = array("app_key"=>$app_key,"app_secret"=>$app_secret,"request_paras"=>$request_paras);
                                    //return $hello;

                                    $this->do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, $info);

                                    $msgNum++;
                }

                //发送成功后，修改信息发送状态
                $message->status = 1;
                if(!$message->save()){
                    throw new \Exception('信息发送状态修改失败！');
                }
            }
        }

        $backMsg = ($msgNum?'短信：'.$msgNum.'条；':'').($emailNum?'邮件：'.$emailNum.'条；':'').($wechatNum?'微信：'.$wechatNum.'条；':'');
        //return ($backMsg == '')? '没有发送信息！' : '成功发送：'.$backMsg;
        ($backMsg == '')? \Log::alert('没有发送信息！'):  \Log::alert('成功发送：'.$backMsg);
    }

    /**
     * 发送短信
     * @param $mobile
     * @param $message
     * @author fandian <fandian@sulida.com>
     * @date    2016-04-27
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function sendShortMsg($mobile, $message)
    {
        $sender = \App::make('messages.sms');
        $sender ->send($mobile,$message);
    }
    /**
     * 发送短信（阿里云）
     * @param $mobile
     * @param $message
     * @author fandian <phpinfo@foxmail.com>
     * @date    2016-12-20
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, &$info) {
        ksort($request_paras);
        $request_header_accept = "application/json;charset=utf-8";
        $content_type = "";
        $headers = array(
        'X-Ca-Key' => $app_key,
        'Accept' => $request_header_accept
        );
        ksort($headers);
        $header_str = "";
        $header_ignore_list = array('X-CA-SIGNATURE', 'X-CA-SIGNATURE-HEADERS', 'ACCEPT', 'CONTENT-MD5', 'CONTENT-TYPE', 'DATE');
        $sig_header = array();
        foreach($headers as $k => $v) {
        if(in_array(strtoupper($k), $header_ignore_list)) {
        continue;
        }
        $header_str .= $k . ':' . $v . "\n";
        array_push($sig_header, $k);
        }
        $url_str = $request_uri;
        $para_array = array();
        foreach($request_paras as $k => $v) {
            array_push($para_array, $k .'='. $v);
        }
        if(!empty($para_array)) {
            $url_str .= '?' . join('&', $para_array);
        }
        $content_md5 = "";
        $date = "";
        $sign_str = "";
        $sign_str .= $request_method ."\n";
        $sign_str .= $request_header_accept."\n";
        $sign_str .= $content_md5."\n";
        $sign_str .= "\n";
        $sign_str .= $date."\n";
        $sign_str .= $header_str;
        $sign_str .= $url_str;

        $sign = base64_encode(hash_hmac('sha256', $sign_str, $app_secret, true));
        $headers['X-Ca-Signature'] = $sign;
        $headers['X-Ca-Signature-Headers'] = join(',', $sig_header);
        $request_header = array();
        foreach($headers as $k => $v) {
            array_push($request_header, $k .': ' . $v);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request_host . $url_str);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        //return $ret;
}
    /**
     * 发送邮件
     * @param $to
     * @param $content
     * @param $access
     * @author  fandian <fandian@sulida.com>
     * @date    2016-04-27
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function sendEmail($to, $content, $access)
    {

    }
    /**
     * 发送微信
     * @param   $to
     * @param   $content
     * @param   $access
     * @author  fandian <fandian@sulida.com>
     * @date    2016-04-27
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function sendWechat($to, $content, $access)
    {

    }
}