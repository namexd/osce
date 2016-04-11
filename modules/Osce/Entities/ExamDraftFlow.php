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
    protected $fillable     = ['name', 'order', 'exam_id', 'exam_screening_id', 'exam_gradation_id', 'optional', 'number', 'status'];

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
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception
     */
    public function handleBigData($data){

        $value = $data['item']->ctrl_type;
        try{
            switch ($value){
                case 1 : $this->bigOne($data);              //简单新增
                    break;
                case 2 : $this->bigTwo($data);              //简单更新
                    break;
                case 3 : $this->bigThree($data);            //新增后更新
                    break;
                case 7 :
                case 5 : $this->bigFive($data);             //删除
                    break;
                default: throw new \Exception('操作有误！');
            }
            return true;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 简单新增
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
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
            return $item;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 简单更新
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
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
            return $examDraftFlow;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 新增后更新(已经保存过后了的)
     * @param $data
     * @author Zhoufuxiang 2016-4-11
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
            return $item;

        } catch (\Exception $ex){
            throw $ex;
        }
    }


    /**
     * 删除
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
    public function bigFive($data){
        try{
            $item      = $data['item'];
            //重新查找对应的这条数据（再赋给$item）
            $newItem   = ExamDraftFlowTemp::where('id','=',$item->id)->first();
            //再获取对应的正式表的id
            $draft_flow_id = $newItem->exam_draft_flow_id;
            //通过大表ID，获取小表所有对应数据
            $examDrafts    = ExamDraft::where('exam_draft_flow_id','=',$draft_flow_id)->get();
            if (count($examDrafts)>0){
                //循环删除小表对应数据
                foreach ($examDrafts as $examDraft) {
                    $examDraft->status = 1;             //软删除
                    if (!$examDraft->save()){
                        throw new \Exception('删除失败，请重试！');
                    }
                }
            }
            $result = $this->where('id','=',$draft_flow_id)->first();
            if (is_null($result)){
                throw new \Exception('未找到对应的站的数据，请重试！');
            }
            //再删除正式表（大表）中对应ID的那条数据
            $result->status = 1;             //软删除
            if(!$result->save()){
                throw new \Exception('删除失败，请重试！');
            }

        } catch (\Exception $ex){
            throw $ex;
        }
    }
}