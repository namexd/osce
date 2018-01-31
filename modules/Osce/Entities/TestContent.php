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
        'id', 'test_id', 'type', 'category', 'images', 'answer', 'poins', 'question',
        'pbase', 'base', 'cognition', 'source', 'lv', 'require','times',
        'degree', 'separate', 'content'
    ];
    protected $visible = [ ];

    /**
     * 获取考试的试卷
     */
    public function test()
    {
        return $this->hasOne('Modules\Osce\Entities\Test','id','test_id');
    }
    public function testLog()
    {
        return $this->hasOne('Modules\Osce\Entities\TestLog','tid','test_id');
    }

}