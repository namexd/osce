<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:35
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Repositories\Common;
use DB;
use Modules\Osce\Entities\ExamArrange\ExamArrangeRepository;

class ExamDraftFlow extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_draft_flow';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'order', 'exam_id', 'exam_screening_id', 'exam_gradation_id', 'optional', 'number', 'status'];

    protected $ctrl_type = [
        1 => '简单新增',
        2 => '简单更新',
        3 => '新增后更新',
        5 => '删除',
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
    private function getNotNullValue($object, $value, $item)
    {
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
    private function judgeValueIfNull($value, $code, $message)
    {
        return Common::valueIsNull($value, $code, $message);
    }


    public function getExamDraftFlowData($id)
    {
        $examDraftFlowList = $this->where('exam_id', '=', $id)->get();
        return $examDraftFlowList;
    }

    /**
     * 保存考场安排所有数据
     * @param $exam_id
     *
     * @author Zhoufuxiang 2016-04-14
     * @return array
     */
    public function saveArrangeDatas($exam_id, $condition = [], ExamArrangeRepository $examArrangeRepository, $FrontArrangeData, $status)
    {
        $connection = DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            $ExamDraft = new ExamDraft();
            //获取所有临时数据
            $datas = $this->getAllTempDatas($exam_id);

            foreach ($datas as $data) {
                //操作大表
                if ($data['is_draft_flow'] == 1) {
                    if (!$this->handleBigData($data)) {
                        throw new \Exception('保存失败，请重试！');
                    }

                    //操作小表
                } else {
                    if (!$ExamDraft->handleSmallData($data)) {
                        throw new \Exception('保存失败，请重试！');
                    }
                }
            }

            //处理 待删除 数据（如：清空临时表数据，删除正式表待删除数据）
            $ExamDraftTempModel = new ExamDraftTemp();
            if (!$ExamDraftTempModel->handleDelDatas($exam_id)) {
                throw new \Exception('处理待删除数据失败');
            }
            //变更站顺序、名称
            $this->changeStationOrder($exam_id);
            //理论考站，处理考试、试卷、考站关系
            $ExamDraft->handleExamPaperStation($exam_id);

            //存在阶段ID，为临时获取数据，无需保存提交
            if (!empty($condition)) {
                $reData = $this->getTempDatas($exam_id, $condition);
                $connection->rollBack();
                return $reData;
            }


            //最后检查，考站、考场安排是否符合要求
            $exam = Exam::doingExam($exam_id);
            switch ($exam->sequence_mode) {
                case 1:
                    $examArrangeRepository->checkData($exam_id, 'station_id');
                    $examArrangeRepository->checkData($exam_id, 'room_id');
                    break;
                case 2:
                    $examArrangeRepository->checkData($exam_id, 'station_id');
                    break;
                default:
                    throw new \Exception('System Error!');
            }

            if ($status == 1) {
                //清空数据
                $result = $examArrangeRepository->resetSmartArrange($exam_id);
                //清空考官安排
                
                if ($result||$result==null){
                    $connection->commit();
                    return true;
                }

            }
            //判断挡前数据和之前数据是否有变化如果有就清除排考内容
            $LaterArrangeData = $examArrangeRepository->getInquireExamArrange($exam_id);
            $ArrangeData = $examArrangeRepository->getDataDifference($exam_id, $FrontArrangeData, $LaterArrangeData);
            if ($ArrangeData){
                //有改动还回code=-1用户确定
                return -100;
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {

            //存在阶段ID，为临时获取数据，无需保存提交
            if (!empty($condition)) {
                $reData = $this->getTempDatas($exam_id, $condition);
                $connection->rollBack();
                return $reData;
            }

            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 获取所有临时数据
     * @param $exam_id
     *
     * @author Zhoufuxiang 2016-04-07
     * @return array
     */
    private function getAllTempDatas($exam_id)
    {
        //获取所有临时数据
        $draftFlows = ExamDraftFlowTemp::where('exam_id', '=', $exam_id)->orderBy('created_at')->get();
        $drafts = ExamDraftTemp::where('exam_id', '=', $exam_id)->orderBy('created_at')->get();

        //所有临时数据 组合
        $datas = [];
        foreach ($draftFlows as $draftFlow) {
            $datas[strtotime($draftFlow->created_at->format('Y-m-d H:i:s'))] = [
                'item' => $draftFlow,
                'is_draft_flow' => 1
            ];
        }

        foreach ($drafts as $draft) {

            $time = strtotime($this->timeIndex($datas, $draft->add_time));
            $datas[$time] = [
                'item' => $draft,
                'is_draft_flow' => 0
            ];
        }
        ksort($datas);     //数组按时间（键）进行排序

        return $datas;
    }

    /**
     * 时间转换
     * @param $data
     * @param $time
     * @return mixed
     */
    private function timeIndex($data, $time)
    {
        if (array_key_exists(strtotime($time), $data)) {
            $time = strtotime($time) + 1;
            return $this->timeIndex($data, date('Y-m-d H:i:s', $time));
        } else {
            return $time;
        }
    }

    /**
     * 处理大表（站）数据
     * @param $data
     *
     * @author Zhoufuxiang 2016-4-11
     * @return bool
     * @throws \Exception
     */
    private function handleBigData($data)
    {
        $value = $data['item']->ctrl_type;
        try {
            switch ($value) {
                case 1 :
                    $this->bigOne($data);              //简单新增
                    break;
                case 2 :
                    $this->bigTwo($data);              //简单更新
                    break;
                case 3 :
                    $this->bigThree($data);            //新增后更新
                    break;
                case 7 :
                case 5 :
                    $this->bigFive($data);             //删除
                    break;
                default:
                    throw new \Exception('操作有误！');
            }
            return $data;

        } catch (\Exception $ex) {
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
    public function bigOne($data)
    {
        try {
            $item = $data['item'];
            $draftFlowData = [
                'order'             => $item->order,
                'name'              => $item->name,
                'exam_id'           => $item->exam_id,
                'exam_screening_id' => $item->exam_screening_id,
                'exam_gradation_id' => $item->exam_gradation_id,
            ];

            $result = ExamDraftFlow::create($draftFlowData);
            $item->exam_draft_flow_id = $result->id;

            if (!$item->save()) {
                throw new \Exception('添加对应站ID失败，请重试！');
            }
            return $item;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 简单更新
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
    public function bigTwo($data)
    {
        try {
            $item = $data['item'];
            $draft_flow_id = $item->exam_draft_flow_id;
            $examDraftFlow = ExamDraftFlow::where('id', '=', $draft_flow_id)->first();
            $this->judgeValueIfNull($examDraftFlow, -101, '数据有误，请重试！');       //判断是否为null

            //获取 不为null的值
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'order', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'name', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_screening_id', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_gradation_id', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'exam_id', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'optional', $item);
            $examDraftFlow = $this->getNotNullValue($examDraftFlow, 'number', $item);

            if (!$examDraftFlow->save()) {
                throw new \Exception('更新站失败，请重试！');
            }
            return $examDraftFlow;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 新增后更新(已经保存过后了的)
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
    public function bigThree($data)
    {
        try {
            $item = $data['item'];
            $id = $item->exam_draft_flow_id;
            $draftFlow = $this->where('id', '=', $id)->first();
            $this->judgeValueIfNull($draftFlow, -102, '数据有误！');       //判断值是否为null

            //获取 不为null的值
            $draftFlow = $this->getNotNullValue($draftFlow, 'order', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'name', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_screening_id', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_gradation_id', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'exam_id', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'optional', $item);
            $draftFlow = $this->getNotNullValue($draftFlow, 'number', $item);

            if (!$result = $draftFlow->save()) {
                throw new \Exception('跟新 ' . $draftFlow->name . ' 失败，请重试！');
            }

            $item->exam_draft_flow_id = $result->id;
            if (!$item->save()) {
                throw new \Exception('添加对应站ID失败，请重试！');
            }
            return $item;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * 删除
     * @param $data
     * @author Zhoufuxiang 2016-4-11
     * @return \Exception|int
     */
    public function bigFive($data)
    {
        try {
            $item = $data['item'];
            //重新查找对应的这条数据（再赋给$item）
            $newItem = ExamDraftFlowTemp::where('id', '=', $item->id)->first();
            $this->judgeValueIfNull($newItem, -103, '数据有误，请重试！');                       //判断获取到的值是否为空
            $this->judgeValueIfNull($newItem->exam_draft_flow_id, -104, '数据有误，请重试！');   //判断获取到的值是否为空
            //再获取对应的正式表的id
            $flow_id = $newItem->exam_draft_flow_id;
            //通过大表ID，获取小表所有对应数据
            $examDrafts = ExamDraft::where('exam_draft_flow_id', '=', $flow_id)->get();
            if (count($examDrafts) > 0) {
                //循环删除小表对应数据
                foreach ($examDrafts as $examDraft) {
                    $examDraft->status = 1;             //软删除
                    if (!$examDraft->save()) {
                        throw new \Exception('删除失败，请重试！');
                    }
                }
            }
            $result = $this->where('id', '=', $flow_id)->first();
            $this->judgeValueIfNull($result, -105, '未找到对应的站的数据，请重试！');        //判断获取到的值是否为空

            //再删除正式表（大表）中对应ID的那条数据
            $result->status = 1;             //软删除
            if (!$result->save()) {
                throw new \Exception('删除失败，请重试！');
            }
            return $result;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取临时保存数据
     * @param $exam_id
     * @param $stage_id
     * @param null $room
     * @param null $station
     *
     * @author Zhoufuxiang 2016-4-15
     * @return array
     */
    public function getTempDatas($exam_id, $condition)
    {
        $examInfo = Exam::where('id', '=', $exam_id)->first();
        //考站分组模式，考场无限制 2016-04-18 Zhoufuxiang
        if ($examInfo->sequence_mode == 2 && $condition['room'] === 1) {
            return [];
        }

        $datas = ExamDraft::select(['exam_draft.room_id', 'exam_draft.station_id'])
            ->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->where('exam_draft_flow.exam_gradation_id', '=', $condition['stage_id']);

        //考场分组模式
        if ($examInfo->sequence_mode == 1) {
            if ($condition['room'] === 1) {
                $datas = $datas->where('exam_draft_flow.order', '<>', $condition['order']);
            }
        }

        //取考场相关数据
        if ($condition['room'] === 1) {
            $rooms = $datas->whereNotNull('exam_draft.room_id')->groupBy('exam_draft.room_id')->get();
            if (!$rooms->isEmpty()) {
                $datas = $rooms->pluck('room_id')->toArray();
            } else {
                $datas = [];
            }
        }
        //取考站相关数据
        if ($condition['station'] === 1) {
            $stations = $datas->whereNotNull('exam_draft.station_id')->groupBy('exam_draft.station_id')->get();
            if (!$stations->isEmpty()) {
                $datas = $stations->pluck('station_id')->toArray();
            } else {
                $datas = [];
            }
        }
        return $datas;
    }

    /**
     * 清空考场安排数据
     * @param $exam_id
     *
     * @author Zhoufuxiang 2016-4-16
     * @throws \Exception
     */
    public function delDraftDatas($exam_id)
    {
        //1、清空考场安排 临时表数据
        $draftTemp = new ExamDraftTemp();
        $tempData  = $draftTemp->getTempData($exam_id);
        if(!$tempData){
            throw new \Exception('清空临时数据失败');
        }

        $draftFlows = $this->where('exam_id','=',$exam_id)->get();
        if (!$draftFlows->isEmpty()){
            foreach ($draftFlows as $index => $draftFlow) {
                $examDrafts = ExamDraft::where('exam_draft_flow_id','=',$draftFlow->id)->get();
                if (!$examDrafts->isEmpty()){
                    foreach ($examDrafts as $index => $examDraft) {
                        if (!$examDraft->delete()){
                            throw new \Exception('删除考场安排失败，请重试！');
                        }
                    }
                }
                //删除站
                if (!$draftFlow->delete()){
                    throw new \Exception('删除考场安排失败，请重试！');
                }
            }
        }

        return $draftFlows;
    }

    /**
     * 修改站名称、序号
     * @param $exam_id
     * @return mixed
     * @throws \Exception
     */
    public function changeStationOrder($exam_id)
    {
        $result = $this->where('exam_id','=',$exam_id)->orderBy('id')->get();
        if (!$result->isEmpty()){
            foreach ($result as $key => $item) {
                $item->order = $key+1;
                $item->name  = '第'.($key+1).'站';
                //保存
                if (!$item->save()){
                    throw new \Exception('保存站名称、序号失败');
                }
            }
        }
        return $result;
    }
}