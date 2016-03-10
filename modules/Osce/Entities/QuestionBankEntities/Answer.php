<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use DB;

/**考生答题时，正式试卷模型
 * Class Answer
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class Answer extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_formal';//正式的试卷表
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'status', 'exam_paper_id','length','name','total_score','created_user_id','created_at','updated_at'];

    /**正式试卷信息列表
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFormalPaper()
    {
        $DB = \DB::connection('osce_mis');
        $builder = $this;
        $builder = $builder->leftJoin('exam_category_formal', function ($join) { //正式试题分类表
            $join->on('exam_paper_formal.id', '=', 'exam_category_formal.exam_paper_formal_id');

        })->leftJoin('exam_question_type',function($join){ //题目类型表
            $join->on('exam_category_formal.exam_question_type_id', '=', 'exam_question_type.id');

        })->leftJoin('exam_question_formal',function($join){ //正式的试题表
            $join->on('exam_category_formal.id', '=', 'exam_question_formal.exam_category_formal_id');

        })->select([
            'exam_paper_formal.name',//试卷名称
            'exam_paper_formal.length',//考试时间
            'exam_paper_formal.total_score as totalScore',//试卷总分
            'exam_category_formal.exam_question_type_id as examQuestionTypeId',//试题类型id
            'exam_category_formal.name as typeName',//试题类型名称
            'exam_category_formal.score',//单个试题分值
            'exam_question_formal.name as questionName',//试题名称
            'exam_question_formal.content',//试题内容
            'exam_question_formal.answer',//试题答案
        ]);
        return $builder->get();
    }




























}