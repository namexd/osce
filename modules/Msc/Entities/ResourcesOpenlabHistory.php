<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use App\Repositories\Common;
use Illuminate\Database\Eloquent\Model;
use DB;

class ResourcesOpenlabHistory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_history';
    protected $fillable 	=	['resources_openlab_apply_id', 'resources_lab_id', 'begin_datetime','end_datetime','group_id','teacher_uid','result_poweroff','result_init'];

    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;

    public function lab(){
        return $this    ->  hasOne('Modules\Msc\Entities\ResourcesClassroom','id','resources_lab_id');
    }
    public function apply(){
        return $this->hasOne('Modules\Msc\Entities\ResourcesOpenLabApply','id','resources_openlab_apply_id');
    }
    /**
     * 根据申请ID 删除历史记录
     * @access public
     *
     * * string        id        申请ID(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 215-12-23 11:10
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function delHistoryByApplyId($id){
        try{
            $history    =   $this   ->  where   ('resources_openlab_apply_id','=',$id)->first();
            if($history)
            {
                $result =   $history    ->  delete();
                if(!$result)
                {
                    throw new \Exception('删除历史记录失败');
                }
            }
            return true;
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function getOpenlabHistory($date,$name='',$result_poweroff=false,$result_init=false){
        $bulider    =   $this   ->  leftJoin(
            'resources_openlab_apply',function($join){
                $join   ->  on($this    ->  table.'.resources_openlab_apply_id','=','resources_openlab_apply.id');
            }
        )   ->  leftJoin(
            'resources_lab',function($join){
                $join   ->  on($this    ->  table.'.resources_lab_id','=','resources_lab.id');
            }
        )->  leftJoin(
            'teacher',function($join){
                $join   ->  on('resources_openlab_apply.apply_uid','=','teacher.id');
            }
        )       ->  leftJoin(
            'student',function($join){
                $join   ->  on('resources_openlab_apply.apply_uid','=','student.id');
            }
        )   ->select(
            DB::raw(
                implode(
                    ',',
                    [
                        $this->table.'.id as id',
                        $this->table.'.resources_openlab_apply_id as resources_openlab_apply_id',
                        $this->table.'.resources_lab_id as resources_lab_id',
                        $this->table.'.begin_datetime as begin_datetime',
                        $this->table.'.end_datetime as end_datetime',
                        $this->table.'.group_id as group_id',
                        $this->table.'.teacher_uid as teacher_uid',
                        $this->table.'.result_poweroff as result_poweroff',
                        $this->table.'.result_init as result_init',
                        'resources_lab.name as resources_lab_name',
                        //'date_format('.$this->table.'.begin_datetime,"%Y-%m-%d") as begin_datetime',
                    ]
                )
            )
        );
        $date   =   date('Y-m-d',strtotime($date));
        $bulider    =   $bulider    ->  whereRaw(
            'date_format('.$this->table.'.begin_datetime,"%Y-%m-%d") = ?',
            [
                $date
            ]
        );
        if($result_poweroff!==false)
        {
            $bulider    =   $bulider    ->  where($this->table.'.result_poweroff','=',$result_poweroff);
        }
        if($name!='')
        {
            $bulider    =   $bulider    ->  where('resources_lab.name','like','%'.$name.'%');
        }
        if($result_init!==false)
        {
            $bulider    =   $bulider    ->  where('result_poweroff','=',$result_poweroff);
        }
        return $bulider    ->  paginate(config('msc.page_size'));
    }
    // 获得pc端开放实验室使用历史记录分析数据
    public function getPcAnalyze ($where)
    {
        $searchDate  = empty($where['date']) ? null : $where['date'];
        $result_init = empty($where['result_init']) ? null : $where['result_init'];

        // 筛选所有符合条件的开放实验室
        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        });

        // 是否进行了日期筛选
        if($searchDate)
        {
            $builder = $builder->whereRaw(
                'date_format('.$this->table.'.begin_datetime,"%Y-%m-%d") = ?',
                [
                    $searchDate
                ]
            );
        }

        $builder = $builder->leftJoin('resources_openlab_apply',function($join){
            $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
        });

        // 是否筛选了复位状态
        if ($result_init)
        {
            $builder = $builder->where('resources_openlab_history.result_init', $result_init);
        }

        // 进行筛选



        $temp = $builder->select(DB::raw(
            implode(
                ',',
                [
                    'resources_lab.name as name',
                    'count('.$this->table.'.id) as total',
                ]
            )
        ))->get();
        dd($temp);
        $data = [];
        foreach ($temp as $item)
        {
            $data[] = $item->name;
        }


        return array_count_values(array_values($data));
    }
}