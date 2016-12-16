<?php
/**
 * Created by PhpStorm.
 * 专业模型
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/11/6
 * Time: 11:02
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class StdProfessional extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'student_professional';
    protected $fillable 	=	["id","name","code"];
    public $incrementing	=	true;
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];





//获得专业分页列表

   public  function getprofessionList($keyword='',$status=''){

       $connection=\DB::connection('msc_mis');

       $professionTable=$connection->table('student_professional');

        if($keyword){
            $professionTable=$professionTable->where('name','like'.'%',$keyword.'%');
        }
       if($status){
           $professionTable =$professionTable->where('status','=',$status);
       }


       return $professionTable->select(['id','name','code','status'])->orderBy('id')->paginate(config('msc.page_size',10));
   }


    //新增专业
    public  function postAddProfession($data){

         $profession  = $this->where('name',$data['name']&&'code',$data['code'])->frist();
        if($profession){
            throw new \Exception('该专业已存在');
        }
        $item=array(
            'name'=>$data['name'],
            'code'=>$data['code'],
            'status'=>$data['status']
        );
        $result=$this->create($item);
        return $result;
    }


//提交编辑
    public  function postSaveProfession($data){
        $input=[
            'name'=>$data['name'],
            'code'=>$data['code'],
            'status'=>$data['status']
        ];
        return $this->create($input);
    }

//改变专业状态
    public  function changeStatus($professionId){
        $data=$this->where('id',$professionId)->select('status')->first();

        foreach($data as $tmp){
            $status=$tmp;
        };

        return $this->where('id',$professionId)->update(['status'=>3-$status]);

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