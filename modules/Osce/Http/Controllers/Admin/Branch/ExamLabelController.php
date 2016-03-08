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
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionType;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class ExamLabelController extends CommonController
{

    /**
     * 试卷标签列表展示页
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:38:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamLabel(Request $Request,ExamQuestionType $examQuestionType ){
/*        $this->validate($Request,[
            'keyword'=>'required|integer',
            'label_type_id'=>'required|integer',
        ]);*/

        /*   $keyword = !empty(Input::get('keyword'))?Input::get('keyword'):'';
            $id=!empty(Input::get('keyword'))?Input::get('keyword'):'';
            $where['keyword'] = $keyword;
            $where['id']=$id;
            $datalist = $examQuestionType->getFilteredPaginateList($where);


        $ExamQuestionLabelType=new ExamQuestionLabelType();
        $ExamQuestionLabelTypeList= $ExamQuestionLabelType->examQuestionLabelTypeList();*/


        return view('osce::admin.resourcemanage.subject_check_tag',
            [
         /*    'ExamQuestionLabelTypeList'=>$ExamQuestionLabelTypeList,
            'datalist'=>$datalist*/
        ]);

    }
    /**
     * 新增试卷标签
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:38:23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function AddExamQuestionLabel(Request $Request){
        $this->validate($Request,[
               'name'=>'require',
               'label_type_id'=>'required|integer',
               'describe'=>'required'
        ]);
        $data = [
            'name'=>Input::get('name'),
            'label_type_id'=>Input::get('id'),
             'describe'=>Input::get('describe')
            ];
        $add = ExamQuestionLabel::create($data);
        if($data != fasle){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }

    }

    /**
     * 获取编辑试卷标签内容
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:39:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditExamQuestionLabel(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $examQuestionDetail = ExamQuestionLabel::find($id);
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
        $data = [
            'examQuestionDetail' => $examQuestionDetail,
        ];
        //return view('msc::admin.',$data);
    }

    /**
     * 编辑试卷标签内容
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:49:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function EditExamQuestionLabelInsert(Request $Request){

        $this->validate($Request,[
            'id'=>'require',
            'name'=>'require',
            'label_type_id'=>'required|integer',
            'describe'=>'required'
        ]);

        $data = [
            'id'=>\Input::get('id'),
            'name'=>Input::get('name'),
            'label_type_id'=>Input::get('id'),
            'describe'=>Input::get('describe')
        ];
        $add = DB::connection('msc_mis')->table('exam_question_label_type')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if($data != fasle){
            return redirect()->back()->withInput()->withErrors('修改成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }


    /**
     * 删除试卷标签
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日17:39:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDeleteExamQuestionLabel(){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('exam_question_label')->where('id','=',$id)->delete();
            if($data != fasle){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

}