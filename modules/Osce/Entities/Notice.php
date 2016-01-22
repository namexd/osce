<?php
/**
 * 通知模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
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
    public function addNotice(array $data,array $to){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                //关联消息接收用户和消息
                //$this   ->  makeNoticeUserRelative($notice,$to);
                //通知用户
                $this   ->  sendMsg($notice,array_pluck($to,'opendid'));
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
                $notice ->  accept  =   implode(',',$groups);
                if(!$notice  ->save())
                {
                    throw new \Exception('修改通知失败');
                }
                //关联消息接收用户和消息
                //$to     =   $this   ->  getNoticeToOpendIds($notice,$groups);


                $to     =   $this   ->  getGroupsOpendIds($groups,$notice->exam_id);
                //通知用户
                $this   ->  sendMsg($notice,array_pluck($to,'opendid'));
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
    public function sendMsg($notice,$to){
        try
        {
            $url    =   route('osce.admin.notice.getMsg',['id'=>$notice->id]);
            $sendType   =   Config::where('name','=','type')    ->  first();

            $values      =   json_decode($sendType->value);

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
                                $this->sendWechat($notice,$to,$url);
                                break;
                            case 2:
                                $this->sendEmail($notice,$to,$url);
                                break;
                            case 3:
                                $this->sendSms($notice,$to,$url);
                                break;
                            default:
                                $this->sendWechat($notice,$to,$url);
                        }
                    }
                    catch(\Exception $ex)
                    {
                        \Log::alert('通知发送失败');
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
                'title' =>  $notice->exam->name.'通知',
            ],
            [
                'title' =>  $notice->title,
                'url'   =>  $url
            ]
        ];
        $message    =   Common::CreateWeiXinMessage($msgData);
        Common::sendWeixinToMany($message,$to);
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function sendNotice($title,$content,$exam_id,array $groups,$attach){
        $user   =   Auth::user();
        $data   =   [
            'name'      =>  $title,
            'content'   =>  $content,
            'exam_id'   =>  $exam_id,
            'accept'    =>  implode(',',$groups),
            'status'    =>  1,
            'create_user_id'    =>  $user->id,
            'attachments'    =>  $attach,
        ];
        $groups=    [
            1
        ];
        try{
            $to     =   $this   ->  getGroupsOpendIds($groups,$exam_id);
            $notice =   $this   ->  addNotice($data,$to);
            return $notice;
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-06 19:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getGroupsOpendIds($groups,$exam_id){
        $data   =   [];
        if(in_array(1,$groups))
        {
            //$data   =   $this   ->  getStudentsOpendIds($exam_id,$data);
            $data   =   array_merge($data,$this   ->  getStudentsOpendIds($exam_id,$data));
        }
        if(in_array(2,$groups))
        {
            //$data   =   $this   ->  getExamTeachersOpendIds($exam_id,$data);
            $data   =   array_merge($data,$this   ->  getExamTeachersOpendIds($exam_id,$data));
        }
        if(in_array(3,$groups))
        {
            //$data   =   $this   ->  getExamSpTeachersOpendIds($exam_id,$data);
            $data   =   array_merge($data,$this   ->  getExamSpTeachersOpendIds($exam_id,$data));
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
            if($teacher->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $teacher->userInfo  ->  id,
                    'openid'=>  $teacher->userInfo  ->  openid,
                    'mobile'=>  $teacher->userInfo  ->  mobile,
                    'email'=>  $teacher->userInfo   ->  email,
                ];
            }
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private function getStudentsOpendIds($exam_id,array $data=[]){
        $list   =   Student::where('exam_id','=',$exam_id)->get();
        foreach($list as $student)
        {
            if(is_null($student->userInfo))
            {
                throw new \Exception('没有找到指定的考生用户信息');
            }
            if($student->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $student->userInfo->id,
                    'openid'=>  $student->userInfo->openid,
                ];
            }
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
            if($teacher->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $teacher->userInfo->id,
                    'openid'=>  $teacher->userInfo->openid,
                ];
            }
        }
        return $data;
    }


    public function sendEmail($notice,$to,$url){
        $sender =   \App::make('messages.email');
        $content=   [];
        $content[]  =   '亲爱的osce考试系统用户:\n';
        $content[]  =   $notice->exam->name. ' ' .$notice->title.'<br/>';
        $content[]  =   '<a href="'.$url.'">查看详情</a>\n';
        $sender ->  send(array_pluck($to,'email'),implode('',$content));
    }

    public function sendSms($notice,$to,$url){
        $sender =   \App::make('messages.sms');
        $content=   [];
        $content[]  =   $notice->exam->name. ' ' .$notice->title;
        $content[]  =   '详情查看'.$url;
        foreach(array_pluck($to,'mobile') as $mobile)
        {
            $sender ->  send($mobile,implode('',$content).' 【敏行医学】');
        }
    }
}