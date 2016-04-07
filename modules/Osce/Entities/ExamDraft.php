<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:36
 */

namespace Modules\Osce\Entities;


class ExamDraft extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['station_id', 'room_id', 'subject_id',  'exam_draft_flow_id', 'effected'];


    public function getExamDraftData($ExamDraftFlowId){
        $examDraftList=$this->whereIn('exam_draft_flow_id',$ExamDraftFlowId)->get();
        return $examDraftList;

    }



    public function handleSmallData($data){

        switch ($data['ctrl_type']){
//            case 1 : $this->smallOne($data);
//                break;
//            case 2 : $this->smallTwo($data);
//                break;
//            case 3 : $this->smallThree($data);
//                break;
//            case 4 : $this->smallOne($data);
//                break;
//            case 5 : $this->smallOne($data);
//                break;
//            default: throw new \Exception('操作有误！');
        }
    }

}