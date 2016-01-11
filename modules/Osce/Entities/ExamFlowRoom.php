<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:24
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;

class ExamFlowRoom extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_flow_room';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=   ['serialnumber','room_id','flow_id','created_user_id'];

    /*
     * 所属房间
     */
    public function room(){
        return $this->hasOne('\Modules\Osce\Entities\Room','id','room_id');
    }
}