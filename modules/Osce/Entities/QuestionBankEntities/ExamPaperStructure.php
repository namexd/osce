<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 13:56
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamQuestionPaperStructure extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_structure';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_paper_id', 'exam_question_type_id','num','score','total_score'];

    /**
     * 与试卷构造表和试题标签关联表的关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamQuestionPaperStructureLabel(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionPaperStructureLabel','exam_paper_structure_id','id');
    }

    /**
     * 与试卷构造和试题关系表的关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamQuestionPaperStructureQuestion(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionPaperStructureQuestion','exam_paper_structure_id','id');
    }

}