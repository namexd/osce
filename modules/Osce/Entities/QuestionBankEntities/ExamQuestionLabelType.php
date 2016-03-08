<?php
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
class ExamQuestionLabelType extends  Model
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_question_label_type';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['id', 'name','status'];
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

    /**获取标签类型和标签的相关数据
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getLabAndType(){
        $builder = $this;
        $builder = $builder->with('LabelTypeAndLabel')->get();
        dd($builder);
    }
}


