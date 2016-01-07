<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/28
 * Time: 15:52
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Invigilator;
use Modules\Osce\Http\Controllers\CommonController;

class InvigilatorController extends CommonController
{
    public function getTest()
    {
        return view('osce::wechat.exammanage.exam_notice_detail');
    }
    /**
     * 获取SP考教师列表
     * @url GET /osce/admin/invigilator/invigilator-list
     * @access public
     *
     * @param Request $request get 请求<br><br>
     * <b>get请求字段：</b>
     * * string        is_sp        是否为sp老师(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view {姓名：$item->name,是否为sp老师：$isSpValues[$item->is_sp]}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilatorList(Request $request){
       $Invigilator    =   new Invigilator();

        $list       =   $Invigilator    ->getSpInvigilatorList();
        $isSpValues =   $Invigilator    ->  getIsSpValues();
        return view('osce::admin.resourcemanage.sp_invigilator');
    }

    /**
     * 获取普通监考老师列表
     * @api GET /osce/admin/invigilator/invigilator-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getInvigilatorList(){
       /* $Invigilator    =   new Invigilator();

        $list       =   $Invigilator    ->  getInvigilatorList();
        $isSpValues =   $Invigilator    ->  getIsSpValues();*/
        return view('osce::admin.resourcemanage.invigilator');
    }
    /**
     *  新增监考老师 表单显示页面
     * @api GET /osce/admin/invigilator/add-invigilator
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 15:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddInvigilator(Request $request){
        return view('osce::admin.resourcemanage.invigilator_add');
    }

    /**
     * 新增监考老师 提交表单
     * @url POST /osce/admin/invigilator/add-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 15:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddInvigilator(Request $request){
        $this   ->  validate($request,[
            'name'      =>  'required',
            'is_sp'     =>  'required|in:1,2',
        ],[
            'name.required'     =>  '监考教师姓名必填',
            'is_sp.required'    =>  '监考教师类型必填'
        ]);

        $data   =   [
            'name'  =>  e($request->get('name')),
            'is_sp' =>  intval($request->get('is_sp')),
        ];

        $Invigilator    =   new Invigilator();
        try
        {
            if($Invigilator    ->  create($data))
            {
                return redirect()->route('osce.admin.invigilator.getInvigilatorList');
            }
            else
            {
                throw new \Exception('新增失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }

    /**
     *  关联老师（将用户注册的账号和管理员导入的单个监考教师信息进行关联）
     * @url GET /osce/admin/invigilator/relative-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 15:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRelativeInvigilator(Request $request){

    }

    /**
     *
     * @url GET /osce/admin/invigilator/edit-invigilator
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditInvigilator(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
        ]);
        $id             =   intval($request    ->  get('id'));

        $InvigilatorModel    =   new Invigilator();
        $invigilator    =   $InvigilatorModel    ->  find($id);
        //return view('',['item'=>$invigilator]);
    }
    /**
     * 编辑监考老师信息
     * @url GET /osce/admin/invigilator/edit-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28 19:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditInvigilator(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
            'name'  =>  'required',
            'is_sp' =>  'required',
        ]);
        $id             =   (int)$request    ->  get('id');
        try
        {
            $invigilator    =   Invigilator::find($id);
            $data   =   [
                'name'  =>  e($request->get('name')),
                'is_sp' =>  intval($request->get('name')),
            ];
            foreach($data as $feild =>$item)
            {
                $invigilator    ->  $feild  =   $item;
            }
            if($invigilator    ->  save())
            {
                return redirect()->route('osce.admin.invigilator.getInvigilatorList');
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
     * 发送预约邀请
     * @url POST /osce/admin/invigilator/send-invitation
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id               监考老师ID(必须的)
     * * string        osce_id          考试ID(必须的)
     * * string        site_id          考站ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28 19:19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postSendInvitation(Request $request){
        $this   ->  validate($request,[
            'id'        =>  'required',
            'osce_id'   =>  'required',
            'site_id'   =>  'required',
        ]);
        //TODO:罗海华 2015-12-29 18:23 预留 功能方法
        throw new \Exception('等待考场建好之后才能具体实现');
    }

    /**
     * SP老师查看邀请
     * @api GET /osce/admin/invigilator/invitation
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date    2015-12-28 19:19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getInvitation(Request $request){
        //TODO:罗海华 2015-12-29 18:23 预留 功能方法
        throw new \Exception('等待考场建好之后才能具体实现');
    }

    /**
     * SP老师处理邀请
     * @api GET /osce/admin/invigilator/deal-invitation
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28 19:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postDealInvitation(Request $request){
        //TODO:罗海华 2015-12-29 18:23 预留 功能方法
        throw new \Exception('等待考场建好之后才能具体实现');
    }
}