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
    protected $fillable = ['serialnumber', 'station_id', 'flow_id', 'created_user_id', 'exam_id'];

    public function queueStation(){
        return $this->hasMany('\Modules\Osce\Entities\ExamQueue','station_id','station_id');
    }

    public function roomStation() {
       return $this->belongsTo('\Modules\Osce\Entities\RoomStation','station_id','station_id');
    }

    public function station(){
        return $this->hasOne('\Modules\Osce\Entities\Station','id','station_id');
    }
	public function createExamAssignment($examId,array $room, array $formData = [])
    {
        //将数据插入各表，使用事务
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
//            dd($formData);
            $user = Auth::user();
            if (empty($user)) {
                throw new Exception('未找到当前操作人信息！');
            }

            //查询考试名
            $exam = Exam::findOrFail($examId)->first();

            foreach ($room as $key => $item) {
//                dd($room);
                foreach ($item as $v) {
                    //根据station_id查对应的名字
                    $station = Station::findOrFail($v)->first();
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
                    $this->examStationAssociationSave($examId, $flowsId, $user, $key, $v);
                }
            }

            foreach ($formData as $key => $value) {
                //准备数据，插入station_teacher表
                $this->stationTeacherAssociation($examId, $value, $user);
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
     * @param array $room
     * @param array $formData
     * @return bool
     * @throws Exception
     */
    public function updateExamAssignment($examId,array $room,array $formData = [])
    {
        try {
            //使用事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
            //查询操作者id
            $user = Auth::user();
            if (empty($user)) {
                throw new Exception('未找到当前操作人信息！');
            }

            //查询考试名
            $exam = Exam::findOrFail($examId)->first();

            $id = $examId;
            $this->examStationDelete($id);
            foreach ($room as $key=> $item) {
                foreach ($item as $value) {
                    //根据station_id查对应的名字
                    $station = Station::findOrFail($value)->first();
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
                }
            }

            //删除stationTeacher表
            if (!StationTeacher::where('exam_id',$examId)->get()->isEmpty()) {
                if (!StationTeacher::where('exam_id',$examId)->delete()) {
                    throw new \Exception('删除考站老师失败，请重试！');
                }
            }

            foreach ($formData as $key => $value) {
                //准备数据，插入station_teacher表
                $this->stationTeacherAssociation($examId, $value, $user);
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
            //配置数据插入exam_station表
            $examStationData = [
                'exam_id' => $examId,
                'station_id' => $value,
                'create_user_id' => $user->id
            ];
            if (ExamStation::where('exam_id',$examId)->where('station_id',$value)->get()->isEmpty()) {
                if (!ExamStation::create($examStationData)) {
                    throw new \Exception('考试考站关联添加失败');
                }
            }


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
                'station_id' => $value,
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
        if (!empty($value['teacher_id'])) {
            $teacherIDs[] = $value['teacher_id'];
        }   else {
            $teacherIDs[] = NULL;
        }


        if (!empty($value['spteacher_id'])) {
            if (is_array($value['spteacher_id'])) {
                foreach ($value['spteacher_id'] as $spTeacherId) {
                    $teacherIDs[] = $spTeacherId;
                }
            } else {
                $teacherIDs[] = $value['spteacher_id'];
            }
        } else {
            $teacherIDs[] = NULL;
        }

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
            if (!ExamStation::where('exam_id', $id)->get() -> isEmpty()) {
                if (!ExamStation::where('exam_id', $id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }


            //删除考试流程关联
            if (count($examFlows = ExamFlow::where('exam_id', $id)->first()) != 0) {
                if (!ExamFlow::where('exam_id', $id)->delete()) {
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