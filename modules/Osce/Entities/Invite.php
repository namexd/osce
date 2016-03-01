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
    protected $fillable = ['id', 'name', 'begin_dt', 'end_dt', 'exam_screening_id', 'station_id', 'status', 'user_id'];


    public function examSpTeacher(){
        return $this->hasOne('\Modules\Osce\Entities\ExamSpTeacher','invite_id','id');
    }

    //保存并发送邀请
    public function addInvite(array $data)
    {
        //开启事务
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            foreach ($data as &$list) {
                //查询出数据库是否有该老师在这场考试邀请过
                $examScreening = Invite::where('exam_screening_id', '=', $list['exam_screening_id'])
                    ->where('user_id','=',$list['teacher_id'])
                    ->where('station_id','=',$list['station_id'])
                    ->whereIn('status',[0, 1])
                    ->first();
                //查询出老师名字
//                $teacherName    = Teacher::where('id','=',$inviteDat['user_id'])->select('name')->first();
                $teacherName = Teacher::find($list['teacher_id']);
                if ($examScreening) {
                    throw new \Exception('在该场考试中已经邀请过' . $teacherName->name . '老师了！！！');
                }
                $inviteDat = [
                    'user_id' => $list['teacher_id'],
                    'name' => $list['exam_name'],
                    'begin_dt' => $list['begin_dt'],
                    'end_dt' => $list['end_dt'],
                    'exam_screening_id' => $list['exam_screening_id'],
                    'station_id' => $list['station_id'],
                    'status' => 0,
                ];
                $notice = $this->Create($inviteDat);
                if ($notice) {
                    $list['id']=$notice->id;
                    $ExamSpList = [
//                           'id'=>$data[$k]['teacher_id'],
                        'invite_id' => $notice->id,
                        'exam_screening_id' => $list['exam_screening_id'],
                        'case_id' => $list['case_id'],
                        'teacher_id' => $list['teacher_id'],
                    ];
                    //关联到考试邀请sp老师表
                    $examspModel = new ExamSpTeacher();
                    $examspModel->addExamSp($ExamSpList);
                } else {
                    throw new \Exception('邀请保存失败');
                }
            }
            //$connection->commit();

            $this->sendMsg($data);

            return $notice;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }

    // 发送邀请

    public function sendMsg($data)
    {
        $openIdList =   [];
        try {
            foreach ($data as $key => $userInfo) {
                $url = route('osce.wechat.invitation.getMsg', ['id' => $userInfo['id']]);
                $msgData = [
                    [
                        'title' => '邀请通知',
                        'desc' => '邀请您参加'.$userInfo['exam_name'] . '考试',
                        'url' => $url,
                    ],
                ];
                $openIdList[]   =   $userInfo['openid'];
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
//            oI7UquLMNUjVyUNaeMP0sRcF4VyU
            }
            try {
                $message = Common::CreateWeiXinMessage($msgData);
                if(count($openIdList)<2)
                {
                    $openId   =   $openIdList[0];
                    Common::sendWeiXin($openId,$message);//单发
                }
                else
                {
                    Common::sendWeixinToMany($msgData,$openIdList);
                }

            } catch (\Exception $ex_msg) {
                throw new \Exception('温馨提示'.$openIdList['teacher_name'] . '目前还没有登录过微信号');
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //邀请状态
    static public function status($examId)
    {
        $examScreeningIds = ExamScreening::where('exam_id', $examId)->select('id')->get()->pluck('id');

        return Invite::whereIn('invite.exam_screening_id', $examScreeningIds)
          ->select(
            'status as invite_status',
            'user_id as invite_user_id'
          )
         ->get();
    }

}