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
            if($notice  =   $this   -> create($data))
            {
                //关联消息接收用户和消息
//                $this   ->  makeNoticeUserRelative($notice);
                //邀请用户
                $this   ->  sendMsg($notice,array_pluck($data,'openid'));
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



//       发送邀请

    public function sendMsg($notice,$to){
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
            Common::sendWeiXin('oI7UquLMNUjVyUNaeMP0sRcF4VyU',$msgData);//单发
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$to);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }


}