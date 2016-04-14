<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:35
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Repositories\Common;

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

    /**
     * 获取 不为null的值
     * @param $object
     * @param $value
     * @param $item
     *
     * @author Zhoufuxiang 2016-04-14
     * @return object
     */
    private function getNotNullValue($object, $value, $item){
        return Common::getNotNullValue($object, $value, $item);
    }

    /**
     * 判断 值 是否为空
     * @param $value
     * @param $code
     * @param $message
     *
     * @author Zhoufuxiang 2016-04-14
     * @return bool
     * @throws \Exception
     */
    private function judgeValueIfNull($value, $code, $message){
        return Common::valueIsNull($value, $code, $message);
    }



    public function getExamDraftFlowData($id){
        $examDraftFlowList=$this->where('exam_id','=',$id)->get();
        return $examDraftFlowList;
    }

    /**
     * 处理大表（站）数据
     * @param $data
     *
     * @author Zhoufuxiang 2016-4-11
     * @return bool
     * @throws \Exception
     */
    public function handleBigData($data)
    {
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
     *
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|object
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
            $this->judgeValueIfNull($examDraftFlow, -101, '数据有误，请重试！');       //判断是否为null

            //获取 不为null的值
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'order', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'name',  $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_screening_id', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_gradation_id', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_id',   $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'optional',  $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'number',    $item);

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
            $this->judgeValueIfNull($draftFlow, -102, '数据有误！');       //判断值是否为null

            //获取 不为null的值
            $draftFlow = $this->getNotNullValue($draftFlow, 'order', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'name',  $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_screening_id', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_gradation_id', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_id',   $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'optional',  $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'number',    $item);

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
            $this->judgeValueIfNull($newItem, -103, '数据有误，请重试！');                       //判断获取到的值是否为空
            $this->judgeValueIfNull($newItem->exam_draft_flow_id, -104, '数据有误，请重试！');   //判断获取到的值是否为空
            //再获取对应的正式表的id
            $flow_id   = $newItem->exam_draft_flow_id;
            //通过大表ID，获取小表所有对应数据
            $examDrafts= ExamDraft::where('exam_draft_flow_id','=',$flow_id)->get();
            if (count($examDrafts)>0){
                //循环删除小表对应数据
                foreach ($examDrafts as $examDraft) {
                    $examDraft->status = 1;             //软删除
                    if (!$examDraft->save()){
                        throw new \Exception('删除失败，请重试！');
                    }
                }
            }
            $result = $this->where('id','=',$flow_id)->first();
            $this->judgeValueIfNull($result, -105, '未找到对应的站的数据，请重试！');        //判断获取到的值是否为空

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