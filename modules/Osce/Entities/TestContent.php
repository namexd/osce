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

class TestContent extends Model
{
    protected $connection   = 'osce_mis';
    protected $table        = 'g_test_content';
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
    protected $visible = [ ];



}