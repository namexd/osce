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
/**试题模型
 * Class ExamQuestion
 * @package Modules\Osce\Entities
 */
class ExamQuestion extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_question';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_question_type_id', 'name', 'parsing', 'answer'];


    /**获取试题列表的方法
     * @method
     * @url /osce/
     * @access public
     * @param string $formData
     * @return mixed
     * @throws \Exception
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function showExamQuestionList($formData = '')
    {
        $builder = $this;
        if ($formData['examQuestionTypeId']) {
            $builder = $builder->where('exam_question_label.exam_paper_label_id', '=', $formData['examPaperLabelId']);
        }
        if ($formData['examQuestionTypeId']) {
            $builder = $builder->where('exam_question.exam_question_type_id', '=', $formData['examQuestionTypeId']);
        }

        $builder = $builder->leftJoin('exam_question_item', function ($join) {
            $join->on('exam_question.id', '=', 'exam_question_item.exam_question_id');
        })->leftJoin('exam_question_label', function ($join) {
            $join->on('exam_question.id', '=', 'exam_question_label.exam_question_id');
        })->leftJoin('exam_question_type', function ($join) {
            $join->on('exam_question.exam_question_type_id', '=', 'exam_question_type.id');
        })->leftJoin('exam_question_label_type', function ($join) {
            $join->on('exam_question_label.exam_paper_label_id', '=', 'exam_question_label_type.id');
        })->groupBy('exam_question.id')->select([
            'exam_question.id',//试题id
            'exam_question.name',//试题名称
            'label_type.name as labelTypeName',//考核范围
            'exam_question_type.name as examQuestionTypeName',//题目类型
        ]);
        $pageSize = config('page_size');
        return $builder->paginate($pageSize);
    }

    /**删除
     * @method
     * @url /osce/
     * @access public
     * @param $id
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function deleteData($id)
    {
        DB::beginTransaction();
        //删除试题表
        $examQuestion = $this->where('id', '=', $id)->get();
        if (!$examQuestion->isEmpty()) {
            if (!$this::where('id', $id)->delete()) {
                DB::rollback();
                return false;
            }
        }
        //删除试题子项表
        $examQuestionItem = ExamQuestionItem::where('exam_question_id', '=', $id)->get();
        if (!$examQuestionItem->isEmpty()) {
            if (!ExamQuestionItem::where('exam_question_id', $id)->delete()) {
                DB::rollback();
                return false;
            }
        }
        //删除试题和标签中间表
        $examQuestionLabel = ExamQuestionLabel::where('exam_question_id', '=', $id)->get();
        if (!$examQuestionLabel->isEmpty()) {
            if (!ExamQuestionLabel::where('exam_question_id', $id)->delete()) {
                DB::rollback();
                return false;
            }
        }
        DB::commit();
        return true;
    }

    /**新增
     * @method
     * @url /osce/
     * @access public
     * @param $examQuestionData
     * @param $examQuestionItemData
     * @param $examQuestionLabelData
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addExamQuestion($examQuestionData, $examQuestionItemData, $examQuestionLabelData)
    {
        DB::beginTransaction();
        $examQuestion = ExamQuestion::create($examQuestionData);
        if (!$examQuestion instanceof self) {
            DB::rollback();
            return false;
        }
        foreach ($examQuestionItemData as $key => $value) {
            $value['exam_question_id'] = $examQuestion->id;
            if (!$examQuestionItem = ExamQuestionItem::create($value)) {
                DB::rollback();
                return false;
            }
        }
        $examQuestionLabelData['exam_question_id'] = $examQuestion->id;
        if (!$examQuestionLabel = ExamQuestionLabel::create($examQuestionLabelData)) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

    /**编辑页面回显
     * @method
     * @url /osce/
     * @access public
     * @param $examQuestionData
     * @param $examQuestionItemData
     * @param $examQuestionLabelData
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestionById($id)
    {
        $builder = $this;
        $builder = $builder->leftJoin('exam_question_item', function ($join) {
            $join->on('exam_question.id', '=', 'exam_question_item.exam_question_id');
        })->leftJoin('exam_question_label', function ($join) {
            $join->on('exam_question.id', '=', 'exam_question_label.exam_question_id');
        })->leftJoin('exam_question_type', function ($join) {
            $join->on('exam_question.exam_question_type_id', '=', 'exam_question_type.id');
        })->leftJoin('label_type', function ($join) {
            $join->on('exam_question_label.exam_paper_label_id', '=', 'label_type.id');
        });
        $data = $builder->where('exam_question.id','=',$id)
            ->groupBy('exam_question.id')
            ->select([
            'exam_question.id',//试题id
            'exam_question.exam_question_type_id',//题目类型
            'exam_question_item.name as examQuestionItemName',//题目名称
            'exam_question_item.content as examQuestionItemContent',//考核范围
            'exam_question_type.name as examQuestionTypeName',//题目类型
            'exam_question.answer',//正确答案
            'exam_question.parsing',//解析
            'exam_question_label.exam_paper_label_id',//考核范围
        ]);
        return $data;
    }

    /**保存编辑
     * @method
     * @url /osce/
     * @access public
     * @param $examQuestionData
     * @param $examQuestionItemData
     * @param $examQuestionLabelData
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editExamQuestion($examQuestionData, $examQuestionItemData, $examQuestionLabelData)
    {
        DB::beginTransaction();
        $examQuestion = ExamQuestion::where('id', '=', $examQuestionData['id'])->update($examQuestionData);
        if (!$examQuestion) {
            DB::rollback();
            return false;
        }

        foreach ($examQuestionItemData as $key => $value) {
            $examQuestionItem = ExamQuestionItem::where('exam_question_id', '=', $examQuestionData['id'])->update($examQuestionItemData);
            if (!$examQuestionItem) {
                DB::rollback();
                return false;
            }
        }
        $examQuestionLabel = ExamQuestionLabel::where('exam_question_id', '=', $examQuestionData['id'])->update($examQuestionLabelData);
        if (!$examQuestionLabel) {
            DB::rollback();
            return false;
        }

        DB::commit();
        return true;
    }
}