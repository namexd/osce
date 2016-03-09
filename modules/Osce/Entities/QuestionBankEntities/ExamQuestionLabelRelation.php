<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;

use DB;
use Auth;
/**试题和标签关联
 * Class ExamQuestion
 * @package Modules\Osce\Entities
 */
class ExamQuestionLabelRelation extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_question_label_relation';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'status', 'exam_question_label_id', 'exam_question_id', 'created_user_id'];
    /**试卷标签表
     * @method
     * @url /osce/
     * @access public
     * @return bool
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function exam_question_label(){
        return $this->hasOne('Modules\Osce\Entities\QuestionBankEntities\examQuestionLabel','id','exam_question_label_id');
    }
}