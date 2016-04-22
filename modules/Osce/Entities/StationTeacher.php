<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12 0012
 * Time: 14:52
 */

namespace Modules\Osce\Entities;

use Auth;
use DB;

class StationTeacher extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'user_id', 'case_id', 'created_user_id', 'type', 'exam_id'];

    public function station()
    {
        return $this->belongsTo('\Modules\Osce\Entities\Station', 'station_id', 'id');
    }


    //根据老师的 用户id，查询对对应的考试ID集
    public function getExamToUser($user_id)
    {
        return $this->where('user_id', $user_id)->select(['exam_id'])->groupBy('exam_id')->get();
    }

    /**
     * TODO: Zhoufuxiang 2016-3-9
     * 获取摄像头信息
     */
    public function getVcrInfo($exam_id, $teacher_id, $room_id)
    {
        try {
            $data = $this->select(['vcr.id', 'vcr.name', 'vcr.ip', 'vcr.status', 'vcr.port', 'vcr.realport', 'vcr.channel', 'vcr.username', 'vcr.password'])
                ->leftJoin('room_station', 'room_station.station_id', '=', $this->table . '.station_id')
                ->leftJoin('station_vcr', 'station_vcr.station_id', '=', $this->table . '.station_id')
                ->leftJoin('vcr', 'vcr.id', '=', 'station_vcr.vcr_id')
                ->where('room_station.room_id', $room_id)
                ->where($this->table . '.user_id', $teacher_id)
                ->where($this->table . '.exam_id', $exam_id)
                ->get();

            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    //保存考官安排

    public function getsaveteacher($teacherData, $exam_id)
    {

        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //判断是新增还是编辑
            $examTeacherData = $this->where('exam_id', '=', $exam_id)->get();

            if (count($examTeacherData) != 0) {
                //这里是编辑先删除以前的数据
                foreach ($examTeacherData as $item) {
                    if (!$item->delete()) {
                        throw new \Exception('删除旧数据失败！');
                    }
                }
            }
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('未找到当前操作人信息！');
            }
            $teacherModel = new Teacher();
            if ($teacherData) {
                foreach ($teacherData as $key => $item) {
                    $stationType = Station::find($item['station_id']);
                    if ($stationType->type != 2) {
                        if ($item['teacher'] == "" || $item['teacher'] == "") {

                            throw new \Exception('还有考试没有安排考官，请安排！！重试！！');
                        }
                        if ($item['teacher'] == null || $item['teacher'] == null) {
                            throw new \Exception('还有考试没有安排考官，请安排！！重试！！');
                        }
                    } else {
                        if ($item['teacher'] == "" && $item['sp_teacher'] == "" || $item['teacher'] == "" || $item['sp_teacher'] == "") {
                            throw new \Exception('还有考试没有安排考官，请安排！！重试！！');
                        }
                        if ($item['teacher'] == null && $item['sp_teacher'] == null || $item['teacher'] == null || $item['sp_teacher'] == null) {
                            throw new \Exception('还有考试没有安排考官，请安排！！重试！！');
                        }
                    }
                    $teacherIDs = [];
                    $subjectId = [];
                    if (!empty($item['teacher'])) {
                        foreach ($item['teacher'] as $value) {

                            //判该老师是否支持该项目如果不支持添加
                            $subjectId = TeacherSubject::where('teacher_id', '=', $value)->get()->pluck('subject_id')->toArray();
                            $subjectId [] = intval($item['subject_id']);
                            $subjectId = array_unique($subjectId);
                            $teacherModel->handleTeacherSubject($subjectId, $value, $user->id);
                            $teacherIDs[] = $value;
                        }
                    }
                    if (!empty($item['sp_teacher'])) {
                        foreach ($item['sp_teacher'] as $value) {
                            //判该老师是否支持该项目如果不支持添加
                            $subjectId = TeacherSubject::where('teacher_id', '=', $value)->get()->pluck('subject_id')->toArray();
                            $subjectId [] = intval($item['subject_id']);
                            $subjectId = array_unique($subjectId);
                            $teacherModel->handleTeacherSubject($subjectId, $value, $user->id);
                            $teacherIDs[] = $value;
                        }
                    }
////                    根据科目id，获取对应的病例id
                    $stationCase = SubjectCases::where('subject_id', $item['subject_id'])->first();
                    if (is_null($stationCase)) {
                        $case_id = NULL;
                    } else {
                        $case_id = $stationCase->case_id;
                    }
                    foreach ($teacherIDs as $teacherID) {
                        //考站-老师关系表 数据
                        $stationTeacher = [
                            'station_id' => $item['station_id'],
                            'user_id' => $teacherID,
                            'case_id' => $case_id,
                            'exam_id' => $exam_id,
                            'created_user_id' => $user->id,
//                            'type'              =>  empty($item['teacher_id']) ? 2 : 1
                        ];
                        if (!$StationTeachers = StationTeacher::create($stationTeacher)) {
                            throw new \Exception('考站-老师关系添加失败！');
                        }
                    }
                }
                $connection->commit();
                return true;
            }
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }


    public function getTeacherData($stationId, $exam_id)
    {
        $data = $this->leftJoin('teacher', 'teacher.id', '=', $this->table . '.user_id')
            ->leftJoin('teacher_subject', 'teacher_subject.teacher_id', '=', $this->table . '.user_id')
            ->whereIn('station_teacher.station_id', $stationId)
            ->where('station_teacher.exam_id', '=', $exam_id)
            ->select([
                'teacher.id as teacher_id',
                'teacher.name as teacher_name',
                'teacher.type as teacher_type',
                'teacher_subject.subject_id as subject_id',
                $this->table . '.station_id',
                $this->table . '.id',

            ])
//            ->groupBy('teacher.id')
            ->get();

        return $data;
    }
}