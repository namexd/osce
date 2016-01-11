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

class Notice extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'notice';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['title','content','create_user_id','exam_id'];

    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam','id','exam_id');
    }
    public function receivers(){
        return $this->hasManyThrough('App\Entities\User','Modules\Osce\Entities\NoticeTo','id','notice_id','id');
    }
    public function addNotice(array $data,array $to){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                //关联消息接收用户和消息
                $this   ->  makeNoticeUserRelative($notice,$to);
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

    public function editNotice($id,$title,$content){
        try{
            if($notice  =   $this   -> find($id))
            {
                $notice ->  title   =   $title;
                $notice ->  content =   $content;
                if(!$notice  ->save())
                {
                    throw new \Exception('修改通知失败');
                }
                //关联消息接收用户和消息
                $to     =   $this   ->  getNoticeToOpendIds($notice);
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
            $msgData    =   [
                [
                    'title' =>$notice->exam->name.'通知',
                ],
                [
                    'title' =>  $notice->title,
                    'url'   =>  $url
                ]
            ];
            $message    =   Common::CreateWeiXinMessage($msgData);
            Common::sendWeixinToMany($message,$to);
        }
        catch(\Exception $ex)
        {
            throw $ex;
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function sendNotice($title,$content,$exam_id,array $groups){
        $data   =   [
            'title'     =>  $title,
            'content'   =>  $content,
            'exam_id'   =>  $exam_id,
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
            $data   =   $this   ->  getStudentsOpendIds($exam_id,$data);
        }
        if(in_array(2,$groups))
        {
            $data   =   $this   ->  getExamTeachersOpendIds($exam_id,$data);
        }
        if(in_array(3,$groups))
        {
            $data   =   $this   ->  getExamSpTeachersOpendIds($exam_id,$data);
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
                    'id'    =>  $teacher->userInfo->id,
                    'openid'=>  $teacher->userInfo->openid,
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
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function getStudentsOpendIds($exam_id,array $data=[]){
        $list   =   Teacher::where('exam_id','=',$exam_id);
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

    public function getNoticeToOpendIds($notice){
        return  $notice->receivers;
    }
}