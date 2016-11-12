<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5 0005
 * Time: 18:10
 */

namespace Modules\Osce\Entities;


class Examinee extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'examinee';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'name', 'exam_id', 'user_id', 'idcard', 'mobile', 'code', 'avator',
        'create_user_id', 'description', 'exam_sequence', 'grade_class', 'teacher_name'
    ];

    public function userInfo()
    {
        return $this->hasOne('\App\Entities\User', 'id', 'user_id');
    }

}