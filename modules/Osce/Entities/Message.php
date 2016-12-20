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
    protected $fillable     = ['to', 'content', 'decode_type', 'access', 'status'];


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

                //根据解码方式 处理信息('msg', 'email', 'wechat')
                switch($message->decode_type)
                {
                    case 'email'  : $this->sendEmail($to, $content, $access);   //2、发送邮件
                                    $emailNum++;
                                    break;
                    case 'wechat' : $this->sendWechat($to, $content, $access);  //3、发送微信
                                    $wechatNum++;
                                    break;
                    case 'msg'    :                                             //1、发送短信（调用发送短信方法）
                    default       :
                                    $content    .=  config('osce.sms_signature','【华西临床技能中心】');
                                    $this->sendShortMsg($to, $content);         //默认发送短信
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
        return ($backMsg == '')? '没有发送信息！' : '成功发送：'.$backMsg;
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