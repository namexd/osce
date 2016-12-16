<?php
/**
 * Created by PhpStorm.
 * User: fandian
 * Date: 2016/1/9 0009
 * Time: 15:59
 */
namespace Modules\Osce\Entities;
use Modules\Msc\Entities\CommonModel;

class Reply extends  CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'reply';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[];
    public      $search    =   [];

    public function getReply(){
        return $this->hasMany('Modules\Msc\Entities\Users','reply_id','id');
    }

    public function getReplyList($id)
    {
      //ͨ�������ȡ�ظ�����
        $list=Reply::where()->select();
      //�������ݲ���ҳ
        $replys=[];
        foreach($list as $item){
          $replys[]=[
              'id'          =>$item->id,
              'name'        =>$item->getReply,
              'context'     =>$item->context,
              'create_at'   =>$item->create_at,
          ];
        }
       return $replys-> paginate(config('osce.page_size'));
    }

}