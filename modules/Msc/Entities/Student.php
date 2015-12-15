<?php

namespace Modules\Msc\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Msc\Entities\CommonModel;
use App\Entities\User;

class Student extends CommonModel {

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'student';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name','code','qq','class','grade','professional','student_type','validated','id'];


    /**
     * 关联用户信息
     * @return \App\Entities\User
     */
    public function userInfo()
    {
        return $this->hasOne('App\Entities\User','id','id');
    }

    /**
     * 格式化班级名称
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function className(){
        return $this->belongsTo('Modules\Msc\Entities\StdClass','class');
    }

    /**
     * 格式化专业名称
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professionalName(){
        return $this->belongsTo('Modules\Msc\Entities\StdProfessional','professional');
    }

    /**
     * 格式化用户证件类型
     * @return mixed
     */
    public function getIdCardType(){
        $value=config('msc.idcard_type');
        if( is_array($value) && $this->userInfo!=null ){
            if(array_key_exists($this->userInfo->idcard_type,$value)){
                return $value[$this->userInfo->idcard_type];
            }
        }
    }

    // 学生类型获取器
    public function getStudentTypeAttribute ($value)
    {
        switch ($value) {
            case 1:
                $type = '本科';
                break;

            case 2:
                $type = '专科';
                break;

            default:
                $type = '-';
        }

        return $type;
    }

    // 获得分页列表
    public function getFilteredPaginateList ($kwd='', $order=['id', 'desc'])
    {
        $builder = $this;

        if ($kwd)
        {
            $builder = $builder->whereRaw(
                'locate(?, student.name)>0 or locate(?, student.code)>0 ',
                [$kwd, $kwd]
            );
        }

        return $builder->orderBy($order['0'], $order['1'])->paginate(config('msc.page_size',10));
    }

    //保存编辑数据
    public function saveEditStudent($data){
       $connection=\DB::connection('msc_mis');
       $connection->beginTransaction();

       $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'grade'=>$data['grade'],'professional'=>$data['professional'],'student_type'=>$data['student_type']);

       $result=$connection->table('student')->save($item);
       if($result==false){
          $connection->rollBack();
       }

       $connection=\DB::connection('sys_mis');
       $users=array('id'=>$data['id'],'gender'=>$data['gender'],'moblie'=>$data['moblie'],'idcard_type'=>$data['idcard_type'],'idcard'=>$data['idcard']);
       $result=$connection->table('users')->save($users);
        if($result==false){
            $connection->rollBack();
        }

        $connection->commit();
    }

    //保存添加学生

    public function postAddStudent($data){
        $connection=\DB::connection('msc_mis');


        $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'grade'=>$data['grade'],'professional'=>$data['professional'],'student_type'=>$data['student_type']);

        $id=$connection->table('student')->insertGetId($item);


        $connection=\DB::connection('sys_mis');
        $users=array('id'=>$id,'gender'=>$data['gender'],'moblie'=>$data['moblie'],'idcard_type'=>$data['idcard_type'],'idcard'=>$data['idcard']);

        $result=$connection->table('users')->insert($users);

        return $result;
    }

    //软删除
    public function SoftTrashed($id){
        $connection=\DB::connection('sys_mis');

        return $connection->table('users')->where('id',$id)->update(['status'=>2]);

    }

    //更改状态
    public function changeStatus($id){
         $connection=\DB::connection('sys_mis');


         $data=$connection->table('users')->where('id',$id)->select('status')->find(2);

         foreach($data as $tmp){
            $status=$tmp;
         }

         return $connection->table('users')->where('id',$id)->update(['status'=>1-$status]);

    }
}
