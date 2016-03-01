<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 20:54
 */

namespace Modules\Osce\Entities;


class Standard extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'standard';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['subject_id', 'content', 'sort', 'score', 'created_user_id','pid','level','answer'];
    public $search = [];


    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    //获取考核项
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\Standard','id','pid');
    }

    public function childrens(){
        return $this->hasMany('Modules\Osce\Entities\Standard','pid','id');
    }

    public function ItmeList($subjectId){
        try{
            $prointList =   $this->where('subject_id','=',$subjectId)->get();
            $data       =   [];
            foreach($prointList as $item)
            {
                $data[$item->pid][] =   $item;
            }
            $return =   [];
            foreach($data[0] as $proint)
            {
                $prointData =   $proint;

//            $prointData['test_term']=0;
                //$prointData['test_point']['test_term']    =   $data[$proint->id];
                if(array_key_exists($proint->id,$data))
                {
                    $prointData['test_term']    =   $data[$proint->id];

                }
                else
                {
                    $prointData['test_term']    =   [];
                }
                $return[]=$prointData;
                foreach($return as $proint){
                    foreach($proint['test_term'] as $str){
                        $str['real']= '0' ;
                    }
                }
            }

            return $return;
        }catch (\Exception $ex){
           throw $ex;
        }
    }





   public function getScore($stationId,$subjectId){

       $builder=$this-> leftJoin('exam_score', function($join){
           $join -> on('standard.id', '=', 'exam_score.standard_id');
       })-> leftJoin('station', function($join){
           $join -> on('station.subject_id', '=', 'exam_score.subject_id');
       })-> leftJoin('exam_result', function($join){
           $join -> on('station.id', '=', 'exam_result.station_id');
       });
       $builder=$builder->where('standard.pid',0)->where('exam_score.subject_id',$subjectId)->where('station.id',$stationId);

       $builder=$builder->select([
           'standard.score as score',
           'standard.id as id',
           'standard.sort as sort',
       ])->orderBy('standard.sort','DESC')->get();

       return $builder;
    }

   public function getAvgScore($sort,$stationId,$subjectId){

       $builder=$this-> leftJoin('exam_score', function($join){
           $join -> on('standard.id', '=', 'exam_score.standard_id');
       })-> leftJoin('station', function($join){
           $join -> on('station.subject_id', '=', 'exam_score.subject_id');
       })-> leftJoin('exam_result', function($join){
           $join -> on('station.id', '=', 'exam_result.station_id');
       });
       $builder=$builder->where('standard.pid',0)->where('exam_score.subject_id',$subjectId)->where('station.id',$stationId)->where('sort',$sort);

       $builder=$builder->avg('exam_score.score');

      return $builder;
   }

    /**
     * 查询考核点的平均分
     * @author zhoufuxiang <zhoufuxiang@misrobot.com>
     */
    public function getCheckPointAvg($pid, $subjectId)
    {
        $builder = $this-> leftJoin('exam_score', function($join){
                    $join -> on('standard.id', '=', 'exam_score.standard_id');
                })
                -> leftJoin('exam_result', function($join){
                    $join -> on('exam_result.id', '=', 'exam_score.exam_result_id');
                });
        $builder = $builder ->where('standard.pid', $pid)
                            ->where('exam_score.subject_id', $subjectId)
                            ->groupBy('exam_score.exam_result_id');

        $builder = $builder->select(\DB::raw(implode(',', ["SUM(exam_score.score) as total_score"])))
                    ->get();
        $avg = 0;
        if(count($builder)){
            $totalScore = 0;
            foreach ($builder as $item) {
                $totalScore += $item->total_score;
            }
            $avg = round($totalScore/count($builder),1);
        }

        return $avg;
    }
}