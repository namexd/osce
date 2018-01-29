<?php
/*
 * name：IP限制考试模型
 * date：2018/1/29 9:55
 * author:Hsiaowei(phper.tang@qq.com)
 * param： int *
 * param： string *
 * return： array
 * */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;

class IpLimit extends Model{

    protected $connection = 'osce_mis';
    protected $table = 'g_test_limit';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'id',   'ip_start',   'ip_end',     'created_at', 'updated_at',
    ];

}