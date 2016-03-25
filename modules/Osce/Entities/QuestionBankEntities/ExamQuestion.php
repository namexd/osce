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

    //试题和标签中间表
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
        //标签类型
        if ($formData['examQuestionLabelTypeId']) {
            //根据标签类型id获取对应的标签id
            $examQuestionLabelModel = new ExamQuestionLabel();
            $examQuestionLabelId = $examQuestionLabelModel->where('label_type_id','=',$formData['examQuestionLabelTypeId'])->select('id')->get()->toArray();
            if(count($examQuestionLabelId)>0){
                $builder = $builder->whereIn('exam_question_label_relation.exam_question_label_id',$examQuestionLabelId);
            }
        }
        //dd($examQuestionLabelId);
        //题目类型
        if ($formData['examQuestionTypeId']) {
            $builder = $builder->where('exam_question.exam_question_type_id', '=', $formData['examQuestionTypeId']);
        }

        $builder = $builder->leftJoin('exam_question_item', function ($join) { //试题子项表
            $join->on('exam_question.id', '=', 'exam_question_item.exam_question_id');

        })->leftJoin('exam_question_label_relation', function ($join) { //试题和标签中间表
            $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');

        })->leftJoin('exam_question_type', function ($join) { //题目类型表
            $join->on('exam_question.exam_question_type_id', '=', 'exam_question_type.id');

        })->groupBy('exam_question.id')->select([
            'exam_question.id',//试题id
            'exam_question.name',//试题名称
            'exam_question_type.name as examQuestionTypeName',//题目类型
            'exam_question_label_relation.exam_question_label_id',//标签id
        ]);
        $pageSize = config('osce.page_size');
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

        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //判断题目类型是否为判断题
            if($examQuestionData['exam_question_type_id']=='4'){//表示为判断题
                //向试题表中插入数据
                $examQuestion['created_user_id'] = Auth::user()->id;
                $examQuestion = ExamQuestion::create($examQuestionData);
                if (!$examQuestion instanceof self) {
                    throw new \Exception(' 插入试题表数据失败！');
                }
                //向试题和标签中间表插入数据
                foreach ($examQuestionLabelRelationData as $key => $value) {
                    $examQuestionLabelRelationInfo['exam_question_id'] = $examQuestion->id;
                    $examQuestionLabelRelationInfo['create_user_id'] = Auth::user()->id;
                    $examQuestionLabelRelationInfo['exam_question_label_id'] = $value;
                    if(!ExamQuestionLabelRelation::create($examQuestionLabelRelationInfo)){
                        throw new \Exception(' 插入试题和标签中间表失败！');
                    }
                }

            }else{
                //向试题表中插入数据
                $examQuestion['created_user_id'] = Auth::user()->id;
                $examQuestion = ExamQuestion::create($examQuestionData);
                if (!$examQuestion instanceof self) {
                    throw new \Exception(' 插入试题表数据失败！');
                }
                //向试题子项表插入数据
                if(!empty($examQuestionItemData)){
                    $data['created_user_id'] = Auth::user()->id;
                    $data['exam_question_id'] = $examQuestion->id;
                    foreach($examQuestionItemData['name'] as $k => $v){
                        $data['name'] = $v;
                        $data['content'] = $examQuestionItemData['content'][$k];
                        if(!ExamQuestionItem::create($data)){
                            throw new \Exception(' 插入试题子项数据失败！');
                        }
                    }
                }

                //向试题和标签中间表插入数据
                foreach ($examQuestionLabelRelationData as $key => $value) {
                    $examQuestionLabelRelationInfo['exam_question_id'] = $examQuestion->id;
                    $examQuestionLabelRelationInfo['created_user_id'] = Auth::user()->id;
                    $examQuestionLabelRelationInfo['exam_question_label_id'] = $value;
                    if(!ExamQuestionLabelRelation::create($examQuestionLabelRelationInfo)){
                        throw new \Exception(' 插入试题和标签中间表失败！');
                    }
                }
            }
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }

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


        $data = $this->where('exam_question.id','=',$id)
            ->select([
            'exam_question.id',//试题id
            'exam_question.exam_question_type_id',//题目类型
            'exam_question.name',//题目
            'exam_question.answer',//正确答案
            'exam_question.parsing',//解析
            ])->first();
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
        $DB = DB::connection('osce_mis');
        $DB->beginTransaction();
        try{
            //修改试题表
            $examQuestions = ExamQuestion::where('id','=',$examQuestionData['id'])->first();
            if($examQuestions){
                if (!ExamQuestion::where('id','=',$examQuestionData['id'])->update($examQuestionData)) {
                    throw new \Exception(' 修改试题表数据失败！');
                }
            }
            //删除试题子项表
            $examQuestionItem = ExamQuestionItem::where('exam_question_id', '=', $examQuestionData['id'])->get();
            if (!$examQuestionItem->isEmpty()) {
                if (!ExamQuestionItem::where('exam_question_id','=',$examQuestionData['id'])->delete()) {
                    throw new \Exception(' 删除试题子项表失败！');
                }
            }
            //删除试题和标签中间表
            $examQuestionLabelRelation = ExamQuestionLabelRelation::where('exam_question_id', '=', $examQuestionData['id'])->get();
            if (!$examQuestionLabelRelation->isEmpty()) {
                if (!ExamQuestionLabelRelation::where('exam_question_id','=', $examQuestionData['id'])->delete()) {
                    throw new \Exception(' 删除试题和标签中间表失败！');
                }
            }
            //判断题目类型是否为判断题
            if($examQuestionData['exam_question_type_id']=='4'){//表示为判断题
                //向试题和标签中间表插入数据
                foreach ($examQuestionLabelRelationData as $key => $value) {
                    $examQuestionLabelRelationInfo['exam_question_id'] = $examQuestionData['id'];
                    $examQuestionLabelRelationInfo['created_user_id'] = Auth::user()->id;
                    $examQuestionLabelRelationInfo['exam_question_label_id'] = $value;
                    if(!ExamQuestionLabelRelation::create($examQuestionLabelRelationInfo)){
                        throw new \Exception(' 插入试题和标签中间表失败！');
                    }
                }
            }else{
                //向试题子项表插入数据
                if(!empty($examQuestionItemData)){
                    $data['created_user_id'] = Auth::user()->id;
                    $data['exam_question_id'] = $examQuestionData['id'];
                    foreach($examQuestionItemData['name'] as $k => $v){
                        $data['name'] = $v;
                        $data['content'] = $examQuestionItemData['content'][$k];
                        if(!ExamQuestionItem::create($data)){
                            throw new \Exception(' 插入试题子项数据失败！');
                        }
                    }
                }
                //向试题和标签中间表插入数据
                foreach ($examQuestionLabelRelationData as $key => $value) {
                    $examQuestionLabelRelationInfo['exam_question_id'] = $examQuestionData['id'];
                    $examQuestionLabelRelationInfo['created_user_id'] = Auth::user()->id;
                    $examQuestionLabelRelationInfo['exam_question_label_id'] = $value;
                    if(!ExamQuestionLabelRelation::create($examQuestionLabelRelationInfo)){
                        throw new \Exception(' 插入试题和标签中间表失败！');
                    }
                }
            }
            $DB->commit();
            return true;
        }catch (\Exception $ex){
            $DB->rollback();
            throw $ex;
        }
    }

//    public function exam_question_label_relation(){
//        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelRelation','exam_question_id','id');
//    }
    /**根据标签查找试题
     * @method
     * @url /osce/
     * @access public
     * @return bool
     * @author weihuiguo <weihuiguo@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestion($data,$pageIndex,$question_type){
        $builder = $this->leftjoin('exam_question_type',function($join){

            $join->on('exam_question_type.id','=','exam_question.exam_question_type_id');

        })->leftjoin('exam_question_label_relation',function($join){

            $join->on('exam_question_label_relation.exam_question_id','=','exam_question.id');

        });
        if(count($data)>0){
            $builder->whereIn('exam_question_label_relation.exam_question_label_id',$data);
        }
        $data = $builder->where('exam_question_type.id','=',$question_type)
            ->with(['ExamQuestionLabelRelation'=>function($ExamQuestionLabelRelation){
                $ExamQuestionLabelRelation->with('ExamQuestionLabel');
            }])
            ->groupBy('exam_question.id')
            ->select(
                'exam_question_type.name as tname',
                'exam_question.*'
            )
            ->paginate(config('msc.page_size'));
        return $data;

/*        $builder = $this->leftjoin('exam_question_type',function($join){

            $join->on('exam_question_type.id','=','exam_question.exam_question_type_id');

        })->with(['ExamQuestionLabelRelation'=>function($relation) use($data){

            $relation->with('exam_question_label');
            if(!empty($data)){
                $relation->whereIn('exam_question_label_relation.exam_question_label_id',$data);
            }

        }])->where('exam_question_type.id','=',$question_type)->select('exam_question_type.name as tname','exam_question.*')->paginate(config('msc.page_size'));//
        return $builder;*/
    }

    //获取试题数量
    public function getQuestionsNum($data){
        //分割标签条件
//        $tag1 = explode('@',$data['tag1']);
//        $tag2 = explode('@',$data['tag2']);
//        $tag3 = explode('@',$data['tag3']);
//        if($tag1){
//            $builder = $this->where('exam_question_label_relation.exam_question_label_id','=',$question_type);
//        }
//        $question_type = $data['question'];
//        $builder = $this->where('exam_question_type_id','=',$question_type)->leftjoin('exam_question_label_relation',function($join){
//            $join->on('exam_question_label_relation.exam_question_id','=','exam_question.id');
//        })->get();
//        dd($builder);
    }
}