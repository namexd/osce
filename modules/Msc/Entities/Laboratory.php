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
    protected $fillable 	=	['name', 'short_name','total', 'enname','short_enname','location_id','open_type','manager_user_id','created_user_id','status','floor','code'];
    public $search          =   [];

    //判断实验室类型
    public function getType($v){
        switch ($v) {
            case 1:
                $name = '实验室';
                break;
            case 2:
                $name = '准备间';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }


    //楼栋
    public function floors(){

        return $this->hasOne('Modules\Msc\Entities\Floor','id','location_id');
    }
    //用户管理员
    public function user(){

        return $this->hasOne('App\Entities\User','id','manager_user_id');
    }

    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;
        $local = 'location';
        $lab = 'lab';
        $user = 'user';
        if ($where['keyword'])
        {
            $builder = $builder->where($lab.'.name','like','%'.$where['keyword'].'%');
        }
        if ($where['status'] !== null && $where['status'] !== '')
        {
            $builder = $builder->where($lab.'.status','=',$where['status']);
        }
        if ($where['open_type'] !== null && $where['open_type'] !== '')
        {
            $builder = $builder->where($lab.'.open_type','=',$where['open_type']);
        }
        $builder = $builder->with(['floors','user']);
//        dd($builder);
//        $builder = $builder->leftJoin('location', function($join) use($local, $lab) {
//            $join->on($local.'.id', '=', $lab.'.location_id');
//        })->leftJoin('user', function($join) use($user, $lab) {
//            $join->on($user.'.id', '=', $lab.'.manager_user_id');
//        })->select($lab.'.*',$local.'.name as lname',$local.'.school_id',$user.'.name as tname',$user.'.id as tid');
        return $builder->orderBy('id')->paginate(config('msc.page_size',10));
    }

    public function OrdinaryLaboratoryList(){
        $this->where()->paginate(config('msc.page_size',10));
    }

}