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


    //试题子项表
    public function examQuestionItem (){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionItem','exam_question_id','id');//一对多(参数：关联模型名称，关联模型名称键名，本模型键名)
    }

    //关联标签表
    public function ExamQuestionLabelRelation (){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelRelation','exam_question_id','id');//一对多(参数：关联模型名称，关联模型名称键名，本模型键名)
    }

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

        $builder = $builder->leftJoin('exam_question_item', function ($join) { //试题子项表
            $join->on('exam_question.id', '=', 'exam_question_item.exam_question_id');

        })->leftJoin('exam_question_label_relation', function ($join) { //试题和标签中间表
            $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');

        })->leftJoin('exam_question_type', function ($join) { //题目类型表
            $join->on('exam_question.exam_question_type_id', '=', 'exam_question_type.id');

        })/*->leftJoin('exam_question_label_type', function ($join) { //标签类型表
            $join->on('exam_question_label_relation.exam_paper_label_id', '=', 'exam_question_label_type.id');

        })*/->groupBy('exam_question.id')->select([
            'exam_question.id',//试题id
            'exam_question.name',//试题名称
           // 'exam_question_label_type.name as examQuestionlabelTypeName',//考核范围
            'exam_question_type.name as examQuestionTypeName',//题目类型
        ]);

        $pageSize = config('page_size');
        return $builder->paginate($pageSize);
    }

    /**删除试题
     * @method
     * @url /osce/
     * @access public
     * @param $id
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function deleteExamQuestion($id)
    {
        DB::beginTransaction();
        //查询试题表中是否有对应的数据
        $examQuestion = $this->where('id', '=', $id)->get();
        if (!$examQuestion->isEmpty()) {

            //删除试题子项表
            $examQuestionItem = ExamQuestionItem::where('exam_question_id', '=', $id)->get();

            if (!$examQuestionItem->isEmpty()) {
                if (!ExamQuestionItem::where('exam_question_id','=',$id)->delete()) {
                    DB::rollback();
                    return false;
                }
            }
            //删除试题和标签中间表
            $examQuestionLabelRelation = ExamQuestionLabelRelation::where('exam_question_id', '=', $id)->get();
            if (!$examQuestionLabelRelation->isEmpty()) {
                if (!ExamQuestionLabelRelation::where('exam_question_id','=', $id)->delete()) {
                    DB::rollback();
                    return false;
                }
            }
            //删除试题表
            if (!$this::where('id','=',$id)->delete()) {
                DB::rollback();
                return false;
            }
        }
        DB::commit();
        return true;
    }

    /**新增数据交互
     * @method
     * @url /osce/
     * @access public
     * @param $examQuestionData 试题表数据
     * @param $examQuestionItemData 试题子项表数据
     * @param $examQuestionLabelRelationData 试题和标签中间表数据
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addExamQuestion($examQuestionData, $examQuestionItemData, $examQuestionLabelRelationData)
    {
        DB::beginTransaction();
        //向试题表中插入数据
        $examQuestion['create_user_id'] = Auth::user()->id;
        $examQuestion = ExamQuestion::create($examQuestionData);
        if (!$examQuestion instanceof self) {
            DB::rollback();
            return false;
        }

        //向试题子项表插入数据
        foreach ($examQuestionItemData as $key => $value) {
            $value['create_user_id'] = Auth::user()->id;
            $value['exam_question_id'] = $examQuestion->id;
            if (!$examQuestionItem = ExamQuestionItem::create($value)) {
                DB::rollback();
                return false;
            }
        }
        //向试题和标签中间表插入数据
        foreach ($examQuestionLabelRelationData as $key => $value) {
            $examQuestionLabelRelationData['exam_question_id'] = $examQuestion->id;
            $examQuestionLabelRelationData['create_user_id'] = Auth::user()->id;
            if (!$examQuestionLabelRelation = ExamQuestionLabelRelation::create($examQuestionLabelRelationData)) {
                DB::rollback();
                return false;
            }
        }
        DB::commit();
        return true;
    }

    /**编辑页面回显
     * @method
     * @url /osce/
     * @access public
     * @param $id 试题表id
     * @return mixed
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestionById($id)
    {
        $builder = $this;
        $builder = $builder->leftJoin('exam_question_item', function ($join) {//试题子项表
            $join->on('exam_question.id', '=', 'exam_question_item.exam_question_id');

        })->leftJoin('exam_question_label_relation', function ($join) {//试题和标签中间表
            $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');

        })->leftJoin('exam_question_type', function ($join) {//题目类型表
            $join->on('exam_question.exam_question_type_id', '=', 'exam_question_type.id');
        });
        $data = $builder->where('exam_question.id','=',$id)
            ->groupBy('exam_question.id')
            ->select([
            'exam_question.id',//试题id
            'exam_question.exam_question_type_id',//题目类型
            'exam_question.name',//题目
            'exam_question_item.name as examQuestionItemName',//选项名称
            'exam_question_item.content as examQuestionItemContent',//选项内容
            'exam_question.answer',//正确答案
            'exam_question.parsing',//解析
            'exam_question_label_relation.exam_paper_label_id',//考核范围
        ]);
        return $data;
    }

    /**保存编辑
     * @method
     * @url /osce/
     * @access public
     * @param $examQuestionData 试题表数据
     * @param $examQuestionItemData 试题子项表数据
     * @param $examQuestionLabelRelationData 试题和标签中间表数据
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editExamQuestion($examQuestionData, $examQuestionItemData, $examQuestionLabelRelationData)
    {
        DB::beginTransaction();
        $examQuestion = ExamQuestion::where('id', '=', $examQuestionData['id'])->update($examQuestionData);
        if (!$examQuestion) {
            DB::rollback();
            return false;
        }

        foreach ($examQuestionItemData as $key => $value) {
            $examQuestionItem = ExamQuestionItem::where('exam_question_id', '=', $examQuestionData['id'])->update($value);
            if (!$examQuestionItem) {
                DB::rollback();
                return false;
            }
        }
        foreach ($examQuestionLabelRelationData as $key => $value) {
            $examQuestionLabelRelation = ExamQuestionLabelRelation::where('exam_question_id', '=', $examQuestionData['id'])->update($examQuestionLabelRelationData);
            if (!$examQuestionLabelRelation) {
                DB::rollback();
                return false;
            }
        }


        DB::commit();
        return true;
    }

    /**保存编辑
     * @method
     * @url /osce/
     * @access public
     *
     * @param $examQuestionData 试题表数据
     * @param $examQuestionItemData 试题子项表数据
     * @param $examQuestionLabelRelationData 试题和标签中间表数据
     * @return bool
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function exam_question_label_relation(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelRelation','exam_question_id','id');
    }

    /**根据标签查找试题
     * @method
     * @url /osce/
     * @access public
     * @return bool
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestion($data){
        $_GET['currentPage'] = 2;
        $builder = $this->leftjoin('exam_question_type',function($join){
            $join->on('exam_question_type.id','=','exam_question.exam_question_type_id');
        })->with(['exam_question_label_relation'=>function($relation) use($data){
            $relation->with('exam_question_label')->whereIn('exam_question_label_relation.exam_question_label_id',$data);
        }])->paginate(config('msc.page_size'));
        return $builder;
    }
}