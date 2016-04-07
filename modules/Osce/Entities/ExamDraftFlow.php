<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:35
 */

namespace Modules\Osce\Entities;


class ExamDraftFlow extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft_flow';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['name',  'order',  'exam_id', 'exam_screening_id', 'exam_gradation_id'];



      public function getExamDraftFlowData($id){
          $examDraftFlowList=$this->where('exam_id','=',$id)->get();
          return $examDraftFlowList;

        }
}