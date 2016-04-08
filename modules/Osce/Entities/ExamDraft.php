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


        $examDraftList=$this->leftJoin('station', function($join){
            $join -> on('exam_draft.station_id', '=', 'station.id');
        })->leftJoin('room', function($join){
            $join -> on('exam_draft.room_id', '=', 'room.id');
        })->leftJoin('subject', function($join){
            $join -> on('exam_draft.subject_id', '=', 'subject.id');
        })
            ->whereIn('exam_draft_flow_id',$ExamDraftFlowId)
            ->select([
                'exam_draft.id as id',
                'exam_draft.station_id as station_id',
                'station.type as station_type',
                'station.name as station_name',
                'exam_draft.room_id as room_id',
                'room.name as room_name',
                'exam_draft.subject_id as subject_id',
                'subject.title as subject_name',
                'exam_draft.exam_draft_flow_id as exam_draft_flow_id',
                'exam_draft.effected as effected',
            ])
            ->get()
            ->toArray();



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