<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 14:30
 */

namespace Modules\Osce\Entities;

use Modules\Msc\Entities\Teacher;
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


    /**
     * 以考场的为中心的保存
     * @param $exam_id
     * @param $roomData
     * @param $stationData
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     */
    public function saveExamroomAssignmen($exam_id, $roomData, $stationData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息！');
            }

            if ($roomData !== null) {
            //保存考场安排数据
                foreach($roomData as $key => $value){
                    foreach($value as $room_id){
                        //考试房间关系表 数据
                        $examRoom = [
                            'exam_id'           =>  $exam_id,
                            'room_id'           =>  $room_id,
                            'create_user_id'    =>  $user ->id
                        ];
                        if(!$test1 = ExamRoom::create($examRoom)){
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

                        if(!$test2 = ExamFlow::create($examFlow)){
                            throw new \Exception('考试流程关联添加失败！');
                        }

                        //考试流程-房间关系表 数据
                        $examFlowRoom = [
                            'serialnumber'      =>  $key,
                            'room_id'           =>  $room_id,
                            'flow_id'           =>  $result->id,
                            'created_user_id'   =>  $user ->id,
                            'exam_id'           =>  $exam_id
                        ];
                        if(!$test3 = ExamFlowRoom::create($examFlowRoom)){
                            throw new \Exception('考试流程-房间关系添加失败！');
                        }
                    }
                }
            }

            //保存  考站监考老师、sp老师安排数据
            if ($stationData) {
                foreach ($stationData as $key => $item) {

                    $teacherIDs = [];
                    //拼装一下老师的数据
                    if (($item['teacher_id']) !== "") {
                        $teacherIDs[] =  $item['teacher_id'];
                    } else {
                        $teacherIDs[] = NULL;
                    }

                    if (!empty($item['spteacher_id'])) {
                        foreach ($item['spteacher_id'] as $value) {
                            $teacherIDs[] = $value;
                        }
                    } else {
                        $teacherIDs[] = NULL;
                    }
                    $station_id = $item['id'];
                    //根据考站id，获取对应的病例id
                    $stationCase = StationCase::where('station_id', $station_id)->first();
                    if(!is_null($stationCase))
                    {
                        $station    =   $stationCase->station;
                        if(is_null($station))
                        {
                            throw new \Exception('找不到考站');
                        }
                        if(empty($stationCase)&&$station->type!=3){
                            throw new \Exception('找不到考站对应的病例对象');
                        }
                    }
                    else
                    {
                        $station    =   Station::find($station_id);
                        if(is_null($station))
                        {
                            throw new \Exception('找不到考站');
                        }

                        if($station->type!=3){
                            throw new \Exception('找不到考站对应的病例对象');
                        }
                    }
                    $case_id = is_null($stationCase)? null:$stationCase->case_id;
                    foreach ($teacherIDs as $teacherID) {
                        //考站-老师关系表 数据
                        $stationTeacher = [
                            'station_id'        =>  $station_id,
                            'user_id'           =>  $teacherID,
                            'case_id'           =>  $case_id,
                            'exam_id'           =>  $exam_id,
                            'created_user_id'   =>  $user ->id,
                            'type'              =>  empty($item['teacher_id']) ? 2 : 1
                        ];
                        if(!$StationTeachers = StationTeacher::create($stationTeacher)) {
                            throw new \Exception('考站-老师关系添加失败！');
                        }
                    }

                    //考试-试卷-考站关联  TODO: Zhoufuxiang 2016-3-22
                    $station = Station::where('id', '=', $station_id)->select(['name', 'type', 'paper_id'])->first();
                    if(!$station){
                        throw new \Exception('未找到对应考站');
                    }
                    if($station->type == 3){
                        if(!isset($station->paper_id) || empty($station->paper_id)){
                            throw new \Exception($station->name.' (考站) 没有关联考卷!');
                        }
                        $examPaperStation = [
                            'exam_id'       => $exam_id,
                            'exam_paper_id' => $station->paper_id,
                            'station_id'    => $station_id,
                        ];
                        //插入exam_paper_exam_station表
                        if(!$examPaperStation = ExamPaperStation::create($examPaperStation)){
                            throw new \Exception('考试-试卷-考站关联添加失败');
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

    /**
     * 以考场为中心的保存
     * @param $exam_id
     * @param $roomData
     * @param $stationData
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     */
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

            $id = $exam_id;
            $flowIds = ExamFlow::where('exam_id',$id)->select('flow_id')->get(); //获得流程的id
            $examScreening = ExamScreening::where('exam_id',$id);

            //删除考试考场学生表
            foreach ($examScreening->select('id')->get() as $item) {
                if (count(ExamScreeningStudent::where('exam_screening_id',$item->id)->get()) != 0) {
                    if (!ExamScreeningStudent::where('exam_screening_id',$item->id)->delete()) {
                        throw new \Exception('删除考试考场学生关系表失败，请重试！');
                    }
                }
            }

            //删除考试考场关联
            if (count(ExamRoom::where('exam_id',$id)->first()) != 0) {
                if (!ExamRoom::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考场关联失败，请重试！');
                }
            }

            //删除考试流程关联
            if (count(ExamFlow::where('exam_id',$id)->first()) != 0) {
                if (!ExamFlow::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试流程关联失败，请重试！');
                }
            }

            //删除考试考场流程关联
            if (count(ExamFlowRoom::where('exam_id',$id)->first()) != 0) {

                if (!ExamFlowRoom::where('exam_id',$id)->delete()) {
                    throw new \Exception('删除考试考场流程关联失败，请重试！');
                }
            }

            //如果有flow的话，就删除
            if (!$flowIds->isEmpty()) {
                foreach ($flowIds as $flowId) {
                    if (!Flows::where('id',$flowId->flow_id)->delete()) {
                        throw new \Exception('删除流程失败，请重试！');
                    }
                }
            }

            //删除stationTeacher表
            if (!StationTeacher::where('exam_id',$id)->get()->isEmpty()) {
                if (!StationTeacher::where('exam_id',$id)->delete()) {
//                    dd(123);
                    throw new \Exception('删除教师考站关联失败，请重试！');
                }
            }

            //删除排考记录表
            ExamPlanRecord::deleteRecord($exam_id);

            //删除考试-试卷-考站关联  TODO: Zhoufuxiang 2016-3-22
            if(!ExamPaperStation::where('exam_id', '=', $id)->get()->isEmpty()){
                if(!ExamPaperStation::where('exam_id', '=', $id)->delete()){
                    throw new \Exception('删除考试-试卷-考站关联失败，请重试！');
                }
            }

            //保存新的数据
            $this->saveExamroomAssignmen($exam_id,$roomData,$stationData);

            $connection->commit();
            return true;
        } catch (\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

}