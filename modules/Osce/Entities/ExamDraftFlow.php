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
    protected $fillable     = ['id','name','order','exam_id','exam_screening_id', 'exam_gradation_id'];

    protected $ctrl_type    = [
        1   => '简单新增',
        2   => '简单更新',
        3   => '新增后更新',
        5   => '删除',
    ];



    public function getExamDraftFlowData($id){
        $examDraftFlowList=$this->where('exam_id','=',$id)->get();
        return $examDraftFlowList;
    }

    /**
     * 处理大表（站）数据
     * @param $data
     * @return \Exception
     */
    public function handleBigData($data){
        try{
            switch ($data['ctrl_type']){
                case 1 : $this->bigOne($data);              //简单新增
                    break;
                case 2 : $this->bigTwo($data);              //简单更新
                    break;
                case 3 : $this->bigThree($data);            //新增后更新
                    break;
                case 5 : $this->bigFive($data);             //删除
                    break;
                default: throw new \Exception('操作有误！');
            }

        } catch (\Exception $ex){
            return $ex;
        }
    }

    public function bigOne($data){
        try{
            $item   = $data['item'];
            $draftFlowData = [
                'order'             => $item->order,
                'name'              => $item->name,
                'exam_screening_id' => $item->exam_screening_id,
                'exam_gradation_id' => $item->exam_gradation_id,
                'exam_id'           => $item->exam_id,
            ];

            $result = ExamDraftFlow::create($draftFlowData);
            $item->exam_draft_flow_id = $result->id;
            if(!$item->save())
            {
                throw new \Exception('添加对应站ID失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            return $ex;
        }
    }

    public function bigTwo($data){
        try{
            $item          = $data['item'];
            $draft_flow_id = $item->exam_draft_flow_id;
            $examDraftFlow = ExamDraftFlow::where('id','=',$draft_flow_id)->first();
            if (is_null($examDraftFlow)){
                throw new \Exception('数据有误，请重试！');
            }
            $examDraftFlow -> order             = $item->order;
            $examDraftFlow -> name              = $item->name;
            $examDraftFlow -> exam_screening_id = $item->exam_screening_id;
            $examDraftFlow -> exam_gradation_id = $item->exam_gradation_id;
            $examDraftFlow -> exam_id           = $item->exam_id;

            if(!$examDraftFlow->save())
            {
                throw new \Exception('更新站失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            return $ex;
        }
    }

    /**
     * 新增后更新(已经保存过后了的)
     * @param $data
     * @return \Exception|int
     */
    public function bigThree($data){
        try{
            $item       = $data['item'];
            $id         = $item->exam_draft_flow_id;
            $draftFlow  = $this->where('id','=',$id)->first();
            $draftFlow -> order             = $item->order;
            $draftFlow -> name              = $item->name;
            $draftFlow -> exam_screening_id = $item->exam_screening_id;
            $draftFlow -> exam_gradation_id = $item->exam_gradation_id;
            $draftFlow -> exam_id           = $item->exam_id;

            if(!$result = $draftFlow->save()){
                throw new \Exception('跟新 '.$draftFlow -> name.' 失败，请重试！');
            }

            $item->exam_draft_flow_id = $result->id;
            if(!$item->save())
            {
                throw new \Exception('添加对应站ID失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            return $ex;
        }
    }


    /**
     * 删除
     * @param $data
     * @return \Exception|int
     */
    public function bigFive(){

    }
}