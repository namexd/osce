<?php
/**
 * 教室摄像头关联
 */
namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class ResourcesLabVcr extends Model
{
    protected   $connection	    =	'msc_mis';
    protected   $table 		    = 	'resources_lab_vcr';
    public      $timestamps	    =	true;
    public      $incrementing	=	true;
    protected   $fillable 	    =	['id', 'resources_lab_id', 'vcr_id'];
    public      $search         =   [];
    /**
     * 教室
     */
    public function lab(){
        return $this->belongsTo('Modules\Msc\Entities\ResourcesLab','resources_lab_id','id');
    }
    /**
     * 摄像头
     */
    public function vcr(){
        return $this->belongsTo('Modules\Msc\Entities\Vcr','vcr_id','id');
    }
}