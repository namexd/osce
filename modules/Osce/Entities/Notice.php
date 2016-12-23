<?php
/**
 * 通知模型
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/6
 * Time: 14:03
 */

namespace Modules\Osce\Entities;


use App\Repositories\Common;
use Modules\Osce\Entities\CommonModel;
use Modules\Osce\Entities\Teacher;
use DB;
use Auth;

class Notice extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'inform_info';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['name','content','create_user_id','exam_id','accept','attachments','status'];

    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam','id','exam_id');
    }
    public function addNotice(array $data,array $to,$accept){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                //通知用户
                $this   ->  sendMsg($notice,$to,$accept);
                $connection ->commit();
                return $notice;
            }
            else
            {
                throw new \Exception('创建通知失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection ->rollBack();
            throw $ex;
        }
    }

    public function editNotice($id,$name,$content,$attach,$groups){
        try{
            if($notice  =   $this   -> find($id))
            {
                $notice ->  name   =   $name;
                $notice ->  content =   $content;

                $notice ->  attachments  =   $attach;



                if(!$notice  ->save())
                {
                    throw new \Exception('修改通知失败');
                }
                //关联消息接收用户和消息
                //$to     =   $this   ->  getNoticeToOpendIds($notice,$groups);


                $to     =   $this   ->  getGroupsOpendIds($groups,$notice->exam_id);
                //通知用户
                $accept=  $notice ->  accept  =   implode(',',$groups);

                      $this   ->  sendMsg($notice,$to,$accept);

                return $notice;
            }
            else
            {
                throw new \Exception('创建通知失败');
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function makeNoticeUserRelative($notice,array $to){
        $data   =   [];
        foreach($to as $item)
        {
            $data[]   =   [
                'notice_id' =>  $notice ->  id,
                'uid'       =>  $item['id']
            ];
        }
        $noticeToModel  =   new NoticeTo();
        if($noticeToModel   -> addNoticeTo($data))
        {
            return true;
        }
        else
        {
            throw new \Exception('保存收件人失败');
        }
    }
    public function sendMsg($notice,$to,$accept){
        try
        {

            $url    = $this->makeUrl($notice);
            $sendType   =   Config::where('name','=','type')    ->  first();
            if(!$sendType){
                throw new \Exception('请到系统设置中设置发送消息的方式');
            }
            $values      =   json_decode($sendType->value);
            if(empty($values))
            {
                throw new \Exception('请到系统设置中设置发送消息的方式');
            }
            if(is_null($values))
            {
                $values  =   [1];
            }
            if(is_array($values))
            {
                foreach($values as $value)
                {
                    try
                    {
                        switch($value)
                        {
                            case 1:
                                $notice->accept =   $accept;
                                $notice->save();
                                $this->sendWechat($notice,array_pluck($to,'openid'),$url);
                                break;
                            case 2:
                                $this->sendSms($notice,array_pluck($to,'mobile'),$url);
                                break;
                            case 3:
                                $this->sendEmail($notice,array_pluck($to,'email'),$url);
                                break;
                            case 4:
                                $this->sendPm($notice,array_pluck($to,'id'),$url);
                                break;
                            default:
                                $this->sendWechat($notice,$to,$url);
                        }
                    }
                    catch(\Exception $ex)
                    {
                        \Log::alert('应该是邮件问题');
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    public function sendWechat($notice,$to,$url){
        $msgData    =   [
            [
                'title' =>  '资讯通知',
                'desc' =>  $notice->exam->name,
                'url'   =>  $url
            ]
        ];
        $message    =   Common::CreateWeiXinMessage($msgData);
        if(count($to)==1)
        {
            Common::sendWeiXin($to[0],$message);
        }
        else
        {
            Common::sendWeixinToMany($message,$to);
        }
    }

    public function sendPm($notice,$to,$url){
        $sender =   \App::make('messages.pm');
        foreach($to as $accept)
        {
            if(empty($accept))
            {
                continue;
            }


            $sender ->  send($accept,$url,$notice->name);
        }
    }
    /**
     * 发布通知
     * @access public
     *
     * @param $title        通知标题
     * @param $content      通知内容
     * @param $exam_id      通知所属考试ID
     * @param $groups       被通知的人群
     *
     * @return
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function sendNotice($title,$content,$exam_id,array $groups,$attach){
        $user   =   Auth::user();
        $data   =   [
            'name'           =>  $title,
            'content'        =>  $content,
            'exam_id'        =>  $exam_id,
            'status'         =>  1,
            'create_user_id' =>  $user->id,
            'attachments'    =>  $attach,
        ];
        try{
            $accept = implode(',',$groups);
            $to     =   $this   ->  getGroupsOpendIds($groups,$exam_id);
            $notice =   $this   ->  addNotice($data,$to,$accept);
            return $notice;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    public function makeUrl($notice){

     return   $url    =   route('osce.wechat.notice.getView',['id'=>$notice->id]);

    }



    public function getList(){
        return $this    ->  paginate(config('osce.page_size'));
    }

    /**
     * 根据群组列表获取opendid列表
     * @access public
     *
     * @param  array $groups 用户选择的接收群组
     *
     * @return array
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-06 19:25
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getGroupsOpendIds($groups,$exam_id){

        $data   =   [];
        if(in_array(1,$groups)){
            $student    =   $this   ->  getStudentsOpendIds($exam_id);
        }
        if(in_array(2,$groups)){
            $teachers   =   $this   ->  getExamTeachersOpendIds($exam_id);
        }
        if(in_array(3,$groups)){
            $spTeahcers =   $this   ->  getExamSpTeachersOpendIds($exam_id,$data);
        }
        if(!empty($student)){
            $data   =   array_merge($data,$student);
        }
        if(!empty($teachers)){
            $data   =   array_merge($data,$teachers);
        }
        if(!empty($spTeahcers)){
            $data   =   array_merge($data,$spTeahcers);
        }

        return $data;
    }


    private function getExamTeachersOpendIds($exam_id,array $data=[]){
        $ExamRoom   =   new ExamRoom();
        $list   =   $ExamRoom   ->  getRoomTeachersByExamId($exam_id);
        foreach($list as $teacher)
        {
            if(is_null($teacher->userInfo))
            {
                throw new \Exception('没有找到指定的教务人员用户信息');
            }
            $data[] =   [
                'id'    =>  $teacher->userInfo  ->  id,
                'openid'=>  $teacher->userInfo  ->  openid,
                'mobile'=>  $teacher->userInfo  ->  mobile,
                'email'=>  $teacher->userInfo   ->  email,
            ];
        }
        return $data;
    }

    /**
     * 根据考试ID获取学生openid列表
     * @access public
     *
     * @param int $exam_id 考试id
     * @param array $data
     * @return array
     * @throws \Exception
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2015-12-29 17:09
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    private function getStudentsOpendIds($exam_id,array $data=[]){
        $list   =   Student::where('exam_id','=',$exam_id)->get();
        foreach($list as $student)
        {
            if(is_null($student->userInfo))
            {
                throw new \Exception('没有找到指定的考生用户信息');
            }

            $data[] =   [
                'id'    =>  $student->userInfo->id,
                'openid'=>  $student->userInfo->openid,
                'email' =>  $student->userInfo->email,
                'mobile' =>  $student->userInfo->mobile,
            ];
        }
        return $data;
    }
    private function getExamSpTeachersOpendIds($exam_id,array $data=[]){
        $ExamRoom   =   new ExamRoom();
        $list   =   $ExamRoom   ->  getRoomSpTeachersByExamId($exam_id);
        foreach($list as $teacher)
        {
            if(is_null($teacher->userInfo))
            {
                throw new \Exception('没有找到指定的教务人员用户信息');
            }

            $data[] =   [
                'id'    =>  $teacher->userInfo->id,
                'openid'=>  $teacher->userInfo->openid,
                'email' =>  $teacher->userInfo->email,
                'mobile'=>  $teacher->userInfo->mobile,
            ];

        }
        return $data;
    }


    public function sendEmail($notice,$to,$url){
        try {
            $sender =   \App::make('messages.email');
            $content=   [];
            $content[]  =   '亲爱的osce考试系统用户:';
            $content[]  =   $notice->exam->name. ' ' .$notice->title;
            $content[]  =   '详情查看'.$url;
            $sender ->  send($to,implode('',$content));
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
        }
    }



    public function sendSms($notice,$to,$url){
/*        $sender =   \App::make('messages.sms');
        $content=   [];
        $content[]  =   $notice->exam->name. ' ' .$notice->title;
        $content[]  =   '详情查看'.$url;*/
        $TemplateCode5 = config('message.messages.sms.TemplateCode5');
        $app_key = config('message.messages.sms.app_key');
        $app_secret = config('message.messages.sms.app_secret');
        $request_uri = config('message.messages.sms.request_uri');
        $request_host = config('message.messages.sms.request_host');
        $request_method = 'GET';
        //签名名称
        $SingName = config('message.messages.sms.SingName');
        $url = preg_match('/id.(\d*)/',$url,$a)?$a[1]:0;
        $content = "{\"name\":\"$notice->exam->name\",\"val\":\"$url\"}";

        $info = "";

        foreach($to as $mobile)
        {
            //$sender ->  send($mobile,implode('',$content).' 【敏行医学】');

            $request_paras = array(
                'ParamString' => $content,
                'RecNum' => $mobile,
                'SignName' =>$SingName,
                'TemplateCode' =>$TemplateCode5
            );

            $this->do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, $info);
        }
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
        return $ret;
    }
}