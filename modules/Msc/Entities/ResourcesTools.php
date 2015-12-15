<?php
/**
 * 资源-工具 资源
 * author Luohaihua
 * date 2015-11-24
 */
namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesTools extends CommonModel {
    protected $connection = 'msc_mis';
    protected $table = 'resources_tools';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['repeat_max', 'name', 'cate_id', 'manager_id', 'manager_name', 'manager_mobile', 'location', 'detail', 'loan_days', 'loaned', 'total', 'status'];
    public $search = ['manager_name', 'detail', 'name'];

    /**
     * 分类
     */
    public function categroy () {
        return $this->belongsTo ('Modules\Msc\Entities\ResourcesToolsCate', 'cate_id');
    }

    /**
     * 管理员
     */
    public function manager () {
        return $this->belongsTo ('App\Entities\User', 'manager_id');
    }

    /**
     * 包含设备
     */
    public function items () {
        return $this->hasMany ('Modules\Msc\Entities\ResourcesToolsItems', 'resources_tool_id');
    }

    /**
     * 所属资源
     */
    public function resources () {
        return $this->belongsTo ('Modules\Msc\Entities\Resources', 'id', 'item_id')->where ('type', '=', 'TOOLS');

    }

    /**
     * 获取管理人员姓名

     */
    public function getManagerName () {
        $thisManagerOb = $this->manager;
        if (!is_null ($thisManagerOb)) {
            $ObManager = $thisManagerOb->manager;
        }
        $thisManagName = $this->manager_name;
        $categroy = $this->categroy;
        if (!is_null ($categroy)) {
            $cateManager = $categroy->manager;
        }
        if ($thisManagName) {
            return $thisManagName;
        }
        if (!is_null ($ObManager) && $ObManager->name) {
            return $ObManager->name;
        }
        if (!is_null ($cateManager) && $cateManager->name) {
            return $cateManager->name;
        }
        return '-';
    }

    /**
     * 获取管理人员联系电话

     */
    public function getManagerMoblie () {
        $thisManagerOb = $this->manager;
        if (!is_null ($thisManagerOb)) {
            $ObManager = $thisManagerOb->manager;
        }
        $thisManagName = $this->manager_name;
        $categroy = $this->categroy;
        if (!is_null ($categroy)) {
            $cateManager = $categroy->manager;
        }
        if ($thisManagName) {
            return $thisManagName;
        }
        if (!is_null ($ObManager) && $ObManager->manager_mobile) {
            return $ObManager->manager_mobile;
        }
        if (!is_null ($cateManager) && $cateManager->manager_mobile) {
            return $cateManager->manager_mobile;
        }
        return '-';
    }

    //设置获取器
    public function getStatusAttribute ($value) {
        switch ($value) {
            //0=待审核 1=已通过 2=不通过
            case 0:
                $name = '不允许借出';
                break;
            case 1:
                $name = '正常';
                break;
            case 2:
                $name = '可借出';
                break;
            default:
                $name = '-';
        }
        return $name;
    }
    public function addTools(){

    }
}