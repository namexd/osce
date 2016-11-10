<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/30
 * Time: 18:26
 */

namespace Modules\Msc\Entities;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'groups';
    public $timestamps	=	false;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name', 'detail', 'student_class_id'];
    public $search          =   [];

    public function students ()
    {
        return $this->belongsToMany('Modules\Msc\Entities\Student', 'student_group', 'group_id', 'student_id');
    }
}