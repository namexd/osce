<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/28
 * Time: 15:52
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Entities\User;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Invigilator;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;
use DB;

class InvigilatorController extends CommonController
{



    public function getTest()
    {
//        return view('osce::admin.exammanage.smart_assignment');
    }
    /**
     * 获取SP考教师列表
     * @url GET /osce/admin/invigilator/invigilator-list
     * @access public
     *
     * @param Request $request get 请求<br><br>
     * <b>get请求字段：</b>
     * * string        type        是否为sp老师(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view {姓名：$item->name,是否为sp老师：$isSpValues[$item->type]}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-28
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilatorList(Request $request){
       $Invigilator    =   new Teacher();

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
        $Invigilator    =   new Teacher();

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
     * * string        name             用户姓名(必须的)
     * * string        mobile           用户手机号(必须的)
     * * string        code             用户工号
     * * string        type             用户类型(必须的)
     * * string        case_id          病例ID
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
            'type'      =>  'required|in:1,3',
            'moblie'    =>  'required',
            'code'      =>  'sometimes',
            'case_id'   =>  'sometimes',
        ],[
            'name.required'     =>  '监考教师姓名必填',
            'type.required'     =>  '监考教师类型必填',
            'type.in'           =>  '监考教师类型不对',
            'moblie.required'   =>  '监考教师手机必填'
        ]);
        $user   =   Auth::user();
        $data   =   [
            'name'              =>  e($request->get('name')),
            'type'              =>  intval($request->get('type')),
            'mobile'            =>  e($request->get('moblie')),
            'code'              =>  e($request->get('code')),
            'case_id'           =>  intval($request->get('case_id')),
            'status'            =>  1,
            'create_user_id'    => $user->id
        ];

        $Invigilator    =   new Teacher();
        try
        {
            if($Invigilator    ->  addInvigilator($data))
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
     * 新增SP老师
     * @url /osce/admin/invigilator/add-sp-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name             用户姓名(必须的)
     * * string        mobile           用户手机号(必须的)
     * * string        code             用户工号(必须的)
     * * string        type             用户类型(必须的)
     * * string        case_id          病例ID(必须的)
     * * string        status           用户状态(必须的)
     * * string        create_user_id   创建人ID(必须的)
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
            'type'      =>  'required|in:2',
            'mobile'    =>  'required',
            'code'      =>  'sometimes',
            'case_id'   =>  'sometimes',
        ],[
            'name.required'     =>  '监考教师姓名必填',
            'type.required'     =>  '监考教师类型必填',
            'type.in'           =>  '监考教师类型不对',
            'mobile.required'   =>  '监考教师手机必填',
            'case_id.required'  =>  '监考教师病例必填'
        ]);
        try
        {
            $user   =   Auth::user();
            $data   =   [
                'name'              =>  e($request->get('name')),
                'type'              =>  intval($request->get('type')),
                'mobile'            =>  e($request->get('mobile')),
                'code'              =>  e($request->get('code')),
                'case_id'           =>  intval($request->get('case_id')),
                'status'            =>  1,
                'create_user_id'    => $user->id
            ];

            $Invigilator    =   new Teacher();
            if($Invigilator    ->  addInvigilator($data))
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
     *
     * @url GET /osce/admin/invigilator/edit-invigilator
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           id               老师信息ID(必须的)
     * * string        name             用户姓名(必须的)
     * * string        mobile           用户手机号(必须的)
     * * string        code             用户工号(必须的)
     * * int           type             用户类型(必须的)
     * * int           case_id          病例ID(必须的)
     * * int           status           用户状态(必须的)
     * * int           create_user_id   创建人ID(必须的)
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

        $InvigilatorModel    =   new Teacher();
        $invigilator    =   $InvigilatorModel    ->  find($id);
        return view('osce::admin.resourcemanage.invigilator_edit',['item'=>$invigilator]);
    }

    /**
     * 编辑sp老师名单
     * @url GET /osce/admin/invigilator/edit-sp-snvigilator
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return View
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditSpInvigilator(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
        ]);
        $id             =   intval($request    ->  get('id'));

        $InvigilatorModel    =   new Teacher();
        $invigilator    =   $InvigilatorModel    ->  find($id);
        $list   =   CaseModel::get();
        return view('osce::admin.resourcemanage.sp_invigilator_edit',['item'=>$invigilator,'list'=>$list]);
    }

    /**
     * 编辑监考老师信息
     * @url POST /osce/admin/invigilator/edit-invigilator
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id               老师信息ID(必须的)
     * * string        name             用户姓名(必须的)
     * * string        mobile           用户手机号(必须的)
     * * string        code             用户工号(必须的)
     * * int           type             用户类型(必须的)
     * * int           case_id          病例ID(必须的)
     * * int           status           用户状态(必须的)
     * * int           create_user_id   创建人ID(必须的)
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
            'mobile' =>  'required',
        ]);
        $id             =   (int)$request    ->  get('id');
        try
        {
            $teacherModel   =   new Teacher();
            $name           =   e($request->get('name'));
            $mobile         =   e($request->get('mobile'));
            $type=Teacher::where('id',$id)->select('type')->first()->type;
            if($teacherModel    ->  editInvigilator($id,$name,$mobile,$type))
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
     * * string        type        老师类型(必须的)
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
            'id'        =>  'required',
            'name'      =>  'required',
            'type'      =>  'required',
            'mobile'    =>  'required',
            'case_id'   =>  'required',
        ]);

        $id                 =   (int)$request    ->  get('id');
        try
        {
            $TeahcerModel   =   new Teacher();
            $name           =   e($request->get('name'));
            $mobile         =   e($request->get('mobile'));
            $caseId         =   intval($request->get('case_id'));

            if($TeahcerModel    ->  editSpInvigilator($id,$name,$mobile,$caseId))
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
            if(!is_null($id))
            {

                    if(!Teacher::where('id',$id)->delete())
                    {
                        throw new \Exception('删除用户失败,请检查次教务人员是否已在在其他考试中已用了');
                    }
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

    /**
     * 导入excel的方法
     * @api GET /osce/admin/invigilator/import
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
    private function postImport($request)
    {
        //获得上传的数据
        $data = Common::getExclData($request,'teacher');
        //去掉sheet
        $invigilatorList = array_shift($data);
        //将中文表头转为英文
        $data = Common::arrayChTOEn($invigilatorList, 'osce.importForCnToEn.teacher');
        //返回数组
        return $data;
    }

    /**
     * 导入老师的数据
     * @api GET /osce/admin/invigilator/import
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        老师ID(必须的)
     *
     * @param Teacher $teacher
     * @return redirect
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-09 16：48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postImportTeacher(Request $request, Teacher $teacher)
    {
        try {
            //导入
            $data = $this->postImport($request);
            //将创建人插入到数组中
            $createUser = Auth::user();
            $data['create_user_id'] = $createUser->id;

            //将数组导入到模型中的addInvigilator方法
            if ($teacher->addInvigilator($data)) {
                throw new \Exception('系统出错，请重试！');
            } else {
                echo json_encode($this->success_data());
            }

        } catch (\Exception $ex) {
            echo json_encode($this->fail($ex));
        }
    }

}