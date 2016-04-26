<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/30 0030
 * Time: 15:33
 */

namespace Modules\Osce\Entities;


class TeacherSubject extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'teacher_subject';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['teacher_id', 'subject_id', 'created_user_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject','id','subject_id');
    }

    public function getTeachers($type, $subject_id,$examGradationId,$examId){
        //查询出阶段下的大站id和小站里的数据拿到所有的考站id
            $data = ExamDraftFlow::leftJoin('exam_draft', 'exam_draft.exam_draft_flow_id', '=','exam_draft_flow.id')
                ->leftJoin('station_teacher', 'station_teacher.station_id', '=', 'exam_draft.station_id')
                ->where('exam_draft_flow.exam_id',$examId)
                ->where('exam_draft_flow.exam_gradation_id',$examGradationId)
                ->where('station_teacher.exam_id',$examId)
                ->select([
                    'station_teacher.user_id'
                ])
                ->get()->toArray();

        if ($subject_id == 0){

            return Teacher::where('archived','=',0)->where('type','=', $type)->select(['id as teacher_id', 'name'])->get();
        }else{

            return TeacherSubject::leftJoin('teacher', 'teacher.id', '=', 'teacher_subject.teacher_id')
                ->where('teacher_subject.subject_id', '=', $subject_id)
                ->where('teacher.type', '=', $type)
                ->select(['teacher_subject.teacher_id', 'teacher.name'])->get();
        }

    }


    /**
     * 获取当前正在考试的考试对应的所有老师考试项目关系数据
     *
     * @param $subject
     *
     * @author Zhoufuxiang  2016-04-13 10:55
     * @return mixed
     * @throws \Exception
     */
    public function getTeacherSubjects(){
        //拿到当前 正在考试的考试
        $examArray = Exam::where('status', 1)->get()->pluck('id')->toArray();
        //考试考试下面所有的老师
        $TeacherArray= StationTeacher::whereIn('exam_id' ,$examArray)->whereNotNull('user_id')->get()->pluck('user_id');
        if(!is_null($TeacherArray)){

            //拿到考试项目关联的老师
            $TeacherId = array_diff($TeacherArray->all(), [null]);
            $teacherSubjects = TeacherSubject::whereIn('teacher_id',$TeacherId)->get();
        }else{
            $teacherSubjects = collect([]);
        }

        return $teacherSubjects;
    }

    /**
     * 删除和老师关联
     *
     * @param $subject
     *
     * @author Zhoufuxiang  2016-04-13 10:55
     * @return mixed
     * @throws \Exception
     */
    public function delTeacherSubjects($subject)
    {
        //获取与当前考试项目相关联的老师
        $TeacherSubjects = TeacherSubject::where('subject_id','=',$subject->id)->get();
        if($TeacherSubjects){
            foreach ($TeacherSubjects as $teacher){
                if(!$teacher->delete()){
                    throw new \Exception('删除关联老师失败');
                }
            }
        }
        return true;
    }
}