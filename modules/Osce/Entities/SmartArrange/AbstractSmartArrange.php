<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 11:26
 */

namespace Modules\Osce\Entities\SmartArrange;

use Modules\Osce\Entities\SmartArrange\Export\DengStudent;
use Modules\Osce\Entities\SmartArrange\Traits\CheckTraitsForHuaxi;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Modules\Osce\Entities\SmartArrange\Traits\CheckTraits;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamStationStatus;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamOrder;


abstract class AbstractSmartArrange implements SmartArrangeInterface
{
    use CheckTraitsForHuaxi, SQLTraits;

    protected $model;

//    public function __construct()
//    {
//        $this->makeModel();
//    }

    public function makeModel()
    {
        $model = \App::make($this->model());

        return $this->model = $model;
    }

    /**
     * 更改effect
     * @param $exam
     * @param array $attributes
     * @author ZouYuChao
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
     * @author ZouYuChao
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
     * @author ZouYuChao
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

    /**
     * 新的保存order数据
     * @access public
     * @param $exam
     * @return mixed
     * @throws \Exception
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-10
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function newSaveStudentOrder($exam)
    {
        /*
         * 获取列表
         */
        $dengStudent = new DengStudent(new ExamPlan());
        $planList = $dengStudent->getData($exam->id);
        try {
            //删除exam_order表
            if (ExamOrder::where('exam_id', '=', $exam->id)->delete() === false) {
                throw new \Exception('弃用旧安排失败');
            }

            //处理数据
            $studentOrderData = [];
            foreach ($planList as $item) { //item是每一个场次
                $tempArray = [];
                //在场次里循环遍历，将每一个学生的第一次出现写入
                foreach ($item as $value) {
                    //如果学生已经出现过了，continue
                    if (array_key_exists($value->student_id, $tempArray)) {
                        continue;
                    }

                    //如果不是，存入表中
                    $tempArray[$value->student_id] = [
                        'exam_id' => $exam->id,
                        'exam_screening_id' => $value->exam_screening_id,
                        'student_id' => $value->student_id,
                        'begin_dt' => $value->begin_dt,
                        'status' => 0,
                        'created_user_id' => \Auth::id(),
                    ];
                }

                //合并数组
                $studentOrderData = array_merge($studentOrderData, $tempArray);
            }

            //将数据写入数据表中

            return ExamOrder::insert($studentOrderData);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}