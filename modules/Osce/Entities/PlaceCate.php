<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;

use DB;

class PlaceCate extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'place_cate';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'pid', 'cid'];
    public $search = [];

    /**
     * 与场所的关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function place()
    {
        return $this->hasMany('\Modules\Osce\Entities\Place', 'pid', 'cid');
    }

    /**
     * 获取场所类别列表
     * @param $formData
     * @return mixed
     */
    public function showPlaceCateList($formData)
    {
        //默认查询status不为0（已删除）的场所类别
        $builder = $this->where($this->table . '.status', '<>', 0);

        //如果传入了ID，那么就依据ID进行查找
        if ($formData['id'] !== null) {
            $builder = $builder->where($this->table . '.id', $formData['id']);
        }

        //根据pid进行查询，因为暂时只考虑一层，所以暂时注释掉
//        $builder = $builder->where($this->table . 'pid', '=', $pid);

        //如果order不为空的话，就使用order的数据，否则就指定，暂时不考虑排序
//        $orderName = empty($formData['order_name']) ? 1 : $formData['order_name'];
//        $orderBy = empty($formData['order_by']) ? 'desc' : $formData['order_by'];
//        $paramArray = ['created_at'];
//        $builder = $this->order($builder, $orderName, $orderBy, $paramArray);

        //如果keyword不为空，那么就进行模糊查询
        if ($formData['keyword'] !== null) {
            $builder = $builder->where($this->table . '.created_at', '=', '%' . $formData['keyword'] . '%');
        }

        //选择查询的字段
        $builder = $builder->select([
            'id',
            'name',
            'status'
        ]);

        return $builder->paginate(config('osce.page_size'));
    }

    /**
     * 插入数据
     * @param $formData
     */
    public function insertData($formData)
    {
        DB::transaction(function () use ($formData) {
            $this->insert($formData);
            return true;
        });
    }

    /**
     * 修改数据
     * @param $id
     * @param $formData
     */
    public function updateData($id, $formData)
    {
        DB::transaction(function () use ($id, $formData) {
            $this->where($this->table .'.id', $id)->update($formData);
            return true;
        });
    }

    /**
     * 删除数据
     * @param $id
     */
    public function deleteData($id)
    {
        DB::transaction(function () use ($id) {
            $this->where($this->table.'.id',$id)->delete();
            return true;
        });
    }
}