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
    protected $fillable = ['id', 'name', 'begin_dt', 'end_dt', 'exam_screening_id'];
    private $excludeId = [];

    //保存并发送邀请
    public function addInvite(array $data)
    {
//        $connection     =   DB::connection($this->connection);
//        $connection     ->  beginTransaction();
        try {

            foreach ($data as  $list) {
                $inviteDat = [
                    'id'  =>$list['teacher_id'],
                    'name'  => $list['exam_name'],
                    'begin_dt' => $list['begin_dt'],
                    'end_dt' => $list['end_dt'],
                    'exam_screening_id' => $list['exam_screening_id'],
                ];
            }
            if ($notice = $this->firstOrCreate($inviteDat)) {
                dd(11111);

//                $invitelist = $this->where('id', '=', $data[$k]['teacher_id'])->first()->toArray();


               foreach($data as  $list){
                    $ExamSpList = [
//                           'id'=>$data[$k]['teacher_id'],
//                             'invite_id' => $invitelist['id'],
                             'exam_screening_id' => $list['exam_id'],
                             'case_id' => $list['case_id'],
                             'teacher_id' => $list['teacher_id'],
                         ];
                     }

                //关联到考试邀请sp老师表
                $examspModel = new ExamSpTeacher();
                $result = $examspModel->addExamSp($ExamSpList);
                //邀请用户
                $this->sendMsg($notice, $data);
//                $connection ->commit();
                return $notice;
            } else {
                throw new \Exception('邀请失败');
            }
        } catch (\Exception $ex) {
//            $connection ->rollBack();
            throw $ex;
        }
    }
        // 发送邀请

    public function sendMsg($notice, $data)
    {
        try {
            foreach ($data as $k => $v) {
            }
            $url = route('osce.wechat.invitation.getMsg', ['id' => $notice->id]);
            $msgData = [
                [
                    'title' => '邀请通知',
                    'desc' => $data[$k]['exam_name'] . '邀请',
                    'url' => $url,
                ],
            ];
            $message = Common::CreateWeiXinMessage($msgData);
            Common::sendWeiXin($data[$k]['openid'], $message);//单发
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
//            oI7UquLMNUjVyUNaeMP0sRcF4VyU

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}