<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/7
 * Time: 17:04
 */

namespace modules\Osce\Entities;


class ExamSpTeacher
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_sp_teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['invite_id', 'exam_screening_id', 'case_id', 'teacher_id', 'create_user_id'];

    public function addExamSp(array $list){
//        dd($list['teacher_id'],$list['invite_id'] );

        return  $this ->where('teacher_id','=',$list['teacher_id'])->update('invite_id','=',$list['invite_id']);
    }


}