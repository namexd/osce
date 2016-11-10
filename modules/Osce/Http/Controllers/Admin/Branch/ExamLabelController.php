<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class ExamLabelController extends CommonController
{

    /**
     * 试卷标签添加验证
     * @method  GET
     * @url /osce/admin/exam/exam-verify
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:38:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postCheckNameOnly(Request $request){
        //dd('验证');
        $this->validate($request,[
            'name'   => 'required',
        ]);

        $ExamQuestionLabelType = new ExamQuestionLabel();
        if(!empty($request['id'])){
            $ExamQuestionLabelTypeList = $ExamQuestionLabelType->where('name','=',$request['name'])->where('id','<>',$request['id'])->first();
        }else{
            $ExamQuestionLabelTypeList = $ExamQuestionLabelType->where('name','=',$request['name'])->first();
        }
        if(!empty($ExamQuestionLabelTypeList->id)){
            die(json_encode(['valid'=>false]));
        }else{
            die(json_encode(['valid'=>true]));
        }

    }



    /**
     * 试卷标签列表展示页
     * @method  GET
     * @url /osce/admin/exam/exam-label
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:38:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamLabel(Request $Request, ExamQuestionLabel $examQuestionLabel)
    {

        $keyWords=Input::get('tagName', '');
        $where['keyword'] =$keyWords;
        $id=Input::get('tagType', '');
        //dd($id);
        $where['id'] = $id;

        $datalist = $examQuestionLabel->getFilteredPaginateList($where);

        //获取标签类型列表
        $ExamQuestionLabelType = new ExamQuestionLabelType();
        $ExamQuestionLabelTypeList = $ExamQuestionLabelType->examQuestionLabelTypeList();

        //TODO 拼凑标签名称
        foreach($datalist as $k=>$v){
            $datalist[$k]['LabelType']=empty($v->ExamQuestionLabelType->name)?'-':$v->ExamQuestionLabelType->name;
        }
            return view('osce::admin.resourcemanage.subject_check_tag',
            [
                'ExamQuestionLabelTypeList' => $ExamQuestionLabelTypeList,
                'datalist' => $datalist,
                'id'=>$id,
                'keyWords'=>$keyWords
            ]);

    }

    /**
     * 新增试卷标签
     * @method  GET
     * @url   /osce/admin/exam/exam-addLabel
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:38:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function addExamQuestionLabel(Request $Request){
        //获取标签类型列表
        $ExamQuestionLabelType = new ExamQuestionLabelType();
        $ExamQuestionLabelTypeList = $ExamQuestionLabelType->examQuestionLabelTypeList();
        return view('osce::admin.resourcemanage.subject_check_tag_add',
            [
                'ExamQuestionLabelTypeList'=>$ExamQuestionLabelTypeList,
            ]);

    }

    /**
     * @methodb post
     * @url /osce/admin/exam/exam-addLabel
     * @access public
     * @return $this
     * @author tangjun <tangjun@misrobot.com>
     * @date 2016年3月8日14:26:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddExamQuestionLabel(Request $Request)
    {
        //dd($Request->all());
        $this->validate($Request, [
            'name' => 'required',
            'label_type_id' => 'required|integer',
            'describe' => 'required'
        ]);
        $data = [
            'name' => Input::get('name'),
            'label_type_id' => Input::get('label_type_id'),
            'describe' => Input::get('describe')
        ];
        $add = ExamQuestionLabel::create($data);
        if ($add != false) {
            return redirect()->back()->with('success', '添加成功');
        } else {
            return redirect()->back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 获取编辑试卷标签内容
     * @method  GET
     * @url   /osce/admin/exam/exam-getLabel
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:39:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditExamQuestionLabel(){
        //dd('获取编辑试卷标签内容');
        $id = e(Input::get('id',''));
        if($id){
            $examQuestionDetail = ExamQuestionLabel::find($id);
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
        $data = [
            'examQuestionDetail' => $examQuestionDetail,
        ];

        return  $this->success_data(['data'=>$data,1,'success']);
    }

    /**
     * 编辑试卷标签内容
     * @method  GET
     * @url  /osce/admin/exam/exam-editLabel
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:49:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editExamQuestionLabelInsert(Request $Request){

        $this->validate($Request, [
            'id'=>'required',
            'name' => 'required',
            'label_type_id' => 'required|integer',
            'describe' => 'required'
        ]);
        $data = [
            'name'=>Input::get('name'),
            'label_type_id'=>Input::get('label_type_id'),
            'describe'=>Input::get('describe')
        ];
        $examTable=new ExamQuestionLabel();
        $add =  $examTable->where('id','=',e(Input::get('id')))->update($data);
        if($add != false){

            return redirect()->back()->with('success', '修改成功');

        }else{
 
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }



    /**
     * 删除试卷标签
     * @method  GET
     * @url   /osce/admin/exam/exam-deleteLabel
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:39:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDeleteExamQuestionLabel(){
        // dd('删除试卷标签');
        $id = e(Input::get('id',''));
        //dd($id);
        $examTable=new ExamQuestionLabel();
        if($id){
            $data =   $examTable->where('id','=',$id)->delete();
            if($data != false){
                return $this->success_data([],1,'success');
            }else{
                return $this->success_data([],0,'fail');
            }
        }else{
                return $this->success_data([],3,'系统异常');
        }
    }

}