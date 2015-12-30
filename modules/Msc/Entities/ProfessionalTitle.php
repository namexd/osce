<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 13:53
 */

namespace Modules\Msc\Entities;


use Illuminate\Database\Eloquent\Model;

class ProfessionalTitle extends Model
{

    protected $connection	=	'msc_mis';
    protected $table 		= 	'professional_title';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'code', 'description','status','id'];
    public $search          =   [];

   //获取职称列表

    public function getJobTitleList($keyword='', $status=''){
        $builder = $this;
        if($status){
            $builder = $builder->where('status','=',$status);
        }

        if ($keyword)
        {
            $builder = $builder->where('name','like','%'.$keyword.'%');
        }
        return $builder->orderBy('id')->paginate(config('msc.page_size',10));


    }

}