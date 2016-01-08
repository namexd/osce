<?php
/**
 * Created by PhpStorm.
 * 专业模型
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/6
 * Time: 11:02
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StdProfessional extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_professional';
    protected $fillable 	=	["id","name","code","created_user_id","status"];
    public $incrementing	=	true;
    public $timestamps	=	true;
    protected $primaryKey	=	'id';

//获得专业分页列表

   public  function getprofessionList($keyword='',$status=''){

       $builder = $this;

       if ($keyword)
       {
           $builder = $builder->where($this->table.'.name','like','%'.$keyword.'%');
       }
       if($status){
           $builder = $builder->where($this->table.'.status',$status);
       }


       return $builder->select(['id','name','code','status'])->orderBy('id')->paginate(config('msc.page_size',10));
   }


//    //新增专业
//    public  function postAddProfession($data){
//
//        $item=array(
//            'name'=>$data['name'],
//            'code'=>$data['code'],
//            'status'=>$data['status']
//        );
//        $result=$this->create($item);
//        return $result;
//    }


//提交编辑
    public  function postSaveProfession($data){
        $input=[
            'name'=>$data['name'],
            'code'=>$data['code'],
            'status'=>$data['status']
        ];
        return $this->where('id','=',$data['id'])->update($input);
    }

    //专业删除
     public  function  SoftTrashed($id){
         return $this->where('id',$id)->update(['status'=>3]);
     }




    //专业导入
    public function  ProfessionImport($professionData){
        $data=[
            'name'=>$professionData['name'],
            'code'=>$professionData['code'],
            'status'=>$professionData['status']
        ];
        $result= $this->create($data);
         return $result;
    }



}