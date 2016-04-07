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



    public function handleBigData($data){
        switch ($data['ctrl_type']){
            case 1 : $this->bigOne($data);
                break;
            case 2 : $this->bigTwo($data);
                break;
            case 3 : $this->bigTwo($data);                //删
                break;
            case 4 :
                break;
            case 5 :
                break;
            default: throw new \Exception('操作有误！');
        }
    }

    public function bigOne($data){
        $item   =$data['item'];
        $draftFlowData = [
            'order'             => $item['order'],
            'name'              => $item['name'],
            'exam_screening_id' => $item['exam_screening_id'],
            'exam_gradation_id' => $item['exam_gradation_id'],
            'exam_id'           => $item['exam_id'],
        ];

        $result = ExamDraftFlow::create($draftFlowData);
        $item->adasdad=$result->id;
        if(!$item->save())
        {
            throw new \Exception('');
        }

    }

    public function bigTwo($data){
        $draftFlowData = [
            'order'             => $data['order'],
            'name'              => $data['name'],
            'exam_screening_id' => $data['exam_screening_id'],
            'exam_gradation_id' => $data['exam_gradation_id'],
            'exam_id'           => $data['exam_id'],
        ];

        $result = ExamDraftFlow::create($draftFlowData);


    }

}