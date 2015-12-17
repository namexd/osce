<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/12
 * Time: 13:33
 */

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class ResourcesBorrowing extends  CommonModel
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_tools_borrowing';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['code', 'resources_tool_item_id', 'begindate', 'enddate', 'lender', 'detail'];
    public $search 	=	['code','agent_name','detail','description','type'];
    public function resources(){
        return $this->belongsTo('Modules\Msc\Entities\Resources','resources_id');
    }
    public function lenderInfo(){
        return $this->belongsTo('App\Entities\User','lender');
    }

    public function user(){
        return $this->hasOne('App\Entities\User','id','lender');
    }

    public function resourcesTool(){

        return $this->hasOne('\Modules\Msc\Entities\ResourcesTools','id','resources_tool_id');

    }
    public function resourcesToolItem(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesToolsItems','resources_tool_item_id','id');
    }

    public function resourcesImage(){
        return $this->hasOne('Modules\Msc\Entities\resourcesImage','resources_id','id');
    }

//    public function getStatusAttribute($status){
//        switch($status){
//            case 0:
//                $name= '借出未归还';
//            break;
//            case 1:
//                $name= '已归还';
//            break;
//            case -1:
//                $name= '预约已过期';
//            break;
//            case -2:
//                $name= '取消预约';
//            break;
//            case -3:
//                $name= '超期未归还';
//            break;
//            case 4:
//                $name= '已归还但有损坏';
//            break;
//            case 5:
//                $name= '超期归还';
//            break;
//        }
//        return $name;
//    }



    public function getWaitExamineListByToolsName($name,$pid=false){
         $builder   =   $this   ->  with([
                'resourcesTool'    =>  function($qurey) use ($name){
                    if(!is_null($name))
                    {
                        $qurey  ->where('name','like','%'.$name.'%');
                    }
                }
        ])  ->  whereRaw(
            'unix_timestamp(begindate)  >= ?',
            [
                strtotime(date('Y-m-d'))
            ]
        )   ->  orderBy('begindate','asc')
            ->  where('apply_validated','=',0);
        if($pid!==false)
        {
            //如果
            if($pid==1)
            {
                $builder    =   $builder    ->  where('pid','>',0);
            }
            if($pid==2)
            {
                $builder    =   $builder    ->  where('pid','=',0);
            }
        }
        return    $builder->  paginate(config('msc.page_size'));
    }
    //查询借出设备的历史记录
    public function getBorrowRecordList($realBegindate = '', $realEnddate = '', $keyword = '', $isGettime = '', $status = '') {
        $builder = $this->leftJoin(
            'resources_tools',function($join){
                $join->on('resources_tools.id','=',$this->table.'.resources_tool_id');
            }
        )   ->  where($this->table.'.status','<>',0);
        //是否根据关键字搜索设备名字
        if ($keyword !== "") {
            $builder->where('resources_tools.name','like','%'.$keyword.'%');
        }
        //是否按照逾期归还设备查询
        if ($isGettime === 1) {
            $builder->whereIn($this->table.'.status',[-3,5]);
        }
        if ($isGettime === 2) {
            $builder->whereIn($this->table.'.status',[1,4]);
        }
        //是否按照时间进行查询

        if ($realBegindate === null && $realEnddate === null) {
            $builder->whereRaw('unix_timestamp(real_enddate) < ?',[strtotime(date('Y-m-d H:i:s'))]);
        } else {
            $builder->whereRaw('unix_timestamp(real_enddate) > ?',[$realEnddate])
                ->whereRaw('unix_timestamp(real_begindate) < ?',[$realBegindate]);
        }
        //按照状态进行查询
        if ($status !== null) {
            $builder -> where($this->table.'.status','=',$status);
        }

        $builder->select([
            $this->table.'.id as id',
            'resources_tools.name as tools_name',
            $this->table.'.real_begindate as real_begindate',
            $this->table.'.real_enddate as real_enddate',
            $this->table.'.detail as detail',
            $this->table.'.status as status',
            $this->table.'.resources_tool_item_id as resources_tool_item_id',
            $this->table.'.lender as lender'
        ])->orderBy('id','desc');
        return $builder->paginate(20);
    }

    public function getBorrowedList($keyword) {
        $builder = $this->leftJoin(
            'resources_tools',function ($join) {
                $join->on('resources_tools.id','=',$this->table.'.resources_tool_id');
            }
        )   ->  where($this->table.'.status','=',0)->where('apply_validated','=',1)
            ->  where($this->table.'.loan_validated','=',1)
            ->whereRaw('unix_timestamp(real_enddate) < ?',[strtotime(date('Y-m-d H:i:s'))]);

        if ($keyword !== "") {
            $builder->where('resources_tools.name','like','%'.$keyword.'%');
        }
        return $builder->paginate(20);
    }
}