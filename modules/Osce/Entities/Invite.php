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
    protected $fillable = ['id', 'name', 'begin_dt', 'end_dt', 'exam_screening_id','station_id','status','user_id'];
    private $excludeId = [];

    //保存并发送邀请
    public function addInvite(array $data)
    {
        try {
            foreach ($data as  $list) {
                $inviteDat = [
                    'user_id'  =>$list['teacher_id'],
                    'name'  => $list['exam_name'],
                    'begin_dt' => $list['begin_dt'],
                    'end_dt' => $list['end_dt'],
                    'exam_screening_id' => $list['exam_screening_id'],
                    'station_id' =>$list['station_id'],
                    'status'=>0,
                ];
//                if($this->find($inviteDat['user_id']))
//                {
//                    throw new \Exception('同一个老师不能同时收到两个不同邀请');
//                }
                  $notice = $this->Create($inviteDat);
            }
                if ($notice) {
                    foreach($data as  $SpTeacher){
                        $ExamSpList = [
//                           'id'=>$data[$k]['teacher_id'],
                             'invite_id' => $notice->id,
                            'exam_screening_id' => $SpTeacher['exam_screening_id'],
                            'case_id' => $SpTeacher['case_id'],
                            'teacher_id' => $SpTeacher['teacher_id'],
                        ];
                        //关联到考试邀请sp老师表
                          $examspModel = new ExamSpTeacher();
                          $examspModel-> addExamSp($ExamSpList);
                    }
                    //邀请用户
                    $this->sendMsg($data,$notice);
//
                    return $notice;
                } else {
                    throw new \Exception('邀请保存失败');
                }

        } catch (\Exception $ex) {
            throw $ex;
        }

    }
        // 发送邀请

    public function sendMsg($data,$notice)
    {

        try {
            foreach ($data as $key => $openIdList) {
                $url = route('osce.wechat.invitation.getMsg',['id'=>$notice->id]);
                $msgData = [
                    [
                        'title' => '邀请通知',
                        'desc' => $openIdList['exam_name'] . '邀请',
                        'url' => $url,
                    ],
                ];

//                $message = Common::CreateWeiXinMessage($msgData);
                try
                {
                    $message = Common::CreateWeiXinMessage($msgData);
                    Common::sendWeiXin($openIdList['openid'], $message);//单发
                }catch (\Exception $ex_msg)
                {

                    throw new \Exception($openIdList['teacher_name'].'没有关联微信号');
                }

//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
//            oI7UquLMNUjVyUNaeMP0sRcF4VyU
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}