<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 17:12
 */

namespace Modules\Osce\Entities;


class SubjectCases extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_cases';
    public    $timestamps   = false;
//    protected $primaryKey   = 'id';
    public    $incrementing = false;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'cases_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

    public function cases(){
        return $this->hasOne('\Modules\Osce\Entities\CaseModel', 'id', 'case_id');
    }

}