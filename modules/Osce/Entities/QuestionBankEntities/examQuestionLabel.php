<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 15:03
 */

namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\QuestionBankEntities\LabelType;
class examQuestionLabel extends  Model
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_question_label';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['label_type_id', 'name','describe','status'];

    //标签类型
    public function LabelType(){

        return $this->hasOne('Modules\Osce\Entities\QuestionBankEntities\LabelType','id','label_type_id');
    }


}