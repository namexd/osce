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

    public function getStudent($time,$mode='',$exam_id){
        $time=1452857167;
        $room_ids=ExamRoom::where('exam_id',$exam_id)->select('room_id')->get();

        $builder=$this->Join('student','exam_queue.student_id','=','student.id')
            ->Join('room','exam_queue.room_id','=','room.id');
        $builder=$builder->select(
            'exam_queue.id as id',
            'exam_queue.begin_dt as begin_dt',
            'room.name as room_name',
            'room.id as room_id',
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

        if($mode==1){
            $data=[];
            foreach($room_ids as $item){
                $room_id=$item->room_id;
                $builder=$builder->where('room.id',$room_id);
                $builder=$builder->take(10)->get();
                $data[]=[$builder];
            }
            return $data;
        }elseif($mode==2){
//            $builder=$this->Join('student','exam_queue.student_id','=','student.id')
//                ->Join('room','exam_queue.room_id','=','room.id');
//            dd(1);
////            $station_id=RoomStation::whereIn('room_id',$room_ids)->select('station_id')->get();
////            dd($station_id);
//            $builder=$builder->Join('station','exam_queue.station_id','=','station.id')
//                ->Join('room','exam_queue.room_id','=','room.id');
//            $builder=$builder->select(
//                'exam_queue.id as id',
//                'room.name as room_name',
//                'exam_queue.begin_dt as begin_dt',
//                'room.address as room_address',
//                'room.id as room_id',
//                'station.id as station_id',
//                'student.name as student_name',
//                'student.code as student_code',
//                'station.name as station_name'
//            );
//            $builder=$builder->whereRaw(
//                'unix_timestamp('.$this->table.'.begin_dt) >= ?',
//                [
//                    $time
//                ]
//            );
          return   $builder->take(10)->get();
        }
        else{
//            $builder=$this->Join('student','exam_queue.student_id','=','student.id')
//                ->Join('room','exam_queue.room_id','=','room.id');
//            $builder=$builder->select(
//                'exam_queue.id as id',
//                'exam_queue.begin_dt as begin_dt',
//                'student.name as student_name',
//                'student.code as student_code'
//            );
//            $builder=$builder->whereRaw(
//                'unix_timestamp('.$this->table.'.begin_dt) >= ?',
//                [
//                    $time
//                ]
//            );
          return  $builder->take(0,10)->get();
        }
    }
}