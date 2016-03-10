<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/27 0027
 * Time: 14:18
 */
namespace Modules\Osce\Entities;


class StationVideo extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_video';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_vcr_id', 'begin_dt', 'end_dt', 'exam_id', 'student_id'];
    public $search = [];


    public function getTiming($stationVcrId,$beginDt,$examId,$endDt){
        $beginDt=strtotime($beginDt);
        $endDt=strtotime($endDt);
        $builder=$this->leftJoin('station_vcr',function($join){
            $join->on('station_video.station_vcr_id','=','station_vcr.id');
        })->leftJoin('vcr',function($join){
            $join->on('station_vcr.vcr_id','=','vcr.id');
        });
        $builder=$builder->where('station_video.station_vcr_id',$stationVcrId)->where('station_video.exam_id',$examId);
        if($beginDt){
            $builder=$builder->whereRaw(
                'unix_timestamp('.'station_video.begin_dt'.') >= ?',
                [
                    $beginDt
                ]
            );
        }
        if($endDt){
            $builder=$builder->whereRaw(
                'unix_timestamp('.'station_video.end_dt'.') >= ?',
                [
                    $endDt
                ]
            );
        }

        $builder=$builder->select([
            'vcr.name as name',
            'vcr.code as code',
            'vcr.ip as ip',
            'vcr.username as username',
            'vcr.port as port',
            'vcr.channel as channel',
            'vcr.status as status',
        ]);
        $data=$builder->get();

        return $data;
    }

    /**
     * 根据各种id获取标记点列表
     * @param $examId
     * @param $studentId
     * @param $stationId
     * @author Jiangzhiheng
     */
//    static public function label($examId, $studentId, $stationId)
//    {
////        $connection = \DB::connection('osce_mis');
////        $connection->enableQueryLog();
//            return StationVideo::Join('station_vcr','station_video.station_vcr_id','=','station_vcr.id')
//            ->Join('vcr','vcr.id','=','station_vcr.vcr_id')
//            ->Join('exam_result','exam_result.station_id','=','station_vcr.station_id')
//            ->where('station_video.exam_id','=',$examId)
//            ->where('station_video.student_id',$studentId)
//            ->where('station_vcr.station_id',$stationId)
//            ->groupBy('station_video.begin_dt')
//            ->select(
//                'vcr.ip as ip',
//                'vcr.username as username',
//                'vcr.password as password',
//                'vcr.port as port',
//                'vcr.channel as channel',
//                'station_video.begin_dt as anchor',
//                'exam_result.begin_dt as begin_dt',
//                'exam_result.end_dt as end_dt'
//            )
//            ->get();
//
////        $c = $connection->getQueryLog();
////        dd($c);
//    }


    static function  getTationVideo($examId, $studentId, $stationVcrId){

        return  StationVideo::where('exam_id','=',$examId)
            ->where('student_id','=',$studentId)
            ->where('station_id','=',$stationVcrId)
            ->groupBy('station_video.begin_dt')
            ->get();
    }


    // TODO 临时修改运用
    static public function label($examId, $studentId, $stationId,$examScreeningIds)
    {
        return ExamResult::Join('station_vcr','exam_result.station_id','=','station_vcr.station_id')
            ->Join('vcr','vcr.id','=','station_vcr.vcr_id')
            ->where('exam_result.student_id',$studentId)
            ->where('exam_result.station_id',$stationId)
            ->whereIn('exam_result.exam_screening_id',$examScreeningIds)
            ->groupBy('exam_result.begin_dt')
            ->select(
                'vcr.ip as ip',
                'vcr.username as username',
                'vcr.password as password',
                'vcr.port as port',
                'vcr.channel as channel',
                'exam_result.begin_dt as anchor',
                'exam_result.begin_dt as begin_dt',
                'exam_result.end_dt as end_dt'
            )
            ->get();
    }

}