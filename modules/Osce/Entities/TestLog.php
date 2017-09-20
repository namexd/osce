<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/11
 * Time: 14:30
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;


class TestLog extends Model
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'g_test_log';
    public $incrementing	=	true;
    public $timestamps	    =	false;
    protected $fillable 	=   ['id','exam_id','tid','teacher','times','start','end','convert','status','ifshow'];
    protected $hidden = [];


    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam','id','exam_id');
    }
    public function question(){
        return $this->hasMany('\Modules\Osce\Entities\TestContent','test_id','tid');
    }
    public function test(){
        return $this->hasOne('\Modules\Osce\Entities\Test','id','tid');
    }
    public function teacherdata(){
        return $this->hasOne('\Modules\Osce\Entities\Teacher','id','teacher');
    }

}