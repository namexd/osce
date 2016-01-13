<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/4 0004
 * Time: 11:52
 */

namespace Modules\Msc\Entities;




use Illuminate\Database\Eloquent\Model;

class PlanApply extends Model
{
    public $timestamps	=	true;
    public $incrementing	=	true;
    protected $connection	=	'msc_mis';
    protected $table 		= 	'plan_apply';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['id','apply_id','open_plan_id'];

    /**
     * @content£º
     * @author£º
     * @createDate£º
     */
    public function OpenPlan(){
        return  $this->hasOne('Modules\Msc\Entities\OpenPlan','id','open_plan_id');
    }

}