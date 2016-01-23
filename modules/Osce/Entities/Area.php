<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;


use Illuminate\Support\Facades\DB;

class Area extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'area';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'cate', 'description'];

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

    /**
     * 删除考试区域
     * @return mixed
     */
    public function deleteArea($id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try{
            $area = $this->where('id',$id)->first();
            if($area->cate ==1 && Room::first()){
                throw new \Exception('该考试区域已关联，无法删除！');
            }
            if($result = AreaVcr::where('area_id',$id)->first()){
                throw new \Exception('该考试区域已与摄像机关联，无法删除！');
            }
            //删除考试区域
            if(!$result = $this->where('id',$id)->delete()){
                throw new \Exception('删除失败，请重试！');
            }
            $connection->commit();
            return true;

        } catch(\Exception $ex){
            $connection->rollback();
            throw $ex;
        }
    }

}