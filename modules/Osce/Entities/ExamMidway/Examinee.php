<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/22
 * Time: 14:38
 */

namespace Modules\Osce\Entities\ExamMidway;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Repositories\Common;

class Examinee
{
    private $params;

    private $exam;

    private $mode;

    /**
     * Drawlots constructor.
     * @param $exam
     * @param array $params
     */
    public function __construct($exam, array $params)
    {
        $this->exam = $exam;
        $this->params = $params;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * 获取到当前组学生
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-22 18:39
     */
    public function examinee()
    {
        try {
            //获取到对象实例
            $examFlowStation = $this->mode->getFlow();
            $serialnumber = array_unique($examFlowStation->pluck('serialnumber')->toArray());

            //直接仍回学生实例
            $students = $this->mode->getExaminee($serialnumber);

            //将图片地址加上域名
            foreach ($students as &$student) {
                $student->student_avator = asset($student->student_avator);
            }


            return $students;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取下一组考生
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-23 10:32
     */
    public function nextExaminee()
    {
        try {
            //获取到对象实例
            $examFlowStation = $this->mode->getFlow();

            $serialnumber = array_unique($examFlowStation->pluck('serialnumber')->toArray());

            //直接仍回学生实例
            $students = $this->mode->getNextExaminee($serialnumber);

            //将图片地址加上域名
            foreach ($students as &$student) {
                $student->avator = asset($student->avator);
            }

            return $students;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 获取当前考试实体的信息
     * @author Jiangzhiheng
     * @time 2016-03-23 18:21
     */
    public function getStation()
    {
        $stationTeacher = StationTeacher::where('user_id', $this->params['id'])
            ->where('exam_id', $this->exam->id)->get();
        try {
            switch ($stationTeacher->count()) {
                case 1: //说明要给出考场-考站
                    $roomStation = RoomStation::where('station_id', $stationTeacher->first()->station_id)
                        ->first();
                    Common::valueIsNull($roomStation, -1);
                    $room = $roomStation->room;

                    $station = $roomStation->station;

                    $station->name = $room->name . '-' . $station->name;

                    //将考场的id封装进去
                    $station->room_id = $room->id;

                    //将考试的id封装进去
                    $station->exam_id = $this->exam->id;

                    //将当前的服务器时间返回
                    $station->service_time = time() * 1000;

                    return $station;
                    break;
                case 0: //报错
                    throw new \Exception('数据错误，请重试', -1);
                    break;
                default: //说明只需要给出考场
                    $roomStation = RoomStation::where('station_id', $stationTeacher->first()->station_id)
                        ->first();
                    $room = $roomStation->room;

                    //将考场的id封装进去
                    $room->room_id = $room->id;

                    //将考试的id封装进去
                    $room->exam_id = $this->exam->id;

                    //将当前的服务器时间返回
                    $room->service_time = time() * 1000;
                    return $room;
                    break;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}




