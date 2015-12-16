<?php
/**
 * 摄像头
 */
namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

class Vcr extends Model
{
    protected   $connection	    =	'msc_mis';
    protected   $table 		    = 	'vcr';
    public      $timestamps	    =	true;
    public      $incrementing	=	true;
    protected   $fillable 	    =	['id', 'name', 'code','ip','username','password','port','channel','description'];
    public      $search         =   [];
}
