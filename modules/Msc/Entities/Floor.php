<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/30
 * Time: 18:26
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'location';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'floor_top', 'floor_buttom','address','status','school_id','created_user_id'];
    public $search          =   [];

    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;

        if ($where['keyword'])
        {
            $builder = $builder->where($this->table.'.name','like','%'.$where['keyword'].'%');
        }
        $builder = $builder->leftJoin(
            'school',
            function($join){
                $join   ->  on(
                    $this->table. '.school_id',
                    '=',
                    'school.id'
                );
            }
        )->select($this->table.'.*','school.name as sname');
        return $builder->orderBy( $this->table.'.id')->paginate(config('msc.page_size',10));
    }
}