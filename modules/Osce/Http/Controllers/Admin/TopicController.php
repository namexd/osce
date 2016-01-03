<?php
/**
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 21:01
 */

namespace Modules\Osce\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
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
        $Subject    =   new Subject;
        $list       =   $Subject    ->  getList();
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
            'title' =>  'required',
            'description' =>  'sometimes',
        ],[
            'title.required'        =>  '评分标准名称必须',
        ]);
        $formData   =   SubjectItem::builderItemData($request->get('content'),$request->get('score'));

        $data   =   [
            'title'         =>  e($request  ->  get('title')),
            'description'   =>  e($request  ->  get('description')),
        ];
        $subjectModel   =   new Subject();
        if($subjectModel->  addSubject($data,$formData))
        {
            return redirect()->route('osce.admin.topic.getList');
        }
        else
        {
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
            'score' =>  'required',
            'order' =>  'required',
            'status'=>  'required',
        ],[
            'id.required'       =>  '课题ID必须',
            'title.required'    =>  '课题名称必须',
            'score.required'    =>  '课题总分必须',
            'order.required'    =>  '课题排序必须',
            'status.required'   =>  '课题状态必须',
        ]);

        $data   =   [
            'title' =>  e($request  ->  get('title')),
            'score' =>  intval($request  ->  get('score')),
            'order' =>  intval($request  ->  get('order')),
            'status'=>  intval($request  ->  get('status')),
        ];
        $id     =   intval($request ->get('id'));

        $subjectModel   =   new Subject();

        if($subjectModel   ->  editTopic($id,$data))
        {

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
}