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
    public $timestamps	=	true;
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

    //改变职称状态
    public  function changeStatus($professionId){
        $data=$this->where('id',$professionId)->select('status')->first();

        foreach($data as $tmp){
            $status=$tmp;
        };

        return $this->where('id',$professionId)->update(['status'=>3-$status]);

    }

    public function getProfessionalTitleList(){
        return  $this->where('status','=',1)->get();
    }
}