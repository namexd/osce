<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/30
 * Time: 18:26
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'lab';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'short_name','total', 'enname','short_enname','location_id','open_type','manager_user_id','created_user_id','status','floor','code'];
    public $search          =   [];

    //判断实验室类型
    public function getType($v){
        switch ($v) {
            case 1:
                $name = '实验室';
                break;
            case 2:
                $name = '准备间';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }


    //楼栋
    public function floors(){

        return $this->hasOne('Modules\Msc\Entities\Floor','id','location_id');
    }
    //用户管理员
    public function user(){

        return $this->hasOne('App\Entities\User','id','manager_user_id');
    }

    // 获得分页列表
    public function getFilteredPaginateList ($where)
    {

        $builder = $this;
        $local = 'location';
        $lab = 'lab';
        $user = 'user';
        if ($where['keyword'])
        {
            $builder = $builder->where($lab.'.name','like','%'.$where['keyword'].'%');
        }
        if ($where['status'] !== null && $where['status'] !== '')
        {
            $builder = $builder->where($lab.'.status','=',$where['status']);
        }
        if ($where['open_type'] !== null && $where['open_type'] !== '')
        {
            $builder = $builder->where($lab.'.open_type','=',$where['open_type']);
        }
        $builder = $builder->with(['floors','user']);
//        dd($builder);
//        $builder = $builder->leftJoin('location', function($join) use($local, $lab) {
//            $join->on($local.'.id', '=', $lab.'.location_id');
//        })->leftJoin('user', function($join) use($user, $lab) {
//            $join->on($user.'.id', '=', $lab.'.manager_user_id');
//        })->select($lab.'.*',$local.'.name as lname',$local.'.school_id',$user.'.name as tname',$user.'.id as tid');
        return $builder->orderBy('id')->paginate(config('msc.page_size',10));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月5日10:43:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function OpenPlan(){
        return  $this->hasMany('Modules\Msc\Entities\OpenPlan','lab_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月5日16:17:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function Floor(){
        return  $this->hasOne('Modules\Msc\Entities\Floor','id','location_id');
    }
    /**
     * 获取普通实验室列表 和 获取开放实验室列表
     * @param
     * @IdRrr 有日历安排的实验室数组
     * @type  1、普通实验室，2、开放实验室。默认获取开放实验室
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月4日15:51:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetLaboratoryListData($IdRrr,$type = 1){
        $thisBuilder = $this->where('open_type','=',1);
        if($type == 1){
            $thisBuilder = $thisBuilder->whereNotIn('id',$IdRrr);
        }elseif($type == 2){
            $thisBuilder = $thisBuilder->whereIn('id',$IdRrr);
        }
        $thisBuilder = $thisBuilder->with('Floor');
        return  $thisBuilder->paginate(config('msc.page_size',10));
    }

    /**
     * 获取实验室相关信息，以及日历安排（普通实验室填写表单页面、开放实验室展示日历安排页面）
     * @param $id
     * @param $dateTime
     * @return Array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月5日16:19:46
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetLaboratoryInfo($id,$dateTime){
        if(!empty($dateTime)){
            $timeInt = strtotime($dateTime);
            $y = date('Y',$timeInt);
            $m = date('m',$timeInt);
            $d = date('d',$timeInt);
            return $this->where('id','=',$id)->with(['Floor','OpenPlan'=>function($OpenPlan) use ($y,$m,$d){

                $OpenPlan->where('year','=',$y)->where('month','=',$m)->where('day','=',$d)->with(['OpenLabApply'=>function($OpenLabApply){
                    //TODO open_lab_apply表 状态1代表 学生预约的开放实验室
                    $OpenLabApply->where('type','=',1);
                },'PlanRecord'=>function($PlanRecord){
                    //TODO plan_record 状态1代表 开放实验室预约的生成的计划
                    $PlanRecord->where('type','=',1);
                }]);
            }])->first();
        }else{
            return  false;
        }

    }

    /**
     * 根据实验室id和和日历id数组  获取实验室信息楼栋信息和 开放日历信息
     * @param $LabId
     * @param $OpenPlanIdRrr
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年1月6日13:41:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetLaboratoryOpenPlan($LabId,$OpenPlanIdRrr){
        return $this->where('id','=',$LabId)->with(['Floor','OpenPlan'=>function($OpenPlan) use ($OpenPlanIdRrr){
            $OpenPlan->whereIn('id',$OpenPlanIdRrr);
        }])->first();
    }


}