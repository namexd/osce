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

        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{

            $inviteData=[
                'name'   =>$data['exam_name'],
                'begin_dt' =>$data['begin_dt'],
                'end_dt' =>$data['end_dt'],
                'exam_screening_id' =>$data['exam_id'],

            ];


            if($notice  =   $this  -> create($inviteData))
            {
//                dd( $notice->id);
                $list=[
                    'invite_id' =>  $notice->id,
//                    'exam_screening_id' =>$data['exam_id'],
//                    'case_id'     =>$data['case_id'],
                    'teacher_id'     =>$data['teacher_id'],
                ];

//                dd($notice,$data,$list);
                //关联到考试邀请sp老师表
                $examspModel =new ExamSpTeacher();
                $result =$examspModel -> addExamSp($list);
                dd($notice,$data,$list,$result);

                //邀请用户
                $this   ->  sendMsg($notice,$data);
                $connection ->commit();
                return $notice;
            }
            else
            {
                throw new \Exception('创建邀请失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection ->rollBack();
            throw $ex;
        }
    }



//       发送邀请

    public function sendMsg($notice,$data){
        try
        {


//            $url    =   route('osce.admin.notice.getMsg',['id'=>$notice->id]);
            $msgData    =   [
                [
                    'title' =>'邀请通知',
                    'desc'  =>'osce考试第一期邀请',
                    'url'=>'http://www.baidu.com'
                ],
            ];
            $message    =   Common::CreateWeiXinMessage($msgData);
            Common::sendWeiXin('oI7UquLMNUjVyUNaeMP0sRcF4VyU',$message);//单发
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$data);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }


}