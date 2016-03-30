<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/30 0030
 * Time: 15:33
 */

namespace Modules\Osce\Entities;


class TeacherSubject extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'teacher_subject';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['teacher_id', 'subject_id', 'created_user_id'];

}