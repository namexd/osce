<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/14 0014
 * Time: 14:44
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;

class TestRecord extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'g_test_record';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'id', 'logid', 'stuid', 'cid', 'answer', 'isright', 'time',
        'poins', 'type', 'ifexam'
    ];

}