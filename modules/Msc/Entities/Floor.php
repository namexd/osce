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
    protected $fillable 	=	['name', 'floor_top', 'floor_bottom','address','status','school_id','created_user_id'];
    public $search          =   [];

    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;
        //dd($where);
        if ($where['keyword'])
        {
            $builder = $builder->where($this->table.'.name','like','%'.$where['keyword'].'%');
        }
        if ($where['status'] !== null && $where['status'] !== '')
        {
            $builder = $builder->where($this->table.'.status','=',$where['status']);
        }
        if ($where['schools'] !== null && $where['schools'] !== '')
        {
            $builder = $builder->where($this->table.'.school_id','=',$where['schools']);
        }
        //dd($builder);
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
    /**
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月6日16:52:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetFloorData(){
        return  $this->where('status','=',1)->get();
    }
}