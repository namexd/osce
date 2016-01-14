<?php
/**
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 21:01
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Repositories\Common;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Http\Controllers\CommonController;

class TopicController extends CommonController
{
    /**
     * 获取课题列表
     * @url /osce/admin/topic/list
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList(Request $request){
        $name   =   e($request->get('name'));
        $Subject    =   new Subject;
        $list       =   $Subject    ->  getList($name);
        return view('osce::admin.examination.topicList',['list'=>$list]);
    }

    /**
     * 新增课题（考核点的盒子）
     * @url /osce/admin/topic/add-topic
     * @access public
     *
     * * @param Request $request
     * <b>post 请求字段：</b>
     * * string         title        课题名称(必须的)
     * * int            score        课题总分(必须的)
     * * int            order        课题排序(必须的)
     * * int            status       课题状态(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 21:35
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddTopic(Request $request){
        $this   ->  validate($request,[
            'title'         =>  'required',
            'content'       =>  'required',
            'score'         =>  'required',
            'description'   =>  'sometimes',
        ],[
            'title.required'    =>  '评分标准名称必须',
            'content.required'  =>  '评分标准必须',
            'score.required'    =>  '评分必须',
        ]);

        $content = $request->get('content');
        $score   = $request->get('score');

        $formData = SubjectItem::builderItemData($content, $score);
        $data   =   [
            'title'         =>  e($request  ->  get('title')),
            'description'   =>  e($request  ->  get('description')),
        ];

        $subjectModel   =   new Subject();
        if($subjectModel->  addSubject($data,$formData)){
            return redirect()->route('osce.admin.topic.getList');
        } else{
            return  redirect()->back()->withErrors(new \Exception('新增失败'));
        }
    }

    /**
     * 编辑课题
     * @url /osce/admin/topic/edit-topic
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * int            id           课题ID(必须的)
     * * string         title        课题名称(必须的)
     * * int            score        课题总分(必须的)
     * * int            order        课题排序(必须的)
     * * int            status       课题状态(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditTopic(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
            'title' =>  'required',
            'description' =>  'sometimes',
        ],[
            'id.required'       =>  '课题ID必须',
            'title.required'    =>  '课题名称必须',
        ]);

        $data   =   [
//            'id'            =>  intval($request     ->  get('id')),
            'title'         =>  e($request          ->  get('title')),
            'description'   =>  e($request          ->  get('description')),
        ];
        $id     =   intval($request ->get('id'));

        $subjectModel   =   new Subject();
        try
        {
            $formData   =   SubjectItem::builderItemData($request->get('content'),$request->get('score'));
            if($subjectModel   ->  editTopic($id,$data,$formData))
            {
                return redirect()->route('osce.admin.topic.getList');
            }
            else
            {
                throw new \Exception('编辑失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     * 新增课题表单
     * @url /osce/admin/topic/add-topic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddTopic(){
        return view('osce::admin.resourcemanage.categories');
    }

    /**
     * 编辑考核标准表单
     * @url /osce/admin/topic/edit-topic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditTopic(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required'
        ]);

        $id         =   $request->get('id');
        $subject    =   Subject::find($id);

        $items      =   $subject->items;

        $items      =   SubjectItem::builderItemTable($items);
        return view('osce::admin.resourcemanage.edittopic',['item'=>$subject,'list'=>$items]);
    }

    /**
     *
     * @url /osce/admin/topic/getDelTopic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        id        考核标准ID(必须的)
     *
     * @return
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDelTopic(Request $request){
        $this->validate($request,[
            'id'=>''
        ]);
        $id =   $request->get('id');
        $SubjectModel   =   new Subject();
        $subject =  $SubjectModel->find($id);
        try{
            $SubjectModel   ->  delSubject($subject);
            return redirect()->route('osce.admin.topic.getList');
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     *
     * @url /osce/admin/topic/import-excel
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     *
     *
     * @param Request $request
     * @version 1.0
     * @author jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postImportExcel(Request $request)
    {
        try {
            $data = Common::getExclData($request, 'topic');
            $topicList = array_shift($data);
            //将中文表头，按配置翻译成英文的字段名
            $data = Common::arrayChTOEn($topicList, 'osce.importForCnToEn.standard');
            echo json_encode($this->success_data($data));
        } catch (\Exception $ex) {
            echo json_encode($this->fail($ex));
        }
    }
}