<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:47
 */

namespace Modules\Osce\Entities;


class CaseHistory extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'case_history';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'detail','status'];
    public $search = [];

    /**
     * 获取列表
     * $formData内有
     * @param $formData
     * @return mixed
     */
    public function getList($formData)
    {
        //默认查询status不为0（已删除）的场所
        $builder = $this->where($this->table . '.status', '<>', 0);

        //查询出数据
        $builder = $builder->select([
            'id',
            'name',
            'status',
            'detail'
        ])  ->  paginate(config('osce.page_size'));

        return $builder;
    }

    public function addCaseHistory($formData)
    {

    }
}