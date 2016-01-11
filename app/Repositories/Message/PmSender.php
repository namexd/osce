<?php

namespace  App\Repositories\Message;

use App\Repositories\Message\Contracts\Message;
use App\Entities\UsersPm;

class PmSender implements Message{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0){

        if(intval($accept)<1){
            return false;
        }

        if($title==null){
            $title=$content;
        }

        if(is_array($accept)){
            foreach($accept as $item){
                UsersPm::create([
                    'title'             =>  $title,
                    'content'           =>  $content,
                    'accept_user_id'    =>  $item,
                    'send_user_id'      =>  $sender,
                    'pid'               =>  $pid,
                    'module'            =>  $module
                ]);
            }
        }
        else{
            UsersPm::create([
                'title'             =>  $title,
                'content'           =>  $content,
                'accept_user_id'    =>  $accept,
                'send_user_id'      =>  $sender,
                'pid'               =>  $pid,
                'module'            =>  $module
            ]);
        }
    }

    public function get($id){

        return UsersPm::findOrNew($id);

    }

    public function messages($accept,$sender=null,$module=null,$status=1,$pageSize=10,$pageIndex=0){

        return (new UsersPm())->getList($accept,$sender,$module,$status,$pageSize,$pageIndex);

    }


    public function delete($id){

        return UsersPm::destroy($id);

    }

}