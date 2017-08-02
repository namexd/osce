<?php
/**
 * 考核项目
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\StandardItem as SItem;

class TestContent extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'g_test_content';
    public    $timestamps   = false;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'id', 'test_id', 'type', 'answer', 'poins', 'question',
        'pbase', 'base', 'cognition', 'source', 'lv', 'require','times',
        'degree', 'separate', 'content'
    ];


}