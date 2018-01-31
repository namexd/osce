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
        'id', 'test_id', 'type', 'category', 'images', 'answer', 'poins', 'question',
        'pbase', 'base', 'cognition', 'source', 'lv', 'require','times',
        'degree', 'separate', 'content'
    ];
    protected $visible = ['typeValues','cognitionValues','sourceValues','lvValues','requireValues','degreeValues'];

    public $typeValues  = [
        1 => '单选题',
        2 => '多选题',
        3 => '判断题',
        4 => '填空题',
        5 => '名词解释题',
        6 => '论述题',
        7 => '简答题'
    ];
    //认知
    public $cognitionValues  = [
        1 => '解释',
        2 => '记忆',
        3 => '应用'
    ];
    //题源
    public $sourceValues  = [
        1 => '自编',
        2 => '国内',
        3 => '国外'
    ];
    //适用层次
    public $lvValues  = [
        1 => '专科生',
        2 => '本科生',
        3 => '研究生',
        4 => '博士生'
    ];
    //要求度
    public $requireValues  = [
        1 => '熟悉',
        2 => '了解',
        3 => '掌握'
    ];
    //难度
    public $degreeValues  = [
        1 => '简单',
        2 => '中等',
        3 => '较难'
    ];

    public function getValues($column)
    {
        $column = $column.'Values';
        return $this->$column;
    }


}