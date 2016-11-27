<?php
/**
 * 监考老师模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@163.com>
 * Date: 2015/12/29
 * Time: 10:46
 */

namespace Modules\Osce\Entities;


use App\Entities\User;

class Invigilator extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'invigilator';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['name', 'is_sp','moblie','case_id'];

    protected $is_spValues  =   [
        '1' =>  '是',
        '2' =>  '不是',
    ];

    public function userInfo(){
        return $this    ->  hasOne('\App\Entities\User','mobile','mobile');
    }
    /**
     * 获取是否为SP老师的值
     * @access public
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@163.com>
     * @date 2015-12-29 16:56
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     *
     */
    public function getIsSpValues(){
        return $this    ->  is_spValues;
    }

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
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@163.com>
     * @date 2015-12-29 10:52
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilatorList(){
        return  $this   ->  where('is_sp','=',1)
                         ->  paginate(config('osce.page_size'));
    }

    /**
     * 获取非SP监考老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@163.com>
     * @date 2015-12-29 16:58
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     *
     */
    public function getInvigilatorList(){
        return  $this   ->  where('is_sp','=',2)
            ->  paginate(config('osce.page_size'));
    }
}