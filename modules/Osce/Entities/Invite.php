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






        //���沢��������
    public function addInvite(array $data){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                //������Ϣ�����û�����Ϣ
//                $this   ->  makeNoticeUserRelative($notice);
                //�����û�
                $this   ->  sendMsg($notice,array_pluck($data,'openid'));
                $connection ->commit();
                return $notice;
            }
            else
            {
                throw new \Exception('����֪ͨʧ��');
            }
        }
        catch(\Exception $ex)
        {
            $connection ->rollBack();
            throw $ex;
        }
    }



//       ��������

    public function sendMsg($notice,$to){
        try
        {


//            $url    =   route('osce.admin.notice.getMsg',['id'=>$notice->id]);
            $msgData    =   [
                [
                     'title' =>'����֪ͨ',
                     'desc'  =>'osce���Ե�һ������',
                     'url'=>'http://www.baidu.com'
                ],
            ];
            Common::sendWeiXin('oI7UquLMNUjVyUNaeMP0sRcF4VyU',$msgData);//����
//            $message    =   Common::CreateWeiXinMessage($msgData);
//            Common::sendWeixinToMany($message,$to);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }


}