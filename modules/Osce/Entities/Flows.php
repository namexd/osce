<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:30
 */

namespace Modules\Osce\Entities;

use Modules\Osce\Entities\CommonModel;
//use Overtrue\Wechat\Auth;
use Auth;
use DB;

class Flows extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'flows';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=   ['name','description','created_user_id'];

    /**
     * 考试-流程节点-房间的关系
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function examFlowRoomRelation(){
        return $this->hasMany('\Modules\Osce\Entities\ExamFlowRoom','flow_id','id');
    }

    public function saveExamroomAssignmen($exam_id, $roomData, $stationData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息！');
            }
            //保存考场安排数据
            foreach($roomData as $key => $value){
                foreach($value as $room_id){
                    //考试房间关系表 数据
                    $examRoom = [
                        'exam_id'           =>  $exam_id,
                        'room_id'           =>  $room_id,
                        'create_user_id'    =>  $user ->id
                    ];
                    if(!$result = ExamRoom::create($examRoom)){
                        throw new \Exception('考试房间关系添加失败！');
                    }

                    $examName = Exam::where('id', $exam_id)->select(['name'])->first();     //查询考试名
                    $roomName = Room::where('id', $room_id)->select(['name'])->first();     //查询考场名
                    //考试流程表 数据
                    $flowsData = [
                        'name'              =>  $examName->name.'-'.$roomName->name,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = $this->create($flowsData)){
                        throw new \Exception('考试流程添加失败！');
                    }
                    $flowId = $result->id;

                    //考试流程关联表 数据
                    $examFlow = [
                        'exam_id'           =>  $exam_id,
                        'flow_id'           =>  $flowId,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = ExamFlow::create($examFlow)){
                        throw new \Exception('考试流程关联添加失败！');
                    }

                    //考试流程-房间关系表 数据
                    $examFlowRoom = [
                        'serialnumber'      =>  $key,
                        'room_id'           =>  $room_id,
                        'flow_id'           =>  $flowId,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = ExamFlowRoom::create($examFlowRoom)){
                        throw new \Exception('考试流程-房间关系添加失败！');
                    }
                }
            }

            //保存  考站监考老师、sp老师安排数据

            foreach ($stationData as $key => $item) {
                $examFlowStationData = [
                    'serialnumber' => $key,
                    'station_id' => $item['id'],
                    'flow_id' => $flowId,
                    'created_user_id' => $user->id
                ];
                if (!ExamFlowStation::create($examFlowStationData)) {
                    throw new \Exception('考试流程-考站关系添加失败！');
                }


                if (isset($item['spteacher_id'])) {
                    foreach ($item['spteacher_id'] as $value) {
                        //考站-老师关系表 数据
                        $stationTeacher = [
                            'station_id'        =>  $item['id'],
                            'user_id'           =>  empty($item['teacher_id'])? $value : $item['teacher_id'],
                            'case_id'           =>  StationCase::where('station_id', $item['id'])->first()->case_id,
                            'created_user_id'   =>  $user ->id,
                            'type'              =>  empty($item['teacher_id'])? 2:1
                        ];
                        if(!$result = StationTeacher::create($stationTeacher)){
                            throw new \Exception('考站-老师关系添加失败！');
                        }
                    }
                }
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    public function editExamroomAssignmen($exam_id, $roomData, $stationData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //删除旧的数据
            $examRoom = ExamRoom::where('exam_id', $exam_id)->get();
            if(count($examRoom) !=0){
                foreach ($examRoom as $item) {
                    if(!$result = ExamRoom::where('id', $item->id)->delete()){
                        throw new \Exception('考试房间关系删除失败！');
                    }
                }
            }
            $examFlow = ExamFlow::where('exam_id', $exam_id)->get();
            if(count($examFlow) !=0){
                foreach ($examFlow as  $item) {
                    if(!$result = ExamFlowRoom::where('flow_id', $item->flow_id)->delete()){
                        throw new \Exception('考试流程-房间关系删除失败！');
                    }
                    if(!$result = ExamFlow::where('id', $item->id)->delete()){
                        throw new \Exception('考试-流程关联删除失败！');
                    }
                    if(!$result = $this->where('id', $item->flow_id)->delete()){
                        throw new \Exception('考试流程删除失败！');
                    }
                }
            }

            //保存新的数据
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息！');
            }
            //保存考场安排数据
            foreach($roomData as $key => $value){
                foreach($value as $room_id){
                    //考试房间关系表 数据
                    $examRoom = [
                        'exam_id'           =>  $exam_id,
                        'room_id'           =>  $room_id,
                        'create_user_id'    =>  $user ->id
                    ];
                    if(!$result = ExamRoom::create($examRoom)){
                        throw new \Exception('考试房间关系添加失败！');
                    }

                    $examName = Exam::where('id', $exam_id)->select(['name'])->first();     //查询考试名
                    $roomName = Room::where('id', $room_id)->select(['name'])->first();     //查询考场名
                    //考试流程表 数据
                    $flowsData = [
                        'name'              =>  $examName->name.'-'.$roomName->name,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = $this->create($flowsData)){
                        throw new \Exception('考试流程添加失败！');
                    }

                    //考试流程关联表 数据
                    $examFlow = [
                        'exam_id'           =>  $exam_id,
                        'flow_id'           =>  $result->id,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = ExamFlow::create($examFlow)){
                        throw new \Exception('考试流程关联添加失败！');
                    }

                    //考试流程-房间关系表 数据
                    $examFlowRoom = [
                        'serialnumber'      =>  $key,
                        'room_id'           =>  $room_id,
                        'flow_id'           =>  $result->id,
                        'created_user_id'   =>  $user ->id
                    ];
                    if(!$result = ExamFlowRoom::create($examFlowRoom)){
                        throw new \Exception('考试流程-房间关系添加失败！');
                    }
                }
            }
            //保存  考站监考老师、sp老师安排数据
            foreach ($stationData as $item) {
                //考站-老师关系表 数据
                $stationTeacher = [
                    'station_id'        =>  $item['id'],
                    'user_id'           =>  empty($item['teacher_id'])? 3:$item['teacher_id'],
                    'case_id'           =>  StationCase::where('station_id', $item['id'])->first()->case_id,
                    'created_user_id'   =>  $user ->id,
                    'type'              =>  empty($item['teacher_id'])? 2:1
                ];
                if(!$result = StationTeacher::create($stationTeacher)){
                    throw new \Exception('考站-老师关系添加失败！');
                }
            }

            $connection->commit();
            return true;
        } catch (\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

}