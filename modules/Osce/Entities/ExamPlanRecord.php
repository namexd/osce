<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/2/15
 * Time: 17:00
 */

namespace Modules\Osce\Entities;


class ExamPlanRecord extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_plan_record';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'room_id',
        'student_id',
        'station_id',
        'exam_id',
        'exam_screening_id',
        'end_dt',
        'begin_dt',
        'serialnumber'
    ];

    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student', 'id', 'student_id');
    }

    public function station()
    {
        return $this->hasOne('\Modules\Osce\Entities\Station', 'id', 'station_id');
    }

    public function room()
    {
        return $this->hasOne('\Modules\Osce\Entities\Room', 'id', 'room_id');
    }

    /**
     * 随机模式下的看是否有人考试
     * @param $screen 当前流程实例
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-02-24 17:51
     */
    static public function randomBeginStudent($screen)
    {
        return ExamPlanRecord::where('exam_screening_id', $screen->id)
            ->whereNotNull('end_dt')
            ->groupBy('student_id')
            ->get();
    }

    /**
     * 轮询模式下看是否有人考试
     * @param $entity
     * @param $screen
     * @param $sequenceMode
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-02-24 17:53
     */
    static public function pollBeginStudent($entity, $screen, $sequenceMode)
    {
        try {
            //1是考场模式
            if ($sequenceMode == 1) {
                return ExamPlanRecord::where('exam_screening_id', $screen->id)
                    ->whereNotNull('end_dt')
                    ->where('room_id', '=', $entity->id)
                    ->groupBy('student_id')
                    ->get();
                //2是考站模式
            } elseif ($sequenceMode == 2) {
                return ExamPlanRecord::where('exam_screening_id', $screen->id)
                    ->whereNotNull('end_dt')
                    ->where('station_id', '=', $entity->id)
                    ->groupBy('student_id')
                    ->get();
            } else {
                throw new \Exception('未定义的考试模式！',-1000);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 顺序模式下是否有符合要求的学生
     * @author Jiangzhiheng
     * @time 2016-02-25 16:33
     */
    static public function orderBeginStudent($screen,$serialnumber,$sequenceMode)
    {
        try {
            $prevSerial = ExamPlanRecord::where('exam_screening_id','=',$screen->id)
                ->where('serialnumber', '=', $serialnumber)
                ->whereNotNull('end_dt')
                ->groupBy('student_id')
                ->get()
                ->pluck('student_id');

            $thisSerial = ExamPlanRecord::where('exam_screening_id', $screen->id)
                ->whereNotNull('end_dt')
                ->where('serialnumber', '=', $serialnumber-1)
                ->groupBy('student_id')
                ->get()
                ->pluck('student_id');

            //求取差集
            return array_diff($thisSerial->toArray(),$prevSerial->toArray());
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}