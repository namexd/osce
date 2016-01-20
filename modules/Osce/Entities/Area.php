<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;


class Area extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'area';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'cate'];

    /**
     * 摄像机和区域的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areaVcr()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Vcr','area_vcr','area_id','vcr_id');
    }


    /**
     * 查出对应的类型数据
     * @return mixed
     */
    public function showRoomCateList()
    {
        return $this->select('id','name','cate')->get();
    }


}