<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2016�?3�?9�?11:02:12
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamPaperStructureLabel extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_structure_label';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'label_type_id', 'exam_question_label_id','exam_paper_structure_id','relation','created_user_id'];


}