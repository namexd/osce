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
     *
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

        return  [
            ['id'=>79,'opendid'=>'oI7UquKmahFwGV0l2nyu_f51nDJ4'],
            ['id'=>81,'opendid'=>'oI7UquPKycumti7NU4HQYjVnRjPo'],
        ];
    }
    private function getExamTeachers($exam_id){
        $list   =   Invigilator::where('type','=',1)->get();
        //return array_pluck($list,'user');
    }
    private function getExamStudent($exam_id){

    }
    private function getSpTeachers($exam_id){

    }

    public function getNoticeToOpendIds($notice){
        return  $notice->receivers;
    }
}