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

   //��ȡְ���б�

    public function getJobTitleList($keyword='', $status=''){
        $builder = $this;

        if ($keyword)
        {
            $builder = $builder->where('name','like','%'.$keyword.'%');
        }
        if(in_array($status,[1,2])){
            $builder = $builder->where('status','=',$status-1);
        }


        return $builder->orderBy('status','desc')->orderBy('id','desc')->paginate(config('msc.page_size',10));
    }

//    //�ı�ְ��״̬
//    public  function changeStatus($professionId){
//        $data=$this->where('id',$professionId)->select('status')->first();
//
//        foreach($data as $tmp){
//            $status=$tmp;
//        };
//
//        return $this->where('id',$professionId)->update(['status'=>3-$status]);
//
//    }

    public function getProfessionalTitleList(){
        return  $this->where('status','=',1)->get();
    }
}