<?php
/**
 * 考核项目
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Model;

class TestContentModule extends Model
{
    protected $connection   = 'osce_mis';
    protected $table        = 'g_test_content_module';
    public    $timestamps   = false;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden = ['answer'];
    protected $fillable     = [
        'id', 'test_id', 'type', 'images', 'answer', 'poins', 'question',
        'pbase', 'base', 'cognition', 'source', 'lv', 'require','times',
        'degree', 'separate', 'content'
    ];
    protected $typeValues  = [
        1 => '单选题',
        2 => '多选题',
        3 => '判断题',
        4 => '填空题',
        5 => '名词解释题',
        6 => '论述题',
        7 => '简答题'
    ];


}