<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/26
 * Time: 19:22
 */

namespace Modules\Osce\Entities;


class ExamResult extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_result';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['student_id', 'exam_screening_id', 'station_id', 'end_dt', 'begin_dt', 'time',
        'create_user_id', 'score', 'score_dt', 'teacher_id','evaluate','operation','skilled','patient','affinity'];

    public function student(){
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    public function teacher(){
        return $this->hasOne('\Modules\Osce\Entities\Teacher','id','teacher_id');
    }

    public function getResultList($examId,$stationId,$name){

        $builder=$this->leftJoin('student', function($join){
            $join -> on('student.id', '=', 'exam_result.student_id');
        })-> leftJoin('exam_screening', function($join){
            $join -> on('exam_result.exam_screening_id', '=', 'exam_screening.id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam_screening.exam_id', '=', 'exam.id');
        })-> leftJoin('station', function($join){
            $join -> on('exam_result.station_id', '=', 'station.id');
        });

        if($examId){
            $builder=$builder->where('exam.id',$examId);
        }
        if($stationId){
            $builder=$builder->where('station.id',$stationId);
        }
        if($name){
            $builder=$builder->where('student.name',$name);
        }

        $builder=$builder->select([
            'exam_result.id as id',
            'exam.name as exam_name',
            'exam_result.begin_dt as begin_dt',
            'exam_result.end_dt as end_dt',
            'exam_result.time as time',
            'exam_result.score as score',
            'student.name as student_name',
            'station.name as station_name',
        ])->paginate(config('osce.page_size'));

        $data=$builder;
        return $data;
    }

    public function getResultDetail($id){

        $builder=$this-> leftJoin('exam_screening', function($join){
            $join -> on('exam_result.exam_screening_id', '=', 'exam_screening.id');
        })-> leftJoin('exam', function($join){
            $join -> on('exam_screening.exam_id', '=', 'exam.id');
        })-> leftJoin('station', function($join){
            $join -> on('exam_result.station_id', '=', 'station.id');
        })-> leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        });

        $builder=$builder->where('exam_result.id',$id);
        $builder=$builder->select([
            'exam_result.id as id',
            'exam.name as exam_name',
            'exam.id as exam_id',
            'exam_result.begin_dt as begin_dt',
            'exam_result.end_dt as end_dt',
            'exam_result.time as time',
            'exam_result.score as score',
            'exam_result.station_id as station_id',
            'exam_result.student_id as student_id',
            'exam_result.teacher_id as teacher_id',
            'exam_result.evaluate as evaluate',
            'exam_result.operation as operation',
            'exam_result.skilled as skilled',
            'exam_result.patient as patient',
            'exam_result.affinity as affinity',
            'subject.title as subject_title',
            'subject.id as subject_id',
        ])->get();

        return $builder;
    }

    public function getStudent($stationId,$subjectId){

        $builder=$this-> leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })-> leftJoin('subject', function($join){
            $join -> on('subject.id', '=', 'station.subject_id');
        });

        $builder=$builder->select([
            'exam_result.student_id as student_id'
        ])->get();

        return $builder;
    }
}