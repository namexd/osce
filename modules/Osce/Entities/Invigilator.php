<?php
/**
 * 监考老师模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/29
 * Time: 10:46
 */

namespace Modules\Osce\Entities;


class Invigilator extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'invigilator';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['name', 'is_sp'];

    /**
     * 获取sp老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagenetion
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 10:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilator(){
        
    }
}