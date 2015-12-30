<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Repositories\Common;
class PlaceCate extends Model
{

    protected $connection	=	'osce_mis';
    protected $table 		= 	'place_cate';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'pid', 'cid'];
    public $search          =   [];

    /**
     * 与场所的关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function place()
    {
        return $this->hasMany('\Modules\Osce\Entities\Place','pid','cid');
    }

    public function showPlaceCateList($formData)
    {
        //默认查询status不为0（已删除）的场所类别
        $builder = $this->where($this->table . '.status', '<>', 0);

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
}