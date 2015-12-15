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
        $builder = $this->leftJoin('student_professional', function ($join) {
            $join->on('student_professional.id', '=', 'student.professional');
        });

        if ($kwd)
        {
            $builder = $builder->whereRaw(
                'locate(?, student.name)>0 or locate(?, student.code)>0 ',
                [$kwd, $kwd]
            );
        }

        return $builder->
            select([
                'student.id as id',
                'student.name as name',
                'student.code as code',
                'student.grade as grade',
                'student.student_type as student_type',
                'student_professional.name as profession_name',
                //'user.mobile as mobile',
                //'user.idcard as idcard',
                //'user.gender as gender',
                //'user.status as status',
        ])->orderBy($order['0'], $order['1'])->paginate(config('msc.page_size',10));
    }



}
