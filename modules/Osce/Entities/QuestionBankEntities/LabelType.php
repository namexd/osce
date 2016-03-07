<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;


/**题目类型表
 * Class ExamQuestionLabel
 * @package Modules\Osce\Entities
 */
class LabelType extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'label_type';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'name', 'status'];

    /**获取标签类型列表
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function labelTypeList(){
        $data = $this->select('id','name')->orderBy('created_at','desc')->get();
        return $data;
    }
}
