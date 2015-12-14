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

    public function getStatusAttribute($status){
        switch($status){
            case 0:
                $name= '借出未归还';
            break;
            case 1:
                $name= '已归还';
            break;
            case -1:
                $name= '预约已过期';
            break;
            case -2:
                $name= '取消预约';
            break;
            case -3:
                $name= '超期未归还';
            break;
            case 4:
                $name= '已归还但有损坏';
            break;
            case 5:
                $name= '超期归还';
            break;
        }
        return $name;
    }

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
            ->  where('status','=',1)
            ->  where('loan_validated','=',0)
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
}