<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:02
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\Drawlots\Validator\CheckTraits;
use Modules\Osce\Entities\Drawlots\Validator\EndExam;
use Modules\Osce\Entities\Drawlots\Validator\GoWrong;
use Modules\Osce\Entities\Drawlots\Validator\InExaminee;
use Modules\Osce\Entities\Drawlots\Validator\NotEndPrepare;
use Modules\Osce\Repositories\Common;

class HuaxiDrawlotsRepository extends AbstractDrawlots
{
    use CheckTraits;

    public function __construct()
    {
        try {
            parent::__construct();

            \App::bind('StudentInterface', function () {
                return new Student();
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

            $this->student = $this->studentObj->getStudent($this->params['uid']);

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
            Common::valueIsNull($this->student, -2, '当前学生信息错误');

            //如果该学生已经抽签了，就直接返回实例
            $obj = $this->draw->isDraw($this->student->student_id);
            if (!is_null($obj)) {
                return $this->draw->assembly($obj->station->name);
            }

            //获取当前的screen
            $screen = $this->screen->screening($this->params['exam_id']);
            Common::valueIsNull($screen, -3, '数据错误');

            //验证
            $this->process($this->student->student_id, $this->params['exam_id'], $screen->id, $this->params['room_id']);

            //获取当前考场下有多少个考站
            $stations = $this->station->site($this->params['exam_id'], $this->params['room_id'], $screen->id);
            Common::objIsEmpty($stations, -4, '数据错误');

            //获取当前能去的考场
            $accessStations = $this->station->accessStation($stations->pluck('station_id'), $screen->id,
                $this->params['room_id']);
            Common::objIsEmpty($accessStations, -5, '数据错误');

            //获取对象模型
            $obj = $this->draw->getObj($this->student->student_id, $screen->id);
            Common::valueIsNull($obj, -6, '数据错误');
            $this->fieldValidator($obj);

            //获取随机的stationId
            $this->draw->ramdonId($accessStations);

            //将数据写入数据表
            $this->draw->writeExamQueue($obj);

            //处理队列表的时间
            $this->draw->judgeTime($this->student->student_id, $screen);
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
}