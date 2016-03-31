<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionItem;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
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
            'examQuestionLabelTypeId' => 'sometimes|integer',//试题类型id(标签类型)
            'examQuestionTypeId'       => 'sometimes|integer',//题目类型id
        ]);
        $formData['examQuestionLabelTypeId'] = $request->input('examQuestionLabelTypeId'); //试题类型id(2)
        $formData['examQuestionTypeId'] = $request->input('examQuestionTypeId');//题目类型id

        //获取试题类型列表（标签类型）
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
                if(!empty($examQuestionLabelName)&&!empty($examQuestionLabelName[$k1])&&count($examQuestionLabelName[$k1])>2){
                    break;
                }else{
                    $examQuestionLabelName[$k1][] = $v2->ExamQuestionLabel['name'];
                }
            }
            if(!empty($examQuestionLabelName[$k1])&&count($examQuestionLabelName[$k1])>0){
                $examQuestionLabelName[$k1] = implode(',',$examQuestionLabelName[$k1]);
            }

        }
        $list = [];
        if(count($data) > 0){
            foreach($data as $k=>$item){
                $list[] = [
                    'number'                       => $k+1,//序号
                    'id'                            => $item->id,//试题id
                    'name'                          => $item->name,//试题名称
                    'examQuestionLabelName'      => !empty($examQuestionLabelName[$k])?$examQuestionLabelName[$k]:'-',//考核范围
                    'examQuestionTypeName'       => $item->examQuestionTypeName,//题目类型
                ];
            }
        }

        return view('osce::admin.resourceManage.subject_manage', [
            'data'                         =>$data,//对象型数据
            'list'                         =>$list ,//试题列表（数组型数据）
            'examQuestionLabelTypeList' =>$examQuestionLabelTypeList,//试题类型列表
            'examQuestionTypeList'       =>$examQuestionTypeList, //题目类型列表
            'formData'                    =>$formData

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

        return view('osce::admin.resourceManage.subject_manage_add', [
            'examQuestionTypeList'       => $examQuestionTypeList, //题目类型列表
            'examQuestionLabelTypeList' => $examQuestionLabelTypeList, //考核范围列表
        ]);
    }


    /**试题图片上传
     * @method
     * @url /osce/examquestion/examquestion-upload
     * @access public
     * @param Request $request
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postQuestionUpload(Request $request){
        $data   =   [
            'path'  =>  '',
            'name'=>''
        ];
        if ($request->hasFile('file'))
        {
            $file   =   $request->file('file');
            $fileName           =   $file->getClientOriginalName();
            $type = substr($fileName, strrpos($fileName,'.'));
            $status = 1;
            $arr = array(".png",'.jpg');
            if(!in_array($type,$arr)){
                $status = 0;
            }
            if($status){
                $path   =   'osce/question/'.date('Y-m-d').'/'.rand(1000,9999).'/';
                $destinationPath    =   public_path($path);
                $file->move($destinationPath,$fileName);
                $pathReturn    =   '/'.$path.$fileName;
            }
            $data   =   [
                'path'=>$pathReturn,
                'name'=>$fileName,
                'status'=>$status
            ];

        }
        return json_encode(
            $this->success_data($data)
        );
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

        $this->validate($request, [
            'examQuestionTypeId'    =>'sometimes|integer',//试题表
            'name'                     => 'sometimes|string',
            'parsing'                 => 'sometimes|string',
            'answer'                  => 'sometimes|array',
            'judge'                  => 'sometimes|integer',

            'examQuestionItemName'  => 'sometimes|array',//试题子项表
            'content'                 => 'sometimes|array',

            'examQuestionLabelId'      =>'sometimes|array',//试题和标签中间表
        ]);

        //试题和标签中间表数据
        $ExamQuestionLabelRelationData = $request->input('tag');

        //试题表数据
        $examQuestionData =array(
            'exam_question_type_id' =>$request->input('examQuestionTypeId'),//题目类型id
            'name'                     =>$request->input('name'),//题目名称
            'parsing'                 =>$request->input('parsing'),//题目内容解析
            'image'                    =>serialize($request->input('image')),//试题图片
            'imageName'                    =>serialize($request->input('imageName')),//试题图片

        );


        //判断是否为判断题
        if($request->input('examQuestionTypeId')=='4'){
            $examQuestionData['answer'] = $request->input('judge');//正确答案（0-错误，1-正确,）
        }else{
            $examQuestionData['answer'] = implode('@',$request->input('answer'));//正确答案（a/abc/0,1）
        }

        //试题子项表数据
        $examQuestionItemData = array(
            'name' =>$request->input('examQuestionItemName'),//选项名称:A/B/C/D
            'content' =>$request->input('content'),//选项内容/判断内容
        );
        $examQuestionModel= new ExamQuestion();
        $result = $examQuestionModel->addExamQuestion($examQuestionData,$examQuestionItemData,$ExamQuestionLabelRelationData);
        if($result)
        {
            return redirect()->route('osce.admin.ExamQuestionController.showExamQuestionList')->with('success','新增成功');
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
    public function getExamQuestionEdit($id)
    {

        $examQuestionItem = new ExamQuestionItem();
        $content = $examQuestionItem->select('name')->where('exam_question_id','=',$id)->get();
        $newContent =[];
        if($content){
            foreach($content as $v){
                $newContent[]=$v->name;
            }
        }

        //获取题目类型列表
        $examQuestionTypeModel= new ExamQuestionType();
        $examQuestionTypeList = $examQuestionTypeModel->examQuestionTypeList();

        //获取试题信息
        $examQuestionModel= new ExamQuestion();
        $list = $examQuestionModel->getExamQuestionById($id);
        $examQuestionItemList ='';
        $examQuestionLabelList='';
        if($list){
            //获取对应试题子项表列表
            $examQuestionItemList = $list->examQuestionItem;
            //根据试题信息获取对应的标签列表
            $examQuestionLabelList = $list->ExamQuestionLabelRelation;
        }


        //获取标签类型列表
        $examQuestionLabelTypeModel = new ExamQuestionLabelType();
        $examQuestionLabelTypeList = $examQuestionLabelTypeModel->examQuestionLabelTypeList();



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

        $data = [];
        if($list){
            $data['id'] = $list->id;
            $data['exam_question_type_id'] = $list->exam_question_type_id;//题目类型
            $data['name'] = $list->name;//题目名称
            $data['image'] = unserialize($list->image);//题目图片
            $data['imageName'] = unserialize($list->imageName);//图片名称
            $data['parsing'] = $list->parsing;//解析
            if($data['exam_question_type_id']==4){
                $data['answer'] = $list->answer;//正确答案
            }else{
                $data['answer'] = explode('@',$list->answer);//正确答案
            }
        }

        //dd($data);
        $imageInfo = [];
        if($data['image']){
            foreach($data['image'] as $k=>$v){
                $imageInfo[$k]['imagePath']=$v;
                $imageInfo[$k]['imageName']=$data['imageName'][$k];
            }
        }

        //dd($imageInfo);
        //dd($examQuestionLabelTypeList);
        return view('osce::admin.resourceManage.subject_manage_edit', [
            'examQuestionTypeList'       =>$examQuestionTypeList,//题目类型列表
            'data'                          =>$data ,//试题信息
            'examQuestionItemList'       =>$examQuestionItemList ,//试题子项表列表
            'examQuestionLabelTypeList' =>$examQuestionLabelTypeList ,//考核范围列表
            '$newContent'              =>$newContent,
            'imageInfo'              =>$imageInfo,
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
        $this->validate($request, [
            'id'                      =>'sometimes|integer',//试题表
            'examQuestionTypeId'    =>'sometimes|integer',
            'parsing'                 => 'sometimes|string',
            'answer'                  => 'sometimes|array',
            'judge'                  => 'sometimes|integer',
            'examQuestionItemName'  => 'sometimes|array',//试题子项表
            'content'                 => 'sometimes|array',
            'examQuestionLabelId'      =>'sometimes|array',//试题和标签中间表
        ]);

        //试题和标签中间表数据

        $ExamQuestionLabelRelationData = $request->input('tag');


        //试题表数据
        $examQuestionData =array(
            'id'                        =>$request->input('id'),//试题id
            'exam_question_type_id' =>$request->input('examQuestionTypeId'),//题目类型id
            'name'                     =>$request->input('name'),//题目名称
            'parsing'                 =>$request->input('parsing'),//题目内容解析
            'image'                    =>serialize($request->input('image')),//试题图片
            'imageName'                    =>serialize($request->input('imageName')),//试题图片

        );

        //判断是否为判断题
        if($request->input('examQuestionTypeId')=='4'){
            $examQuestionData['answer'] = $request->input('judge');//正确答案（0-错误，1-正确,）
        }else{
            $examQuestionData['answer'] = implode('@',$request->input('answer'));//正确答案（a/abc/0,1）
        }

        //试题子项表数据
        $examQuestionItemData = array(
            'name' =>$request->input('examQuestionItemName'),//选项名称:A/B/C/D
            'content' =>$request->input('content'),//选项内容/判断内容
        );


        $examQuestionModel= new ExamQuestion();
        $result = $examQuestionModel->editExamQuestion($examQuestionData,$examQuestionItemData,$ExamQuestionLabelRelationData);
        if($result)
        {
            return redirect()->route('osce.admin.ExamQuestionController.showExamQuestionList')->with('success','编辑成功');
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