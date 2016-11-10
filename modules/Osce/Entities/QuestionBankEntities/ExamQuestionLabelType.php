<?php
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;

/**标签类型模型
 * Class LabelType
 * @package Modules\Osce\Entities\QuestionBankEntities
 */

class ExamQuestionLabelType extends  Model

{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_question_label_type';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['id', 'name','status'];


    //关联标签表
    public function examQuestionLabel (){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel','label_type_id','id');//一对多(参数：关联模型名称，关联模型名称键名，本模型键名)
    }

    /**获取标签类型列表
     * @method
     * @url /osce/
     * @access public
     * @return mixed
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function examQuestionLabelTypeList(){
        $data = $this->select('id','name')->orderBy('created_at','desc')->get();
        return $data;
    }

    /**
     * 标签类型和标签关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function LabelTypeAndLabel()
    {
        return $this    ->  hasMany('\Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel','label_type_id','id');
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
        $builder = $this->with(['LabelTypeAndLabel'=>function($label){
            $label->where('exam_question_label.status','=',1);
        }])->get();
        return $builder;
    }
}


