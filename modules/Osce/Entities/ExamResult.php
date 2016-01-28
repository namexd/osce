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
}