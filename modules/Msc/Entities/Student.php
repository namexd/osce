<?php

namespace Modules\Msc\Entities;

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


}
