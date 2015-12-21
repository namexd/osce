<?php
/**
 * 资源-工具 资源
 * author Luohaihua
 * date 2015-11-24
 */
namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesToolsCate extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_tools_cate';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['repeat_max', 'pid', 'name', 'manager_id', 'manager_name', 'manager_mobile','location' ,'location_id', 'detail', 'loan_days'];
    public $search          =   ['manager_name','detail','name'];

    /**
     * 管理员
     */
    public function manager(){
        return $this->belongsTo('App\Entities\User','manager_id');
    }

    /**
     * 地址
     */
    public function address(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesLocation','location_id');
    }


}