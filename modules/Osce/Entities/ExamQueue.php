<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 15:19
 */
namespace Modules\Osce\Entities;

class ExamQueue extends  CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_queue';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];
    public $search = [];

    public function getStudent($time,$mode){

        $builder=$this->Join('student','exam_queue.student_id','=','student.id');
        if($mode==1){
            $builder=$builder->Join('room','exam_queue.room_id','=','room.id');
            $builder=$builder->select(
                'exam_queue.id as id',
                'exam_queue.begin_dt as begin_dt',
                'room.name as room_name',
                'room.address as room_address',
                'student.name as student_name',
                'student.code as student_code'
            );
            $builder=$builder->whereRaw(
                'unix_timestamp('.$this->table.'.begin_dt) >= ?',
                [
                    $time
                ]
            );
            dd($builder->take(10)->get());
          return  $builder->take(10)->get();
        }elseif($mode==2){
            $builder=$builder->Join('station','exam_queue.station_id','=','station.id')
                ->Join('room','exam_queue.room_id','=','room.id');
            $builder=$builder->select(
                'exam_queue.id as id',
                'room.name as room_name',
                'exam_queue.begin_dt as begin_dt',
                'room.address as room_address',
                'student.name as student_name',
                'student.code as student_code',
                'station.name as station_name'
            );
            $builder=$builder->whereRaw(
                'unix_timestamp('.$this->table.'.begin_dt) >= ?',
                [
                    $time
                ]
            );
          return   $builder->take(10)->get();
        }else{
            $builder=$builder->select(
                'exam_queue.id as id',
                'exam_queue.begin_dt as begin_dt',
                'student.name as student_name',
                'student.code as student_code'
            );
            $builder=$builder->whereRaw(
                'unix_timestamp('.$this->table.'.begin_dt) >= ?',
                [
                    $time
                ]
            );
          return  $builder->take(10)->get();
        }

        return $builder;
    }
}