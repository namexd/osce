<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:36
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Repositories\Common;

class ExamDraft extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['station_id', 'room_id', 'subject_id',  'exam_draft_flow_id', 'status', 'effected'];

    protected $ctrl_type    = [
        1   => '简单新增',
        2   => '简单更新',
        3   => '新增后更新',
        4   => '新增后，小表新增',
        5   => '删除',
        6   => '大表新增后，小表新增后更新',
    ];

    public function subejct(){
        return $this->hasOne('Modules\Osce\Entities\Subject','id','subject_id');
    }

    public function station(){
        return $this->hasOne('Modules\Osce\Entities\Station','id','station_id');
    }
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



    public function getExamDraftData($ExamDraftFlowId){
        $examDraftList=$this->leftJoin('station', function($join){
            $join -> on('exam_draft.station_id', '=', 'station.id');
        })->leftJoin('room', function($join){
            $join -> on('exam_draft.room_id', '=', 'room.id');
        })->leftJoin('subject', function($join){
            $join -> on('exam_draft.subject_id', '=', 'subject.id');
        })->leftJoin('exam_draft_flow', function($join){
            $join -> on('exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id');
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
                'exam_draft_flow.optional',
                'exam_draft_flow.number',
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
            $value = $data['item']->ctrl_type;
            switch ($value){
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
            return $data;

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

            return $item;

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
            $this->judgeValueIfNull($examDraft, -201, '数据有误，请重试！');       //判断值是否为null

            //获取 不为null的值
            $examDraft = $this->getNotNullValue($examDraft, 'station_id', $item);
            $examDraft = $this->getNotNullValue($examDraft, 'room_id',    $item);
            $examDraft = $this->getNotNullValue($examDraft, 'effected',   $item);
            $examDraft = $this->getNotNullValue($examDraft, 'subject_id', $item);
            //理论考站，考试项目 另作处理
            if (!is_null($examDraft->station_id)){
                $Station = Station::where('id','=',$examDraft->station_id)->first();
                if (!is_null($Station) && $Station->type == 3){

                    $examDraft->subject_id = $item->subject_id;
                }
            }
            
            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return $examDraft;

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
            $this->judgeValueIfNull($draftTemp, -202, '数据有误，请重试！');       //判断值是否为null

            $draft_id  = $draftTemp->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            $this->judgeValueIfNull($examDraft, -203, '数据有误，请重试！');       //判断值是否为null

            //获取 不为null的值
            $examDraft = $this->getNotNullValue($examDraft, 'station_id', $item);
            $examDraft = $this->getNotNullValue($examDraft, 'room_id',    $item);
            $examDraft = $this->getNotNullValue($examDraft, 'effected',   $item);
            $examDraft = $this->getNotNullValue($examDraft, 'subject_id', $item);
            //理论考站，考试项目 另作处理
            if (!is_null($examDraft->station_id)){
                $Station = Station::where('id','=',$examDraft->station_id)->first();
                if (!is_null($Station) && $Station->type == 3){

                    $examDraft->subject_id = $item->subject_id;
                }
            }

            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return $examDraft;

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
            $exam_draft_flow_id = ExamDraftFlowTemp::where('id','=',$draft_flow_temp_id)->first();
            $this->judgeValueIfNull($exam_draft_flow_id, -204, '数据有误，请重试！');       //判断值是否为null
            $this->judgeValueIfNull($exam_draft_flow_id->exam_draft_flow_id, -205, '排序有误，请重试！');       //判断值是否为null

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
            return $result;

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
            $this->judgeValueIfNull($examDraft, -206, '数据有误，请重试！');       //判断值是否为null

            //再删除正式表中对应ID的那条数据
            $examDraft->status = 1;         //软删除
            if(!$examDraft->save()){
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
//                ->leftJoin('station_teacher', 'station_teacher.station_id', '=', $this->table.'.station_id')
                ->where('exam_draft_flow.exam_id','=',$exam_id)
                ->select([
                    'exam_draft.id','exam_draft.subject_id','subject.title as subject_title','subject.id as subject_id',

                    'station.id as station_id','station.name as station_name','station.type as station_type',
                    'exam_draft_flow.exam_gradation_id as exam_gradation_id',
                ])
//              ->groupBy('subject.id')
                ->get();

        return $data;
    }

    /**
     * 理论考站，处理考试、试卷、考站关系
     * @param $item
     *
     * @author Zhoufuxiang 2016-04-18
     * @return array|static
     * @throws \Exception
     */
    public function handleExamPaperStation($exam_id)
    {
        //查询考试对应理论考站、试卷数据
        $result = ExamPaperExamStation::where('exam_id','=',$exam_id)->select('station_id')
                ->get()->pluck('station_id')->toArray();

        $stations = $this->leftJoin('station','station.id','=','exam_draft.station_id')
                    ->leftJoin('exam_draft_flow','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
                    ->where('exam_draft_flow.exam_id','=',$exam_id)
                    ->where('station.type','=',3)
                    ->select(['exam_draft.station_id'])
                    ->get()->pluck('station_id')->toArray();

        $delStations = array_diff($result, $stations);     //原来有，现在不具有（需删除）
        $addStations = array_diff($stations, $result);     //现在有，原来不具有（需添加）

        //删除考试、试卷、考站关系
        if (!empty($delStations)){
            $delPaperStiations = ExamPaperExamStation::where('exam_id','=',$exam_id)->whereIn('station_id',$delStations)->get();
            if (!$delPaperStiations->isEmpty()){
                foreach ($delPaperStiations as $item) {
                    if (!$item->delete()){
                        throw new \Exception('删除考试试卷考站关系失败，请重试！');
                    }
                }
            }
        }

        //添加考试、试卷、考站关系
        if (!empty($addStations)){
            foreach ($addStations as $station_id) {
                $Station = Station::where('id','=',$station_id)->first();
                $paper_exam_station = [
                    'exam_id'       => $exam_id,
                    'exam_paper_id' => $Station->paper_id,
                    'station_id'    => $Station->id,
                ];
                $result = ExamPaperExamStation::create($paper_exam_station);
                if (!$result){
                    throw new \Exception('添加考试试卷考站关系失败，请重试！');
                }
            }
        }

        return $result;
    }
}