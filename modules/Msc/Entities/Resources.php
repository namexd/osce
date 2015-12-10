<?php

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class Resources extends  CommonModel {

    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['type', 'item_id', 'description'];
    public $search          =   ['description'];

    public function categroy()
    {
        return $this->belongsTo('Modules\Msc\Entities\ResourcesCate', 'cate_id');
    }

    public function tools(){
        return $this->hasOne('\Modules\Msc\Entities\ResourcesTools','id','item_id');
    }
    public function classroom(){
        return $this->hasOne('\Modules\Msc\Entities\ResourcesClassroom','id','item_id');
    }
    public function images(){
        return $this->hasMany('\Modules\Msc\Entities\ResourcesImage','resources_id','id');
    }
    /**
     * �����豸
     * @access public
     * @return array
     *
     * @version 1.0
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 14:28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function resourcesTools ()
    {
        return $this->hasMany('Modules\Msc\Entities\ResourcesTools', 'item_id');
    }

}