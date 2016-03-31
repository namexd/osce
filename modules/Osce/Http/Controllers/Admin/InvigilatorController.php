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
use Modules\Osce\Entities\ExamSpTeacher;
use Modules\Osce\Entities\Invigilator;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;
use DB;

class InvigilatorController extends CommonController
{
    public function getTest(){
        //return view('osce::admin.statistics_query.exam_vcr');
    }

    /**
     * 获取SP考教师列表
     * @url GET /osce/admin/invigilator/sp-invigilator-list
     * @access public
     *
     * @param Request $request get 请求<br><br>
     * <b>get请求字段：</b>
     * * string        type        是否为sp老师(必须的)
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
        $list       =   $Invigilator    ->getSpInvigilatorInfo();
        $isSpValues =   $Invigilator    ->getIsSpValues();
        return view('osce::admin.resourceManage.staff_manage_invigilator_sp',['list'=>$list,'isSpValues'=>$isSpValues]);
    }

    /**
     * 获取普通监考老师列表
     * @api GET /osce/admin/invigilator/invigilator-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getInvigilatorList(Request $request){
        $type = $request->get('type');
        $Invigilator = new Teacher();
        $list        = $Invigilator -> getInvigilatorList((empty($type) || $type==1)?1:3);

        return view('osce::admin.resourceManage.staff_manage_invigilator',['list'=>$list,'type'=>(empty($type) || $type==1)?1:3]);
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
        return view('osce::admin.resourceManage.staff_manage_invigilator_add');
    }

    /**
     *  新增巡考老师 表单显示页面
     * @api GET /osce/admin/invigilator/add-patrol
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2015-03-30 15:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function getAddPatrol(Request $request){
        return view('osce::admin.resourceManage.staff_manage_invigilator_patrol_add');
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
        $list   =   Subject::get();
        return view('osce::admin.resourceManage.staff_manage_invigilator_sp_add',['list'=>$list]);
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
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'email'         =>  'required',
            'code'          =>  'required',
//            'subject'       =>  'required',
            'images_path'   =>  'required',
            'description'   =>  'sometimes',
        ],[
            'name.required'         =>  '监考教师姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'email.required'        =>  '邮箱必填',
            'code.required'         =>  '监考教师编号必填',
//            'subject.required'      =>  '考试项目必选',
            'images_path.required'  =>  '请上传照片',
        ]);

        $user   =   Auth::user();
        if(empty($user)){
            throw new \Exception('未找到当前操作人信息');
        }
        //用户数据
        $userData = $request -> only('name', 'gender','idcard','mobile','email','code');
        $userData['avatar'] = $request  ->  get('images_path')[0];  //照片
        //老师数据
        $teacherData = $request -> only('name','code','description');  //姓名、编号、类型、备注
        if($request->get('type')==1){
            if(is_null($request->get('subject'))){
                throw new \Exception('考试项目必选');
            }
            //从配置中获取角色对应的ID号, 考官角色默认为1
            $role_id = config('osce.invigilatorRoleId',1);
        }else{
            //从配置中获取角色对应的ID号, 考官角色默认为1
            $role_id = config('osce.invigilatorRoleId',3);
        }
        $teacherData['type']            = $request->get('type');
        $teacherData['case_id']         = null;
        $teacherData['status']          = 1;
        $teacherData['create_user_id']  = $user->id;

        //获取支持的考试项目
        $subjects = $request->get('subject');

//        //从配置中获取角色对应的ID号, 考官角色默认为1
//        $role_id = config('osce.invigilatorRoleId',1);

        $Invigilator    =   new Teacher();
        try{
            if($Invigilator ->  addInvigilator($role_id, $userData , $teacherData, $subjects)){
                return redirect()->route('osce.admin.invigilator.getInvigilatorList');
            } else{
                throw new \Exception('新增失败');
            }
        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
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
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'email'         =>  'required',
            'type'          =>  'required|in:2',
            'code'          =>  'required',
            'images_path'   =>  'required',
            'case_id'       =>  'sometimes',
            'description'   =>  'sometimes',
            'subject'       =>  'sometimes'
        ],[
            'name.required'         =>  '监考教师姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'email.required'        =>  '邮箱必填',
            'type.required'         =>  '监考教师类型必填',
            'code.required'         =>  '监考教师编号必填',
            'type.in'               =>  '监考教师类型不对',
            'images_path.required'  =>  '请上传照片',
            'case_id.required'      =>  '监考教师病例必填'
        ]);
        try{
            $user   =   Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            //用户数据
            $userData = $request -> only('name', 'gender', 'idcard', 'mobile', 'email','code');
            $userData['avatar'] = $request  ->  get('images_path')[0];  //照片
            //老师数据
            $teacherData = $request -> only('name','code','type','description');      //姓名、编号、类型、备注
            $teacherData['case_id']         = intval($request->get('case_id'));
            $teacherData['status']          = 0;
            $teacherData['create_user_id']  = $user->id;
            $role_id = config('osce.spRoleId',4);
            //获取支持的考试项目
            $subjects = $request->get('subject');
            $Invigilator    =   new Teacher();
            if($Invigilator ->  addInvigilator($role_id, $userData , $teacherData,$subjects)){
                return redirect()->route('osce.admin.invigilator.getSpInvigilatorList');
            } else{
                throw new \Exception('新增失败');
            }
        } catch(\Exception $ex){
            return redirect()->back()->withErrors($ex->getMessage());
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
        $id         =   intval($request    ->  get('id'));
        $teacher    =   new Teacher();
        $invigilator=   $teacher -> find($id);
        
        if(!$invigilator){
            throw  new \Exception('没有找到对应老师');
        }
        if($invigilator->type==3){
            
            return view('osce::admin.resourceManage.staff_manage_invigilator_patrol_edit',['item'=>$invigilator]);
        }
        
        $subjects   =   TeacherSubject::where('teacher_id','=',$id)
                        ->leftJoin('teacher', 'teacher.id', '=', 'teacher_subject.teacher_id')
                        ->leftJoin('subject', 'subject.id', '=', 'teacher_subject.subject_id')
                        ->select(['teacher_subject.teacher_id', 'teacher_subject.subject_id',
                                  'teacher.name as teacher_name', 'subject.title as subject_name'])
                        ->get();
        

        return view('osce::admin.resourceManage.staff_manage_invigilator_edit',['item'=>$invigilator, 'subjects'=>$subjects]);
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
        //查询出关联的科目

        $teacher    =   new Teacher();

        $invigilator=   $teacher -> find($id);

   
        $subjects   =   TeacherSubject::where('teacher_id','=',$id)->get();
        return view('osce::admin.resourceManage.staff_manage_invigilator_sp_edit',['item'=>$invigilator,'subject'=>$subjects]);
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
    public function postEditInvigilator(Request $request)
    {
        $this   ->  validate($request,[
            'id'            =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'email'         =>  'required',
            'code'          =>  'required',
//            'subject'       =>  'required',
            'images_path'   =>  'required',
            'description'   =>  'sometimes',
        ],[
            'images_path.required'  => '请上传头像',
            'subject.required'      => '考试项目必选',
        ]);
        $id             =   (int)$request    ->  get('id');
        //用户数据
        $userData = $request -> only('name', 'gender','idcard','mobile','email','code');
        $userData['avatar'] = $request  ->  get('images_path')[0];  //照片
        //老师数据
        $teacherData = $request -> only('name','code','description');  //姓名、编号、类型、备注

        if($request->get('type')==1){
            if(is_null($request->get('subject'))){
                throw new \Exception('考试项目必选');
            }
        }

        $subjects    = $request -> get('subject');      //获取考试项目

        try{
            $teacherModel   =   new Teacher();

            if($result = $teacherModel ->  editInvigilator($id, $userData, $teacherData, $subjects))
            {
                return redirect()->route('osce.admin.invigilator.getInvigilatorList');
            } else{
                throw new \Exception('编辑失败');
            }

        } catch(\Exception $ex){
            if($ex->getCode()==23000){
                return redirect()->back()->withErrors(['这个号码已有过关联，不能修改']);
            }
            return redirect()->back()->withErrors($ex->getMessage());
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
    public function postEditSpInvigilator(Request $request)
    {
        $this   ->  validate($request,[
            'id'            =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'email'         =>  'required',
//            'type'          =>  'required|in:2',
            'code'          =>  'required',
            'images_path'   =>  'required',
//            'case_id'       =>  'required',
            'description'   =>  'sometimes',
        ],[
            'images_path.required'  => '请上传头像',
        ]);

        $id                 =   (int)$request    ->  get('id');
        //用户数据
        $userData = $request -> only('name', 'gender','idcard','mobile','email','code');

        $userData['avatar'] = $request  ->  get('images_path')[0];  //照片
        //老师数据
        $teacherData = $request -> only('name','code','case_id','description');  //姓名、编号、类型、病例
        $subjects    = $request -> get('subject');
//        try{
            $TeahcerModel   =   new Teacher();

            if($TeahcerModel    ->  editSpInvigilator($id, $userData, $teacherData, $subjects))
                
            {
                return redirect()->route('osce.admin.invigilator.getSpInvigilatorList');
            } else{
                throw new \Exception('编辑失败');
            }
//
//        } catch(\Exception $ex){
//            if($ex->getCode()==23000){
//                return redirect()->back()->withErrors(['这个号码已有过关联，不能修改']);
//            }
//            return redirect()->back()->withErrors($ex->getMessage());
//        }
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
    public function postDelInvitation(Request $request){
        $id             =   $request    ->  get('id');

        try{
            if(!is_null($id))
            {
                if(StationTeacher::where('user_id', $id)->first() || ExamSpTeacher::where('teacher_id',$id)->first()){
                    throw new \Exception('该老师已被关联，无法删除！');
                }

                if(!Teacher::where('id',$id)->delete()){

                    throw new \Exception('删除老师失败，请重试！');
                }
                return $this->success_data('删除成功！');
            } else {
                throw new \Exception('没有找到该老师的相关信息');
            }
        }
        catch(\Exception $ex){
            return $this->fail($ex);
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

    /**
     * 查询老师是否已经存在(监,巡考老师,sp老师) 接口
     * @api GET /osce/admin/invigilator/postSelectTeacher
     *
     */
    public function postSelectTeacher(Request $request){
        $this->validate($request,[
            'mobile'    =>  'required'
        ]);
        $mobile = $request  ->get('mobile');
        $id     = $request  ->get('id');

        //存在ID，为编辑时验证
        if(empty($id)){
            $user = User::where('username', $mobile)->orWhere('mobile', $mobile)->get();
        }else{
            $user = User::where('id', '<>', $id)
                ->where(function ($query) use ($mobile){
                    $query  ->orWhere('username', $mobile)
                            ->orWhere('mobile', $mobile);
                })
                ->get();
        }
        if($user){
            foreach ($user as $item) {
                $result = Teacher::where('id', $item->id)->first();
                if($result){
                    return json_encode(['valid' =>false]);
                }
            }
        }
        return json_encode(['valid' =>true]);
    }

    /**
     * 判断教师编号是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postCodeUnique(Request $request)
    {
        $this->validate($request, [
            'code'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $code   = $request  -> get('code');
        //实例化模型
        $model =  new Teacher();
        //查询 该编号 是否存在
        if(empty($id)){
            $result = $model->where('code', $code)->first();
        }else{
            $result = $model->where('code', $code)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }
    /**
     * 判断身份证号是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postIdcardUnique(Request $request)
    {
        $this->validate($request, [
            'idcard'      => 'required',
        ]);

        $id     = $request  -> get('id');
        $idcard = $request  -> get('idcard');
        //实例化模型
        $model =  new User();
        //查询 该身份证号 是否存在
        if(empty($id)){
            $result = $model->where('idcard', $idcard)->first();
        }else{
            $result = $model->where('idcard', $idcard)->where('id', '<>', $id)->first();
        }
        if($result){
            return json_encode(['valid' =>false]);
        }else{
            return json_encode(['valid' =>true]);
        }
    }

    /**
     * Excel导入监、巡考老师
     * @api POST /osce/admin/exam/import-teachers
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME} 2016-03-21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postImportTeachers(Request $request, Teacher $teacher)
    {
        try {
            $this->validate($request,[
               'type'   => 'required|integer'
            ]);
            $type   =   $request->get('type');
            $user   =   Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }

            //获得上传的数据
            $data   = Common::getExclData($request, 'teacher');
            //去掉sheet
            $teacherList = array_shift($data);
            //判断模板 列数、表头是否有误
            $teacher->judgeTemplet($teacherList, config('osce.importForCnToEn.teacher'));
            //将中文表头转为英文
            $teacherData = Common::arrayChTOEn($teacherList, 'osce.importForCnToEn.teacher');
            $result = $teacher->importTeacher($teacherData, $user);
            if(!$result){
                throw new \Exception('老师导入数据失败，请参考模板修改后重试');
            }

            return json_encode($this->success_data([], 1, "成功导入{$result}个老师！"));

        } catch (\Exception $ex) {
            return json_encode($this->fail($ex));
        }
    }
    /**
     * 下载学生导入模板
     * @url GET /osce/admin/invigilator/download-teacher-improt-tpl
     * @access public
     *
     * @return void
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2015-03-21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getdownloadTeacherImprotTpl(){
        $this->downloadfile('teacher.xlsx',public_path('download').'/teacher.xlsx');
    }
    private function downloadfile($filename,$filepath){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }

    /**
     * 异步获取 所有考试项目
     * @author Zhoufuxiang 2016-3-30
     * @return string
     */
    public function getSubjects(){
        try{
            $data = Subject::all();

            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        }catch (\Exception $ex){
            return $this->fail($ex);
        }
    }

}