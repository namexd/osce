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
     * @param $station
     * @param $screen
     * @return mixed
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
                    ->where('station', '=', $entity->id)
                    ->groupBy('student_id')
                    ->get();
            } else {
                throw new \Exception('未定义的考试模式！');
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}