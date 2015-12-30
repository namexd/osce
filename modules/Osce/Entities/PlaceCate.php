<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Repositories\Common;
class PlaceCate extends Model
{

    protected $connection	=	'osce_mis';
    protected $table 		= 	'place_cate';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'pid', 'cid'];
    public $search          =   [];

    public function place()
    {
        return $this->hasMany('\Modules\Osce\Entities\Place','pid','cid');
    }


}