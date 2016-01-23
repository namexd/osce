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
                $notice = $this->firstOrCreate($inviteDat);
            }
                if ($notice) {
                    foreach($data as  $SpTeacher){
                        $ExamSpList = [
//                           'id'=>$data[$k]['teacher_id'],
                          'invite_id' => $SpTeacher['teacher_id'],
                            'exam_screening_id' => $SpTeacher['exam_id'],
                            'case_id' => $SpTeacher['case_id'],
                            'teacher_id' => $SpTeacher['teacher_id'],
                        ];
                        //关联到考试邀请sp老师表
                        $examspModel = new ExamSpTeacher();
                        $result = $examspModel->addExamSp($ExamSpList);
                    }
                    //邀请用户
                    $this->sendMsg($notice, $data);
//                $connection ->commit();
                } else {
                    throw new \Exception('邀请保存失败');
                }
               return $notice;

        } catch (\Exception $ex) {
//            $connection ->rollBack();
            throw $ex;
        }
    }
        // 发送邀请

    public function sendMsg($notice, $data)
    {

          dd($notice);
        try {
            foreach ($data as $key => $openIdList) {
            }
            $url = route('osce.wechat.invitation.getMsg', ['id' => $notice->id]);
            $msgData = [
                [
                    'title' => '邀请通知',
                    'desc' => $data[$key]['exam_name'] . '邀请',
                    'url' => $url,
                ],
            ];
            $message = Common::CreateWeiXinMessage($msgData);
            Common::sendWeiXin($data[$key]['openid'], $message);//单发
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
//            oI7UquLMNUjVyUNaeMP0sRcF4VyU

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}