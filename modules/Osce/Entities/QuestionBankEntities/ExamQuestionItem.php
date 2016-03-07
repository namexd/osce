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

/**试题子项表
 * Class ExamQuestionItem
 * @package Modules\Osce\Entities
 */
class ExamQuestionItem extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_question_item';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'name', 'content', 'exam_question_id', 'status'];

}