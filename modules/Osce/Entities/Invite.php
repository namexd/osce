<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/9 0009
 * Time: 15:19
 */

namespace Modules\Osce\Entities;
use App\Repositories\Common;
use DB;
use Modules\Osce\Http\Controllers\Wechat\InvitationController;

class Invite extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'invite';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','name', 'begin_dt', 'end_dt', 'exam_screening_id'];
    private $excludeId = [];

    //保存并发送邀请
    public function addInvite(array $data){
//        $connection     =   DB::connection($this->connection);
//        $connection     ->  beginTransaction();
        try{
            foreach($data as $k=>$v){}
            $inviteData=[
                'id'  =>$data[$k]['teacher_id'],
                'name'   =>$data[$k]['exam_name'],
                'begin_dt' =>$data[$k]['begin_dt'],
                'end_dt' =>$data[$k]['end_dt'],
                'exam_screening_id' =>$data[$k]['exam_id'],
            ];
//            dd($notice);
            if($notice  =   $this  -> firstOrcreate($inviteData))
            {
                $invitelist =$this->where('id','=',$data[$k]['teacher_id'])->first()->toArray();
                $list=[
                    'invite_id' =>  $invitelist['id'],
                    'exam_screening_id' =>$data[$k]['exam_id'],
                    'case_id'     =>$data[$k]['case_id'],
                    'teacher_id'     =>$data[$k]['teacher_id'],
                ];
                //关联到考试邀请sp老师表
                $examspModel =new ExamSpTeacher();
                $result =$examspModel -> addExamSp($list);
                //邀请用户
                $this   ->  sendMsg($notice,$data);
//                $connection ->commit();
                return $notice;
            }
            else
            {
                throw new \Exception('邀请失败');
            }
        }
        catch(\Exception $ex)
        {
//            $connection ->rollBack();
            throw $ex;
        }
    }

//       发送邀请

    public function sendMsg($notice,$data){
        try
        {
           foreach($data as $k=>$v){}
            $url    =   route('osce.wechat.invitation.getMsg',['id'=>$notice->id]);
            $msgData    =   [
                [
                    'title' =>'邀请通知',
                    'desc'  =>$data[$k]['exam_name'].'邀请',
                    'url'=>  $url
                ],
            ];
            $message    =   Common::CreateWeiXinMessage($msgData);
            Common::sendWeiXin($data[$k]['openid'],$message);//单发
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }


}