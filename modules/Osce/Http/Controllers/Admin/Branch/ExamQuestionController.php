<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

class ExamQuestionController extends CommonController
{
    /**获取试题列表
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\View\View|string
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function showExamQuestionList(Request $request)
    {
        $this->validate($request, [
            'examPaperLabelId' => 'sometimes|integer',//试题类型id
            'examQuestionTypeId' => 'sometimes|integer',//题目类型id
        ]);
        $formData['examPaperLabelId'] = $request->input('examPaperLabelId'); //试题类型id
        $formData['examQuestionTypeId'] = $request->input('examQuestionTypeId');//题目类型id

        //获取试题类型列表
        $examQuestionLabelTypeModel= new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();

        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();

        //获取试题列表信息
        $examQuestionModel= new ExamQuestion();
        $data = $examQuestionModel->showExamQuestionList($formData);

        //获取考核范围
        $examQuestionLabelName = array();
        foreach($data as $k1=>$v1) {
            foreach ($v1->ExamQuestionLabelRelation as $k2 => $v2) {
                $examQuestionLabelName[$k1][$k2] = $v2->ExamQuestionLabel['name'];
            }
            $examQuestionLabelName[$k1] = implode(',',$examQuestionLabelName[$k1]);
        }

        $list = [];
        if(count($data) > 0){
            foreach($data as $k=>$item){
                $list[] = [
                    'number'                       => $k+1,//序号
                    'id'                            => $item->id,//试题id
                    'name'                          => $item->name,//试题名称
                    'examQuestionLabelName'      => $examQuestionLabelName[$k],//考核范围
                    'examQuestionTypeName'       => $item->examQuestionTypeName,//题目类型
                ];
            }
        }
        if ($request->ajax()) {
            return $this->success_data([
                    'list'=>$list,
                    'examQuestionLabelTypeList'=>$examQuestionLabelTypeList,
                    'examQuestionTypeList'=>$examQuestionTypeList
                ]);
        }
        return view('osce::admin.resourcemanage.subject_manage', [
            'data'                         =>$data,//对象型数据
            'list'                         =>$list ,//试题列表（数组型数据）
            'examQuestionLabelTypeList' =>$examQuestionLabelTypeList,//试题类型列表
            'examQuestionTypeList'       =>$examQuestionTypeList //题目类型列表
        ]);
    }

    /**打开新增页面
     * @method
     * @url  /osce/admin/examQuestion/examQuestion-add
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestionAdd()
    {
        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();
        //获取考核范围列表（标签类型列表）
        $examQuestionLabelTypeModel = new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();
        foreach($examQuestionLabelTypeList as $k=>$v){
            $examQuestionLabelTypeList[$k]['examQuestionLabelList'] = $v->examQuestionLabel;
        }

        return view('osce::admin.resourcemanage.subject_manage_add', [
            'examQuestionTypeList'       => $examQuestionTypeList, //题目类型列表
            'examQuestionLabelTypeList' => $examQuestionLabelTypeList, //考核范围列表
        ]);
    }

    /**新增试题数据交互
     * @method
     * @url /osce/admin/examQuestion/examQuestion-add
     * @access public
     * @param Request $request
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postExamQuestionAdd(Request $request)
    {
        dd($request->all());
        $this->validate($request, [
            'examQuestionTypeId'    =>'sometimes|integer',//试题表
            'name'                     => 'required|max:32|string',
            'parsing'                 => 'sometimes|max:255|string',
            'answer'                  => 'required|max:32|string',

            'examQuestionItemName'  => 'required|max:32|string',//试题子项表
            'content'                 => 'sometimes|max:255|string',

            'examQuestionLabelId'      =>'sometimes|integer',//试题和标签中间表
        ]);
        //试题表数据
        $examQuestionData =array(
            'exam_question_type_id' =>$request->input('examQuestionTypeId'),//题目类型id
            'name'                     =>$request->input('name'),//题目名称
            'parsing'                 =>$request->input('parsing'),//题目内容解析
            'answer'                  =>$request->input('answer'),//正确答案（a/abc/0,1）
        );
        //试题子项表数据
        $examQuestionItemData = array(
            'name' =>$request->input('examQuestionItemName'),//选项名称:A/B/C/D
            'content' =>$request->input('content'),//选项内容/判断内容
        );
        //试题和标签中间表数据
        $examQuestionLabelRelationData = array(
            'exam_question_label_id' =>$request->input('examQuestionLabelId'),//标签id
        );
        $examQuestionModel= new ExamQuestion();
        $result = $examQuestionModel->addExamQuestion($examQuestionData,$examQuestionItemData,$examQuestionLabelRelationData);

        if($result)
        {
            return redirect()->route('examQuestion.getCustomerList')->with('success','新增成功');
        }
        else
        {
            return back()->with('error','新增失败');
        }
    }

    /**打开编辑页面
     * @method
     * @url /osce/
     * @access public
     * @param $id
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamQuestionEdit(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'sometimes|integer',//试题id
        ]);
        //试题id
        $id = $request->input('id');

        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();

        //获取试题信息
        $examQuestionModel= new ExamQuestion();
        $list = $examQuestionModel->getExamQuestionById(1);

        //获取对应试题子项表列表
        $examQuestionItemList = $list->examQuestionItem;

        //获取标签类型列表
        $examQuestionLabelTypeModel = new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();

        //根据试题信息获取对应的标签列表
        $examQuestionLabelList = $list->ExamQuestionLabelRelation;

        foreach($examQuestionLabelTypeList as $k=>$v){
            $examQuestionLabelTypeList[$k]['examQuestionLabelList'] = $v->examQuestionLabel;//获取标签信息
            $data = [];
            if(!empty($examQuestionLabelList)){
                foreach($examQuestionLabelList as $key => $val){
                    if(!empty($val->ExamQuestionLabel->ExamQuestionLabelType['id'])){
                        if($v['id'] == $val->ExamQuestionLabel->ExamQuestionLabelType['id'] ){
                            $data[] = $val->ExamQuestionLabel;
                        }
                    }
                }
            }
            $examQuestionLabelTypeList[$k]['examQuestionLabelList_'] = $data;
        }


        $datas = [];
        if(count($list) > 0){
            foreach($list as $k=>$item){
                $datas[] = [
                    'number'                      => $k+1,//序号
                    'id'                           => $item->id,//试题id
                    'exam_question_type_id'     => $item->exam_question_type_id,//题目类型
                    'answer'                      => $item->answer,//正确答案
                ];
            }
        }

        return view('osce::admin.statisticalanalysis.statistics_subject_standard', [
            'examQuestionTypeList'       =>$examQuestionTypeList,//题目类型列表
            'data'                          =>$datas ,//试题信息
            'examQuestionItemList'       =>$examQuestionItemList ,//试题子项表列表
            'examQuestionLabelTypeList' =>$examQuestionLabelTypeList ,//考核范围列表
        ]);
    }

    /**保存编辑
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postExamQuestionEdit(Request $request)
    {
        dd($request->all());
        $this->validate($request, [
            'id'                       => 'required|integer',//试题表
            'examQuestionTypeId'    =>'sometimes|integer',
            'name'                     => 'required|max:32|string',
            'parsing'                 => 'sometimes|max:255|string',
            'answer'                  => 'required|max:32|string',

            'examQuestionItemName'  => 'required|max:32|string',//试题子项表
            'content'                 => 'sometimes|max:255|string',

            'examQuestionLabelId'   =>'sometimes|integer',//试题和标签中间表
        ]);
        //试题表数据
        $examQuestionData =array(
            'id'                       =>$request->input('id'),//试题id
            'exam_question_type_id' =>$request->input('examQuestionTypeId'),//题目类型id
            'name'                     =>$request->input('name'),//题目名称
            'parsing'                 =>$request->input('parsing'),//题目内容解析
            'answer'                  =>$request->input('answer'),//正确答案（a/abc/0,1）
        );
        //试题子项表数据
        $examQuestionItemData = array(
            'name' =>$request->input('examQuestionItemName'),//选项名称:A/B/C/D
            'content' =>$request->input('content'),//选项内容/判断内容
        );
        //试题和标签中间表数据
        $examQuestionLabelRelationData = array(
            'exam_question_label_id' =>$request->input('examQuestionLabelId'),//标签id
        );
        $examQuestionModel= new ExamQuestion();
        $result = $examQuestionModel->editExamQuestion($examQuestionData,$examQuestionItemData,$examQuestionLabelRelationData);

        dd($result);
        if($result)
        {
            return redirect()->route('examQuestion.getCustomerList')->with('success','编辑成功');
        }
        else
        {
            return back()->with('error','编辑失败');
        }

    }

    /**删除试题
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function examQuestionDelete(Request $request){
        //验证
        $this->validate($request, [
            'id' => 'required|integer',//试题id
        ]);
        //试题id
        $id = $request->input('id');
        $examQuestionModel= new ExamQuestion();
        $result = $examQuestionModel->deleteExamQuestion($id);
        if($result){
            return response()->json(true);
        }else{
            return response()->json(false);
        }
    }
}