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
    protected $fillable 	=	['title','content','create_user_id'];

    public function addNotice(array $data,array $to){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                $this   ->  makeNoticeUserRelative($notice,$to);
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
    public function makeNoticeUserRelative($notice,array $to){
        $data   =   [];
        foreach($to as $item)
        {
            $data[]   =   [
                'notice_id' =>  $notice ->  id,
                'uid'       =>  $item['id']
            ];
        }
        if($this   -> insert($data))
        {
            return true;
        }
        else
        {
            throw new \Exception('保存收件人失败');
        }
    }
    public function sendMsg($notice,$to){
        Common::sendWeiXin();
    }

    public function getList(){
        return $this    ->  paginate(config('osce.page_size'));
    }
}