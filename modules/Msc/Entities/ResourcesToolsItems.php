<?php
/**
 * 资源_工具单品记录
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/24
 * Time: 13:32
 */

namespace Modules\Msc\Entities;
use Modules\Msc\Entities\CommonModel;

class ResourcesToolsItems extends CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_tools_items';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['resources_tool_id', 'code', 'status', 'reject_detail', 'reject_date'];
    public $search          =   ['code'];

    /**
     * 所属设备id
     */
    public function resourcesTools ()
    {
        return $this->belongsTo('Modules\Msc\Entities\ResourcesTools','resources_tool_id','id');
    }
}