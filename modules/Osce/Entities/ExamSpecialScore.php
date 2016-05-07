<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7 0007
 * Time: 17:06
 */

namespace Modules\Osce\Entities;


class ExamSpecialScore extends  CommonModel
{

    protected $connection   = 'osce_mis';
    protected $table        = 'exam_special_score';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['exam_result_id', 'subject_id', 'special_score_id', 'score', 'create_user_id'];

    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'subject_id', 'id');
    }

    public function subjectSpecialScore(){
        return $this->hasOne('\Modules\Osce\Entities\SubjectSpecialScore', 'special_score_id', 'id');
    }

    public  function examResult(){
        return $this->hasOne('\Modules\Osce\Entities\ExamResult', 'id', 'exam_result_id');
    }

    public  function getExamSpecialScores($examresultId){
        return $this->where('exam_result_id', '=', $examresultId)->get();
    }

}