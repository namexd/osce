<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamCategoryFormal extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_category_formal';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'name','exam_question_type_name', 'exam_question_type_id','number','score','exam_paper_formal_id'];

    /**
     * 与正式的试题表的关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamQuestionFormal(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionFormal','exam_category_formal_id','id');
    }
}