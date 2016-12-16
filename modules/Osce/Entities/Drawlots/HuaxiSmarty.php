<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:50
 */

namespace Modules\Osce\Entities\Drawlots;


use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\Student;
use Modules\Osce\Repositories\Common;

class HuaxiSmarty
{
    private $stationId;

    /**
     * 随机抽签给学生
     * @access public
     * @param $stations
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function ramdonId($stations)
    {
        if ($stations->isEmpty()) {
            throw new \Exception('当前数据有问题', -60);
        }

        return $this->stationId = $stations->random();
    }


    /**
     * 拼装返回成功字符串
     * @access public
     * @param $name
     * @return string
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function assembly($name)
    {
        return ['请到' . $name . '考站进行考试'];
    }

    /**
     * 检查是否抽签
     * @access public
     * @param $studentId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function isDraw($studentId)
    {
        return ExamQueue::whereStudentId($studentId)
            ->whereIn('status', [1, 2])
            ->orderBy('begin_dt', 'asc')
            ->first();
    }

    /**
     * 获取学生在数据库里的实例
     * @access public
     * @param $studentId
     * @param $screenId
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-02
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getObj($studentId, $screenId, $roomId)
    {
        return ExamQueue::whereStudentId($studentId)
            ->where('room_id', $roomId)
            ->whereExamScreeningId($screenId)
            ->orderBy('begin_dt', 'asc')
            ->first();
    }

    /**
     * 推送学生的数据
     * @access public
     * @param Student $student
     * @param array $params
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function pushStudent($student, array $params)
    {
        $exam = Exam::doingExam($params['exam_id']); 
        
        $studentData = $student->studentList($params['station_id'], $exam, $params['student_id']);

        if ($studentData['nextTester']) {
            $studentData['nextTester']->avator = asset($studentData['nextTester']->avator);

            return $studentData['nextTester'];
        } else {
            throw new \Exception('当前没有学生');
        }
    }

    /**
     * 获取推送异常考试学生
     * @access public
     * @param Student $student
     * @param array $params
     * @return mixed
     * @throws \Exception
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function  AbnormalStudent ($student, array $params){
        
     $studentData = $student->AbnormalStudent($params['exam_id'], $params['room_id'],$params['screenId']);
     return $studentData ;
 }


    

    /**
     *从缓存中获取当前组
     * @access public
     * @param Student $student
     * @param array $params
     * @return object
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public  function Examinee($examId,$roomId){
        //从缓存中 获取当前组考生队列
        $key = 'current_room_id' . $roomId .'_exam_id'.$examId;
        //从缓存中取出 当前组考生队列
        $examQueue = \Cache::get($key);
        \Log::debug('检查当前组有没有异常考生', [$examId, $roomId, $key, $examQueue]);
        if(empty($examQueue) ||count($examQueue) == 0){
            Common::updateRoomCache($examId, $roomId);
        }
        //从缓存中再取一次 当前组考生队列
        $examQueue = \Cache::get($key);
        //检查是否有异常考生
        $AbnormalStudent = [];
        if(!empty($examQueue) || count($examQueue)>0){
            foreach ($examQueue as $item){
                if($item->controlMark == 1 ||$item->controlMark ==2 || $item->controlMark ==3){
                    $AbnormalStudent[] = $item ;
                }
            }
        }
        return $AbnormalStudent;
    }

    /**
     *从缓存中获取下一组
     * @access public
     * @param Student $student
     * @param array $params
     * @return object
     * @throws \Exception
     * @version 3.6
     * @author zhouqiang <JiangZhiheng@misrobot.com>
     * @time 2016-05-07
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */

    public function NextExaminee($examId ,$roomId){
        $abnormal = 1;
        //从缓存中 获取当前组考生队列
        $key = 'next_room_id' . $roomId .'_exam_id'.$examId;
        //从缓存中取出 当前组考生队列
        $NextExamQueue = \Cache::get($key);
        //检查是否有异常考生
        if(!empty($NextExamQueue)){
            foreach ($NextExamQueue as $item){
                //如果有一个学生不是异常就跳出 反之就把这一组学生队列全部结束
                if($item->controlMark == -1){
                    $abnormal = 2;
                    break;
                }
            }
            //结束下一组所有队列
            if($abnormal ==1){
                foreach ($NextExamQueue as $value){
                   $Queue =  $this->getObj($value->student_id, $value->exam_screening_id, $roomId);
                    $Queue->status =3;
                    if($Queue->save()){
                       //创建成绩

                    }
                }
            }
        }

        return $NextExamQueue;
    }






}