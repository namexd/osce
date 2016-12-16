<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@sulida.com>
 * @date 2016年3月9日11:02:12
 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamPaperStructureQuestion extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_structure_question';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_paper_structure_id', 'exam_question_id'];


}