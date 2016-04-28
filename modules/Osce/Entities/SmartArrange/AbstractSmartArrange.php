<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 11:26
 */

namespace Modules\Osce\Entities\SmartArrange;

use Modules\Osce\Entities\SmartArrange\Traits\CheckTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamOrder;


abstract class AbstractSmartArrange implements SmartArrangeInterface
{
    use CheckTraits, SQLTraits;

    protected $model;

    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * 类的名字
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-13 11:31
     */
    abstract function model();

    public function makeModel()
    {
        $model = \App::make($this->model());

        return $this->model = $model;
    }

    /**
     * 更改effect
     * @param $exam
     * @param array $attributes
     * @author Jiangzhiheng
     * @time 2016-04-14 15:06
     */
    public function changeEffect($exam, array $attributes = [])
    {
        if (count($attributes) == 0) {
            $result = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                ->where('exam_draft_flow.exam_id', $exam->id)->get();
            foreach ($result as $item) {
                $item->effected = 0;
                if (!$item->save()) {
                    throw new \Exception('数据更新失败！');
                }
            }
            return true;
        } else {
            switch ($exam->sequence_mode) {
                case 1:
                    $rooms = collect($attributes)->pluck('room_id')->unique()->toArray();
                    $a = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                        ->whereIn('exam_draft.room_id', $rooms)
                        ->where('exam_draft_flow.exam_id', $exam->id)
                        ->get();
                    foreach ($a as $item) {
                        $item->effected = 0;
                        if (!$item->save()) {
                            throw new \Exception('数据更新失败！');
                        }
                    }

                    break;
                case 2:
                    $stations = collect($attributes)->pluck('station_id')->unique()->toArray();
                    $a = ExamDraft::join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
                        ->whereIn('exam_draft.station_id', $stations)
                        ->where('exam_draft_flow.exam_id', $exam->id)
                        ->get();
                    foreach ($a as $item) {
                        $item->effected = 0;
                        if (!$item->save()) {
                            throw new \Exception('数据更新失败！');
                        }
                    }

                    break;
                default:
                    throw new \Exception('没有这种考试模式！', -987);
                    break;
            }

            return true;
        }
    }

    /**
     * 将数据保存入ExamStationStatus
     * @param $exam
     * @param array $attributes
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-14 15：35
     */
    public function stationStatus($exam)
    {
        $examStationStatus = ExamStationStatus::where('exam_id', $exam->id)->get();

        if (!$examStationStatus->isEmpty()) {
            ExamStationStatus::where('exam_id', $exam->id)->delete();
        }

        $attributes = $this->getDraft($exam)->toArray();
        foreach ($attributes as $attribute) {
            $attribute['status'] = 0;
            if (!ExamStationStatus::create($attribute)) {
                throw new \Exception('保存数据失败！');
            };
        }

        return true;
    }

    /**
     * 将数据保存在order表中
     * @param $exam
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-14 16:10
     */
    public function saveStudentOrder($exam)
    {
        //$planList = ExamOrder::where('exam_id', '=', $exam->id)->orderBy('begin_dt', 'asc')->get();
        $planList = ExamPlan::where('exam_id', '=', $exam->id)->orderBy('begin_dt', 'asc')->get();

        $studentOrderData = [];
        if (ExamOrder::where('exam_id', '=', $exam->id)->delete() === false) {
            throw new \Exception('弃用旧安排失败');
        }
        try {
            foreach ($planList as $plan) {
                if (!array_key_exists($plan->student_id, $studentOrderData)) {
                    $studentOrderData[$plan->exam_screening_id][$plan->student_id] = [
                        'exam_id' => $exam->id,
                        'exam_screening_id' => $plan->exam_screening_id,
                        'student_id' => $plan->student_id,
                        'begin_dt' => $plan->begin_dt,
                        'status' => 0,
                        'created_user_id' => \Auth::id(),
                    ];
                }

            }

            foreach ($studentOrderData as $stduentOrder) {
                foreach ($stduentOrder as $value){
                    if (!ExamOrder::create($value)) {
                        throw new \Exception('保存学生考试顺序失败');
                    }
                }


            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}