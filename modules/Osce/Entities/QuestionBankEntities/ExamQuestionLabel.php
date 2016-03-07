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


/**试题和标签中间表
 * Class ExamQuestionLabel
 * @package Modules\Osce\Entities
 */
class ExamQuestionLabel extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_question_label';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_question_id', 'exam_paper_label_id'];

}