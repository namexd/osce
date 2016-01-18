<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/14
 * Time: 11:04
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Exception;
class ExamFlowStation extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_flow_station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['serialnumber', 'station_id', 'flow_id', 'created_user_id'];

    public function queueStation(){
        return $this->hasMany('\Modules\Osce\Entities\ExamQueue','station_id','station_id');
    }

    /**
     * postStationAssignment的新增方法
     * @param $examId
     * @param array $formData
     * @return bool
     * @throws Exception
     */
    public function createExamAssignment($examId, array $formData = [])
    {
        //将数据插入各表，使用事务
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            $user = Auth::user();
            if ($user->isEmpty()) {
                throw new Exception('未找到当前操作人信息！');
            }

            //查询考试名
            $exam = Exam::findOrFail($examId)->first();
            foreach ($formData as $key => $item) {
                foreach ($item as $value) {
                    //根据station_id查对应的名字
                    $station = Station::findOrFail($value['station_id'])->first();
                    //为流程表准备数据
                    $flowsData = [
                        'name' => $exam->name . '-' . $station->name,
                        'created_user_id' => $user->id
                    ];

                    //将数据插入Flows站
                    if (!$flowsResult = Flows::create($flowsData)) {
                        throw new Exception('流程添加失败');
                    }
                    $flowsId = $flowsResult -> id;

                    //将数据插入到各个关联表中
                    $this->examStationAssociationSave($examId, $flowsId, $user, $key, $value);

                    //准备数据，插入station_teacher表
                    $this->stationTeacherAssociation($examId, $value, $user);

                }
            }

            $connection->commit();
            return true;
        } catch (Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * ExamAssignment的修改方法
     * @param $examId
     * @param array $formData
     * @return bool
     * @throws Exception
     */
    public function updateExamAssignment($examId, array $formData = [])
    {
        try {
            //使用事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            //查询操作者id
            $user = Auth::user();
            if ($user->isEmpty()) {
                throw new Exception('未找到当前操作人信息！');
            }

            //查询考试名
            $exam = Exam::findOrFail($examId)->first();

            $id = $examId;
            $this->examStationDelete($id);

            foreach ($formData as $key => $item) {
                foreach ($item as $value) {
                    //删除stationTeacher表
                    if (count(StationTeacher::where('station_id',$value['station_id'])) != 0) {
                        if (StationTeacher::where('station_id',$value['station_id'])->delete()) {
                            throw new \Exception('删除考站老师失败，请重试！');
                        }
                    }
                    //根据station_id查对应的名字
                    $station = Station::findOrFail($value['station_id'])->first();
                    //为流程表准备数据
                    $flowsData = [
                        'name' => $exam->name . '-' . $station->name,
                        'created_user_id' => $user->id
                    ];

                    //将数据插入Flows表
                    if (!$flowsResult = Flows::create($flowsData)) {
                        throw new Exception('流程添加失败');
                    }
                    $flowsId = $flowsResult -> id;

                    //将数据插入到各个关联表中
                    $this->examStationAssociationSave($examId, $flowsId, $user, $key, $value);

                    //准备数据，插入station_teacher表
                    $this->stationTeacherAssociation($examId, $value, $user);
                }
            }

            $connection->commit();
            return true;
        } catch (Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * @param $examId
     * @param $flowsId
     * @param $user
     * @param $key
     * @param $value
     * @throws Exception
     */
    protected function examStationAssociationSave($examId, $flowsId, $user, $key, $value)
    {
        try {
        //配置数据准备插入exam_flows表
        $examFlowsData = [
            'exam_id' => $examId,
            'flow_id' => $flowsId,
            'created_user_id' => $user->id,
        ];

        //将数据插入exam_flows
        if (!$examFlowsResult = ExamFlow::create($examFlowsData)) {
            throw new Exception('考试流程关联添加失败');
        }

        //配置exam_flow_station的数据
        $examFlowsStationData = [
            'serialnumber' => $key,
            'station_id' => $value['station_id'],
            'flow_id' => $flowsId,
            'exam_id' => $examId,
            'created_user_id' => $user->id,
        ];

        //插入exam_flow_station表
        if (!$examFlowsStationResult = ExamFlowStation::create($examFlowsStationData)) {
            throw new Exception('考试流程考站关联添加失败');
        }
    } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $examId
     * @param $value
     * @param $user
     * @return mixed
     * @throws Exception
     */
    protected function stationTeacherAssociation($examId, $value, $user)
    {
        try {
        //先拼装teacher的数据
        $teacherIDs = [];
        if (isset($value['teacher_id'])) {
            $teacherIDs[] = $value['teacher_id'];
        }
        if (isset($value['spteacher_id'])) {
            foreach ($value['spteacher_id'] as $spTeacherId) {
                $teacherIDs[] = $spTeacherId;
            }
        }

        //循环，将老师ID放入station_teacher表的数据
        foreach ($teacherIDs as $teacherID) {
            $stationTeacherData = [
                'station_id' => $value['station_id'],
                'user_id' => $teacherID,
                'case_id' => StationCase::where('station_id', $value['station_id'])->first()->case_id,
                'exam_id' => $examId,
                'created_user_id' => $user->id,
                'type' => empty($value['teacher_id']) ? 2 : 1
            ];

            if (!$StationTeachers = StationTeacher::create($stationTeacherData)) {
                throw new \Exception('考站-老师关系添加失败！');
            }
        }
    } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    protected function examStationDelete($id)
    {
        try {
            //删除考试考场关联
            if (count(ExamRoom::where('exam_id', $id)->first()) != 0) {
                if (!ExamRoom::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }


            //删除考试流程关联
            if (count(ExamFlow::where('exam_id', $id)->first()) != 0) {
                if (!$examFlows = ExamFlow::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试流程关联失败，请重试！');
                }
                $flowsId = $examFlows->flow_id;
            }

            //删除考试考场流程关联
            if (count(ExamFlowStation::where('exam_id', $id)->first()) != 0) {
                if (!ExamFlowStation::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试考场流程关联失败，请重试！');
                }
            }

            //删除flows表
            if (count(Flows::where('id', $flowsId)->first()) != 0) {
                if (!Flows::where('id', $flowsId)->delete()) {
                    throw new \Exception('删除流程失败，请重试！');
                }
            }
        } catch (Exception $ex) {
            throw $ex;
          }
        }
}