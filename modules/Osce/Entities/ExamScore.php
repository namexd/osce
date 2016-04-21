<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/14 0014
 * Time: 14:41
 */

namespace Modules\Osce\Entities;


class ExamScore extends  CommonModel
{

    protected $connection   = 'osce_mis';
    protected $table        = 'exam_score';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['exam_result_id', 'subject_id', 'standard_item_id', 'score', 'evaluate','create_user_id'];

    public function standardItem(){
        return $this->hasOne('\Modules\Osce\Entities\StandardItem','id','standard_item_id');
    }

    public  function examResult(){
        return $this->hasOne('\Modules\Osce\Entities\ExamResult','id','exam_result_id');
    }
    public function getScore($id){}

    public  function getExamScoreList($examresultId){
        $examScoreList=$this->where('exam_result_id','=',$examresultId)->get();
        return $examScoreList;

    }

}