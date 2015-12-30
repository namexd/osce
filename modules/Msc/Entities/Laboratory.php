<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/30
 * Time: 18:26
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'short_name', 'enname','short_enname','location_id','open_type','manager_user_id','created_user_id','status','floor','code'];
    public $search          =   [];

    //判断实验室类型
    public function getType($v){
        switch ($v) {
            case 0:
                $name = '不开放';
                break;
            case 1:
                $name = '只对学生开放';
                break;
            case 2:
                $name = '只对老师开放';
                break;
            case 3:
                $name = '对所有人开放';
                break;
            case 4:
                $name = '对指定用户开放';
                break;
        }
        return $name;
    }
    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;
        $local = 'location';
        $lab = 'lab';
        $teacher = 'teacher';
        if ($where['keyword'])
        {
            $builder = $builder->where($lab.'.name','like','%'.$where['keyword'].'%');
        }
        if ($where['status'] !== '')
        {
            $builder = $builder->where($lab.'.status','=',$where['status']);
        }
        if ($where['open_type'] !== '')
        {
            $builder = $builder->where($lab.'.open_type','=',$where['open_type']);
        }
        //dd($builder);
        $builder = $builder->leftJoin('location', function($join) use($local, $lab) {
            $join->on($local.'.id', '=', $lab.'.location_id');
        })->leftJoin('teacher', function($join) use($teacher, $lab) {
            $join->on($teacher.'.id', '=', $lab.'.manager_user_id');
        })->select($lab.'.*',$local.'.name as lname',$local.'.school_id',$teacher.'.name as tname',$teacher.'.id as tid');
        return $builder->orderBy($lab.'.id')->paginate(config('msc.page_size',10));
    }

}