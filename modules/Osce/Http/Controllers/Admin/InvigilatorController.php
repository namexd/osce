<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/28
 * Time: 15:52
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Invigilator;
use Modules\Osce\Http\Controllers\CommonController;

class InvigilatorController extends CommonController
{
    public function getTest()
    {
        return view('osce::admin.exammanage.sp_invitation');
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
        return view('osce::admin.resourcemanage.sp_invigilator',['list'=>$list,'isSpValues'=>$isSpValues]);
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
        $Invigilator    =   new Invigilator();

        $list       =   $Invigilator    ->  getInvigilatorList();
        $isSpValues =   $Invigilator    ->  getIsSpValues();

        return view('osce::admin.resourcemanage.invigilator',['list'=>$list,'isSpValues'=>$isSpValues]);
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
     * sp老师新增
     * @api GET /osce/admin/invigilator/add-sp-invigilator
     * @access public
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddSpInvigilator(Request $request){
        $list   =   CaseModel::get();
        return view('osce::admin.resourcemanage.sp_invigilator_add',['list'=>$list]);
    }
    /**
     * 新增监考老师 提交表单
     * @url POST /osce/admin/invigilator/add-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           老师信息ID(必须的)
     * * string        name         老师名称(必须的)
     * * string        is_sp        老师类型(必须的)
     * * string        moblie       老师手机号(必须的)
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
            'moblie'     =>  'required',
        ],[
            'name.required'     =>  '监考教师姓名必填',
            'is_sp.required'    =>  '监考教师类型必填',
            'moblie.required'    =>  '监考教师手机必填'
        ]);

        $data   =   [
            'name'  =>  e($request->get('name')),
            'is_sp' =>  intval($request->get('is_sp')),
            'moblie' =>  e($request->get('moblie')),
        ];

        $Invigilator    =   new Invigilator();
        try
        {
            if($Invigilator    ->  create($data))
            {
                return redirect()->route('osce.admin.invigilator.getSpInvigilatorList');
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
     * 新增SP老师
     * @url /osce/admin/invigilator/add-sp-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           老师信息ID(必须的)
     * * string        name         老师名称(必须的)
     * * string        is_sp        老师类型(必须的)
     * * string        moblie       老师手机号(必须的)
     * * string        case_id      老师病例(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddSpInvigilator(Request $request){
        $this   ->  validate($request,[
            'name'      =>  'required',
            'is_sp'     =>  'required|in:1,2',
            'moblie'     =>  'required',
            'case_id'     =>  'required',
        ],[
            'name.required'     =>  '监考教师姓名必填',
            'is_sp.required'    =>  '监考教师类型必填',
            'moblie.required'    =>  '监考教师手机必填',
            'case_id.required'    =>  '监考教师病例必填'
        ]);

        $data   =   [
            'name'  =>  e($request->get('name')),
            'is_sp' =>  intval($request->get('is_sp')),
            'moblie' =>  e($request->get('moblie')),
            'case_id' =>  intval($request->get('case_id')),
        ];

        $Invigilator    =   new Invigilator();
        try
        {
            if($Invigilator    ->  create($data))
            {
                return redirect()->route('osce.admin.invigilator.getSpInvigilatorList');
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
     * * int        id        老师信息ID(必须的)
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
        return view('osce::admin.resourcemanage.invigilator_edit',['item'=>$invigilator]);
    }
    public function getEditSpInvigilator(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
        ]);
        $id             =   intval($request    ->  get('id'));

        $InvigilatorModel    =   new Invigilator();
        $invigilator    =   $InvigilatorModel    ->  find($id);
        $list   =   CaseModel::get();
        return view('osce::admin.resourcemanage.sp_invigilator_edit',['item'=>$invigilator,'list'=>$list]);
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
            'moblie' =>  'required',
        ]);
        $id             =   (int)$request    ->  get('id');
        try
        {
            $invigilator    =   Invigilator::find($id);
            $data   =   [
                'name'  =>  e($request->get('name')),
                'is_sp' =>  intval($request->get('is_sp')),
                'moblie'=>  e($request->get('moblie')),
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
     *
     * @api GET /osce/admin/invigilator/edit-sp-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id           老师信息ID(必须的)
     * * string        name         老师名称(必须的)
     * * string        is_sp        老师类型(必须的)
     * * string        moblie       老师手机号(必须的)
     * * string        case_id      老师病例(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditSpInvigilator(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
            'name'  =>  'required',
            'is_sp' =>  'required',
            'moblie' =>  'required',
            'case_id' =>  'required',
        ]);

        $id             =   (int)$request    ->  get('id');
        try
        {
            $invigilator    =   Invigilator::find($id);
            $data   =   [
                'name'  =>  e($request->get('name')),
                'is_sp' =>  intval($request->get('is_sp')),
                'moblie'=>  e($request->get('moblie')),
                'case_id'=>  intval($request->get('case_id')),
            ];
            foreach($data as $feild =>$item)
            {
                $invigilator    ->  $feild  =   $item;
            }
            if($invigilator    ->  save())
            {
                return redirect()->route('osce.admin.invigilator.getSpInvigilatorList');
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

    /**
     * 删除监考老师
     * @api GET /osce/admin/invigilator/del-invitation
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        老师ID(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-06 10：55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDelInvitation(Request $request){
        $id             =   $request    ->  get('id');
        try{
            $invigilator    =   Invigilator::find($id);
            if(!is_null($invigilator))
            {
                $invigilator    ->  delete();
                return redirect()->back();
            }
            else
            {
                throw new \Exception('没有找到该老师的相关信息');
            }
        }
        catch(\Exception $ex){
            return redirect()->back()->withErrors($ex);
        }
    }
}