<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/12 0012
 * Time: 12:48
 */

namespace Modules\Osce\Entities;

use Auth;
use Illuminate\Database\Eloquent\Collection;

class StandardItem extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'standard_item';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['standard_id', 'content', 'sort', 'score', 'pid', 'level', 'created_user_id', 'answer'];
    public    $search       = [];


    //获取考核项
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\StandardItem','id','pid');
    }

    public function childrens(){
        return $this->hasMany('Modules\Osce\Entities\StandardItem','pid','id');
    }

    /**
     * 新增 考核标准详情
     * @access public
     *
     * @param $subject        考核标准（考核课题数据对象）
     * @param array $point    考核标准详情
     * @param string $parent  父级考核点
     *
     * @return object
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-04-12 12:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addItem($standard,array $point,$parent=''){
        try{
            $user   =   Auth::user();

            if(array_key_exists('child',$point)){
                $data   =   [
                    'standard_id'       =>  $standard->id,
                    'content'           =>  $point['content'],
                    'sort'              =>  $point['sort'],
                    'score'             =>  $point['score'],
                    'created_user_id'   =>  $user->id,
                    'pid'               =>  0,
                    'level'             =>  1,
                ];
                $item    =   $this   ->  create($data);
                if(!$item){
                    throw new \Exception('新增考核点失败');
                }

                foreach($point['child'] as $children)
                {
                    $this->addItem($standard, $children, $item);
                }

            } else{
                $level  =   $parent->level+1;
                $data   =   [
                    'standard_id'        =>  $standard->id,
                    'content'           =>  $point['content'],
                    'sort'              =>  $point['sort'],
                    'score'             =>  $point['score'],
                    'answer'            =>  $point['answer'],
                    'created_user_id'   =>  $user->id,
                    'pid'               =>  $parent->id,
                    'level'             =>  $level,
                ];
                $item   =   $this   ->  create($data);
                if(!$item)
                {
                    throw new \Exception('新增考核项失败');
                }
            }
            return $item;

        } catch (\Exception $ex){
            throw $ex;
        }
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

    /**
     * @param $subjectId
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @return array
     */
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

    static public function builderItemTable(Collection $itemCollect){
        $list   =   [];
        $child   =   [];
        foreach($itemCollect as $item)
        {
            if($item->pid   ==  0)
            {
                $list[] =    $item;
            }
            else
            {
                $child[$item->pid][]=$item;
            }
        }

        $data   =   [];
        foreach($list as $item)
        {
            $data[]     =   $item;

            $childItem  =   array_key_exists($item->id,$child)? $child[$item->id]:[];

            $indexArray =   array_pluck($childItem,'sort');
            array_multisort($indexArray, SORT_ASC, $childItem);

            foreach($childItem as $chilren)
            {
                $data[]     =   $chilren;
            }
        }
        return $data;
    }


//
//    /**
//     * @version 1.0
//     * @author zhouchong <zhouchong@misrobot.com>
//     */
//    public function getScore($stationId,$subjectId){
//
//        $builder=$this-> leftJoin('exam_score', function($join){
//            $join -> on('standard.id', '=', 'exam_score.standard_id');
//        })-> leftJoin('station', function($join){
//            $join -> on('station.subject_id', '=', 'exam_score.subject_id');
//        })-> leftJoin('exam_result', function($join){
//            $join -> on('station.id', '=', 'exam_result.station_id');
//        });
//        $builder=$builder->where('standard.pid',0)->where('exam_score.subject_id',$subjectId)->where('station.id',$stationId);
//
//        $builder=$builder->select([
//            'standard.score as score',
//            'standard.id as id',
//            'standard.sort as sort',
//        ])->orderBy('standard.sort','DESC')->get();
//
//        return $builder;
//    }
//    public function getAvgScore($sort,$stationId,$subjectId){
//
//        $builder=$this-> leftJoin('exam_score', function($join){
//            $join -> on('standard.id', '=', 'exam_score.standard_id');
//        })-> leftJoin('station', function($join){
//            $join -> on('station.subject_id', '=', 'exam_score.subject_id');
//        })-> leftJoin('exam_result', function($join){
//            $join -> on('station.id', '=', 'exam_result.station_id');
//        });
//        $builder=$builder->where('standard.pid',0)->where('exam_score.subject_id',$subjectId)->where('station.id',$stationId)->where('sort',$sort);
//
//        $builder=$builder->avg('exam_score.score');
//
//        return $builder;
//    }

}