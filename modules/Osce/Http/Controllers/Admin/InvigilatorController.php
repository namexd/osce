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
    /**
     * 获取可监考教师列表
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
    public function getInvigilatorList(Request $request){
        $Invigilator    =   new Invigilator();

        $list       =   $Invigilator    ->getSpInvigilatorList();
        $isSpValues =   $Invigilator    ->  getIsSpValues();

        //return view('',['list'=>$list,'isSpValues'=>$isSpValues]);
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
        //return view();
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
     *  关联老师
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
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28 19:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditInvigilator(Request $request){

    }

    /**
     * 发送预约邀请
     * @url POST /osce/admin/invigilator/send-invitation
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
     * @date 2015-12-28 19:19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postSendInvitation(Request $request){

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

    }
}