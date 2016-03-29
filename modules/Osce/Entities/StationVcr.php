<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 15:25
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Repositories\Common;

class StationVcr extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_vcr';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'vcr_id','begind_dt','end_dt','updated_at'];
    public $search = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vcr()
    {
        return $this->belongsTo('Modules\Osce\Entities\Vcr', 'vcr_id', 'id');
    }

    public function getTime($vcr_id,$startTime,$endTime){
        $startTime=strtotime($startTime);
        $endTime=strtotime($endTime);
        $list=$this->select(DB::raw(
            implode(',',[
                $this->table.'.id as id',
                $this->table.'.station_id as station_id',
                $this->table.'.begin_dt as begin_dt',
                $this->table.'.end_dt as end_dt',
                $this->table.'.created_at as created_at',
            ])
        )
        );
        if($startTime){
            $list=$list->whereRaw(
                'unix_timestamp('.$this->table.'.begin_dt) <= ?',
                [
                    $startTime
                ]
            );
        }

        if($endTime){
            $list=$list->whereRaw(
                'unix_timestamp('.$this->table.'.end_dt) => ?',
                [
                    $endTime
                ]
            );
        }

        if($vcr_id){
            $list=$list->where('vcr_id',$vcr_id);
        }
        return $list;

    }

    //��ȡvcr��Ϣ
    public  function vcrlist($stationId){
           return Vcr::leftJoin('station_vcr',function($join){
                 $join->    on('vcr.id','=','station_vcr.vcr_id');
           }) ->where('station_vcr.station_id','=',$stationId)
               ->select([
                   'vcr.name as name',
                   'vcr. code as code',
                   'vcr.ip` as ip`',
                   'vcr.username as username',
                   'vcr.port as port',
                   'vcr.channel as channel',
                   'vcr.status as status',
               ])
               ->get();
    }


//    public function getTiming($vcrId,$beginDt,$examId,$room,$endDt){
//        $beginDt=strtotime($beginDt);
//        $endDt=strtotime($endDt);
//        $builder=$this->leftJoin('vcr',function($join){
//            $join->on('station_vcr.id','=','vcr.id');
//        })->leftJoin('room_station',function($join){
//            $join->on('station_vcr.station_id','=','room_station.station_id');
//        });
//        $builder=$builder->where('station_vcr.vcr_id',$vcrId)->where('room_station.room_id',$room)->where('station_vcr.exam_id',$examId);;
//        $builder=$builder->whereRaw(
//            'unix_timestamp('.'station_video.begin_dt'.') >= ?',
//            [
//                $beginDt
//            ]
//        );
//        $builder=$builder->whereRaw(
//            'unix_timestamp('.'station_video.end_dt'.') >= ?',
//            [
//                $endDt
//            ]
//        );
//        $builder=$builder->select([
//            'vcr.name as name',
//            'vcr.code as code',
//            'vcr.ip as ip',
//            'vcr.username as username',
//            'vcr.port as port',
//            'vcr.channel as channel',
//            'vcr.status as status',
//        ]);
//        $data=$builder->get();
//
//        return $data;
//    }


    //获取考站摄像机信息
    public function getStionVcr($room_id,$exam_id){
        try{
            $exam = Exam::doingExam($exam_id);
            Common::valueIsNull($exam, -1, '没有找到对应的考试');
            if($exam->sequence_mode == 2){
                $result = $this -> leftJoin('exam_station', function($join){
                    $join -> on('exam_station.station_id', '=', 'station_vcr.station_id');
                }) ->leftJoin('vcr', function($join){
                    $join -> on('vcr.id', '=', 'station_vcr.vcr_id');
                })->leftJoin('station', function($join) {
                    $join->on('station.id', '=', 'station_vcr.station_id');
                });
                $result=$result ->where('exam_station.station_id', '=', $exam_id);

            }else{
                $result = $this->leftJoin('room_station', function($join){
                    $join -> on('room_station.station_id', '=', 'station_vcr.station_id');
                })-> leftJoin('exam_room', function($join){
                    $join -> on('exam_room.room_id', '=', 'room_station.room_id');
                })    ->leftJoin('vcr', function($join){
                    $join -> on('vcr.id', '=', 'station_vcr.vcr_id');
                })->leftJoin('station', function($join) {
                    $join->on('station.id', '=', 'station_vcr.station_id');
                });
             $result=$result ->where('exam_room.exam_id', '=', $exam_id);
             $result=$result ->where('room_station.room_id',$room_id);
            }

            $result= $result->select(['station.name as station_name','station_vcr.id AS stationVcrId','vcr.id','vcr.name','vcr.ip','vcr.status','vcr.port','vcr.channel','vcr.username','vcr.password'])
                -> get();

            return $result;
        } catch(\Exception $ex){
            return $ex;
        }
    }

}