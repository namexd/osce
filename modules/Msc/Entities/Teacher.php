<?php

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class Teacher extends CommonModel {

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'teacher';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['code','teacher_dept','validated','id'];


    /**
     * 关联用户信息
     * @return \App\Entities\User
     */
    public function userInfo()
    {
        return $this->hasOne('App\Entities\User','id','id');
    }

    public function dept(){
        return $this->belongsTo('\Modules\Msc\Entities\TeacherDept','teacher_dept');
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
}
