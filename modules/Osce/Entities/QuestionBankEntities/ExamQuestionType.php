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
/**试题类型模型
 * Class ExamQuestionLabel
 * @package Modules\Osce\Entities
 */
class ExamQuestionType extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_question_type';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'name', 'status'];

    /**试题类型列表
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@sulida.com>
     * @date
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function examQuestionTypeList(){
        $data = $this->select('id','name')->get();
        return $data;
    }
}
