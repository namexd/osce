<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:02
 */

namespace Modules\Osce\Entities\Drawlots;



use Modules\Osce\Entities\Drawlots\Validator\EndExam;
use Modules\Osce\Entities\Drawlots\Validator\GoWrong;
use Modules\Osce\Entities\Drawlots\Validator\InExaminee;
use Modules\Osce\Entities\Drawlots\Validator\NotEndPrepare;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Drawlots\Student as StudentObj;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Repositories\WatchReminderRepositories;

class DrawlotsRepository extends AbstractDrawlots
{
    protected $draw = null;

    protected $student = null;

    protected $studentObj = null;

    protected $station = null;

    protected $stationId = null;

    protected $screen = null;

    protected $validator = null;

    protected $roomId = null;
    
    public function __construct()
    {
        try {
            \App::bind('DrawInterface', function () {
                return new HuaxiSmarty();
            });

            $this->draw = \App::make('DrawInterface');
//            $this->draw = $draw;

            \App::bind('StudentInterface', function () {
                return new StudentObj();
            });
            \App::bind('StationData', function () {
                return new Station();
            });
            \App::bind('Screening', function () {
                return new Screening();
            });

            $this->studentObj = \App::make('StudentInterface');
            $this->station = \App::make('StationData');
            $this->screen = \App::make('Screening');
//            $this->studentObj = $student;
//            $this->station = $stationData;
//            $this->screen = $screening;

            \App::bind('GoWrong', function () {
                return new GoWrong();
            });

            \App::bind('EndExam', function () {
                return new EndExam();
            });

            \App::bind('NotEndPrepare', function () {
                return new NotEndPrepare(\App::make('StationData'));
            });

            \App::bind('InExaminee', function () {
                return new InExaminee(\App::make('StationData'));
            });

            $this->validator = [
                \App::make('GoWrong'),
                \App::make('EndExam'),
                \App::make('NotEndPrepare'),
                \App::make('InExaminee')
            ];

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function setValidator(array $validator)
    {
        $this->validator = $validator;
    }

    /**
     * 将学生进行分配
     * @access public
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function distribute()
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //获取当前的screen
            $screen = $this->screen->screening($this->params['exam_id']);
            Common::valueIsNull($screen, -3, '获取场次失败');

            $this->student = $this->studentObj->getStudent($screen->id, $this->params['uid']);
            Common::valueIsNull($this->student, -2, '当前学生信息错误');

            //如果该学生已经抽签了，就直接返回实例
            $obj = $this->draw->isDraw($this->student->student_id);
            if (!is_null($obj)) {
                $this->stationId = $obj->station_id;
                return $this->draw->assembly($obj->station->name);
            }



            //验证
            $this->process($this->student->student_id, $this->params['exam_id'], $screen->id, $this->params['room_id']);

            //获取当前考场下有多少个考站
            $stations = $this->station->site($this->params['exam_id'], $this->params['room_id'], $screen->id);
            Common::objIsEmpty($stations, -4, '获取考站失败');

            //获取当前能去的考场
            $accessStations = $this->station->accessStation($stations->pluck('station_id'), $screen->id,
                $this->params['room_id']);
            Common::objIsEmpty($accessStations, -5, '获取能去的考站失败');

            //获取对象模型
            $obj = $this->draw->getObj($this->student->student_id, $screen->id, $this->params['room_id']);
            Common::valueIsNull($obj, -6, '获取当前用户失败');
            $this->fieldValidator($obj);

            //获取随机的stationId
            $this->stationId = $this->draw->ramdonId($accessStations);
            //将数据写入数据表
            $this->writeExamQueue($obj, $this->stationId);

            //处理队列表的时间
            $this->judgeTime($this->student->student_id, $screen);
            $connection->commit();
            return $this->draw->assembly($obj->station->name);
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 验证方法
     * @access public
     * @param $examId
     * @param $screenId
     * @param $roomId
     * @return bool
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function process($studentId, $examId, $screenId, $roomId)
    {
        foreach ($this->validator as $item) {
            $item->validate($studentId, $screenId, $roomId, $examId);
        }

        return true;
    }

    /**
     * 获取考场下有多少考站
     * @access public
     * @param $examId
     * @param $roomId
     * @param $screenId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStationNum($examId, $roomId, $screenId)
    {
        return $this->station->site($examId, $roomId, $screenId);
    }

    /**
     * 获取当前的screen
     * @access public
     * @param $examId
     * @请求字段：
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-13
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getScreening($examId)
    {
        return $this->screen->screening($examId);
    }

    /**
     * 获取推送的学生
     * @access public
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function pushStudent()
    {
        $params['exam_id'] = $this->params['exam_id'];
        $params['station_id'] = $this->stationId;
        $params['student_id'] = $this->student->student_id;
        return $this->draw->pushStudent(new Student(), $params);
//        if ($this->student) {
//            $this->student->avator = asset($this->student->avator);
//        }
//        return $this->student;
    }

    public function getParams()
    {
        return [
            'student_id' => $this->student->student_id,
            'station_id' => $this->stationId,
            'room_id' => $this->params['room_id']
        ];
    }

    /**
     * 获取已经抽签的考生
     * @access public
     * @param $examId
     * @param $stationId
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-14
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDrawlotsQueue($examId, $stationId)
    {
        try {
            //获取队列id
            $queue = $this->station->getDrawlots($examId, $stationId);

            //通过队列里的学生id，返回值
            if (!is_null($queue)) {
                //拼凑参数
                $params['exam_id'] = $examId;
                $params['station_id'] = $stationId;
                $params['student_id'] = $queue->student_id;
                return $this->draw->pushStudent(new Student(), $params);
            } else {
                throw new \Exception('当前考站没有人抽签', -555);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}