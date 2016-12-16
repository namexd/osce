<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/11/30
 * Time: 18:26
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'short_name', 'enname','short_enname','location_id','open_type','manager_user_id','created_user_id','status','floor','code'];
    public $search          =   [];

    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;

        if ($where['keyword'])
        {
            $builder = $builder->where(name,'like','%'.$where['keyword'].'%');
        }
        return $builder->orderBy('id')->paginate(config('msc.page_size',10));
    }
}