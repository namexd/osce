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

    protected $ctrl_type    = [
        1   => '简单新增',
        2   => '简单更新',
        3   => '新增后更新',
        4   => '新增后，小表新增',
        5   => '删除',
        6   => '大表新增后，小表新增后更新',
    ];

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

    /**
     * 处理小表（站下面的项）数据
     * @param $data
     * @return \Exception
     */
    public function handleSmallData($data){
        try{
            switch ($data['ctrl_type']){
                case 1 : $this->smallOne($data);            //简单新增
                    break;
                case 2 : $this->smallTwo($data);            //简单更新
                    break;
                case 3 : $this->smallThree($data);          //新增后更新
                    break;
                case 5 : $this->smallFive($data);           //删除
                    break;
                case 4 :
                case 6 : $this->smallFour($data);            //大表新增后，小表新增后更新
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
     * @return \Exception|int
     */
    public function smallOne($data){
        try{
            $item      = $data['item'];
            $draftData = [
                'station_id'        => $item->station_id,
                'room_id'           => $item->room_id,
                'exam_draft_flow_id'=> $item->old_draft_flow_id,
                'subject_id'        => $item->subject_id,
                'effected'          => $item->effected,
            ];

            $result = ExamDraft::create($draftData);
            if(!$result)
            {
                throw new \Exception('添加站下的考项失败，请重试！');
            }
            //将保存后的ID，存入临时数据中
            $item->old_draft_id = $result->id;
            if(!$item->save())
            {
                throw new \Exception('添加对应站ID失败，请重试！');
            }

            return 1;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 简单更新(已经保存过后了的)
     * @param $data
     * @return \Exception|int
     */
    public function smallTwo($data){
        try{
            $item      = $data['item'];
            $draft_id  = $item->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            if (is_null($examDraft)){
                throw new \Exception('数据有误，请重试！');
            }
            $examDraft -> station_id         = $item->station_id;
            $examDraft -> room_id            = $item->room_id;
//            $examDraft -> exam_draft_flow_id = $item->exam_draft_flow_id;     //可以省略
            $examDraft -> subject_id         = $item->subject_id;
            $examDraft -> effected           = $item->effected;

            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 新增后更新(已经保存过后了的)
     * @param $data
     * @return \Exception|int
     */
    public function smallThree($data){
        try{
            $item      = $data['item'];
            $draftTemp = ExamDraftTemp::where('id','=',$item->id)->first();
            if (is_null($draftTemp)){
                throw new \Exception('数据有误，请重试！');
            }
            $draft_id  = $draftTemp->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            if (is_null($examDraft)){
                throw new \Exception('数据有误，请重试！');
            }
            $examDraft -> station_id         = $item->station_id;
            $examDraft -> room_id            = $item->room_id;
//            $examDraft -> exam_draft_flow_id = $item->exam_draft_flow_id;
            $examDraft -> subject_id         = $item->subject_id;
            $examDraft -> effected           = $item->effected;

            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 新增后，小表新增（未保存）
     * @param $data
     * @return \Exception|int
     */
    public function smallFour($data){
        try{
            $item   = $data['item'];
            $draft_flow_temp_id = $item->old_draft_flow_id;
            $exam_draft_flow_id = ExamDraftFlowTemp::where('exam_draft_flow_id','=',$draft_flow_temp_id)->first();
            if (is_null($exam_draft_flow_id)){
                throw new \Exception('数据有误，请重试！');
            }
            if(empty($exam_draft_flow_id->exam_draft_flow_id)){
                throw new \Exception('排序有误，请重试！');
            }
            $draftData = [
                'station_id'        => $item->station_id,
                'room_id'           => $item->room_id,
                'exam_draft_flow_id'=> $exam_draft_flow_id->exam_draft_flow_id,
                'subject_id'        => $item->subject_id,
                'effected'          => $item->effected,
            ];

            $result = ExamDraft::create($draftData);
            if(!$result)
            {
                throw new \Exception('添加站下的考项失败，请重试！');
            }
            return 1;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 删除
     * @param $data
     * @return \Exception|int
     */
    public function smallFive($data){
        try{
            $item      = $data['item'];
            //重新查找对应的这条数据（再赋给$item）
            $newItem   = ExamDraftTemp::where('id','=',$item->id)->first();
            //再获取对应的正式表的id
            $draft_id  = $newItem->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            if (is_null($examDraft)){
                throw new \Exception('数据有误，请重试！');
            }
            //再删除正式表中对应ID的那条数据
            if(!$examDraft->delete()){
                throw new \Exception('删除失败，请重试！');
            }

        } catch (\Exception $ex){
            throw $ex;
        }
    }


    /**
     * 查询考场安排数据
     * @param $exam_id
     * @return mixed
     */
    public function getDraftFlowData($exam_id){
        $data = $this->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', $this->table.'.exam_draft_flow_id')
                ->leftJoin('station', 'station.id', '=', $this->table.'.station_id')
                ->leftJoin('subject', 'subject.id', '=', $this->table.'.subject_id')
                ->where('exam_draft_flow.exam_id','=',$exam_id)
                ->select([
                    'exam_draft.id','exam_draft.subject_id','subject.title as subject_title',
                    'station.id as station_id','station.name as station_name','station.type as station_type'
                ])
                ->groupBy('station_id')->get();

        return $data;
    }
}