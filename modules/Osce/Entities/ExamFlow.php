<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:15
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;

class ExamFlow extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_flow';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','flow_id','created_user_id'];

    /**
     * 考试-流程节点-房间的关系
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function examFlowRoomRelation(){
        return $this->hasOne('\Modules\Osce\Entities\ExamFlowRoom','flow_id','flow_id');
    }
    public function flow(){
        return $this->hasOne('\Modules\Osce\Entities\Flows','id','flow_id');
    }
}