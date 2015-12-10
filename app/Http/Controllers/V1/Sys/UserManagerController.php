<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/10
 * Time: 13:52
 */

namespace App\Http\Controllers\V1\Sys;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesBorrowing;
use Modules\Msc\Entities\ResourcesCate;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Entities\ResourcesLocation;
use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use DB;
use App\Http\Controllers\V1\ApiBaseController;


class UserManagerController extends ApiBaseController
{
    /**
     * 获取用户列表
     * @api GET /api/1.0/private/admin/user/user-list
     * @access public
     *
     * @return json {'validated':验证状态，box：所属科室/班级，id:用户Id,username:用户名，avatar:头像}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-10 15:46
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUserList(){
        $usrModel=new User();
        $pagination=$usrModel->paginate(20);
        $collection=$pagination->items();
        $userProfileList=$usrModel->getUserProfileByIds($usrModel->getUsrIds($collection));
        $paginationArray=$pagination->toArray();
        foreach($paginationArray['data'] as &$itemData){
            $itemData['code']=$userProfileList[$itemData['id']]->code;
            $thisPorfile=$userProfileList[$itemData['id']];
            if($thisPorfile->getTable()=='student')
            {
                $gradeName=$thisPorfile->grade;
                $class=$thisPorfile->className;
                $className='-';
                if(!is_null($class))
                {
                    $className=$class->name;
                }
                $boxName=$gradeName.$className;
                $validated=$thisPorfile->validated;
            }
            else
            {
                $dept=$thisPorfile->dept;
                $deptName='-';
                if(!is_null($dept))
                {
                    $deptName=$dept->name;
                }
                $boxName=$deptName;
                $validated=$thisPorfile->validated;
            }
            $itemData['box']=$boxName;
            $itemData['validated']=$validated;
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }

    /**
     *
     * @api GET /api/1.0/private/admin/user/change-user-status
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        要修改的用户ID(必须的)
     * * string        status    要修改的状态(必须的)  0为 未审核   1为通过  2为未通过
     *
     * @return json
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getChangeUserStatus(Request $request){
        $id=(int)$request->get('id');
        $status=(int)$request->get('status');
        $userModel=new User();

        $data=$userModel->getUserProfileByIds($id);
        $userProfile=$data[$id];
        if(!is_null($userProfile))
        {
            $userProfile->validated=$status;
            if($userProfile->save())
            {
                return response()->json(
                    $this->success_data($userProfile,1,'修改成功')
                );
            }
            else
            {
                return response()->json(
                    $this->fail(new \Exception('修改失败'))
                );
            }
        }
        else
        {
            return response()->json(
                $this->fail(new \Exception('没有找到相关用户'))
            );
        }

    }

    /**
     * 批量修改用户状态
     * @api GET /api/1.0/private/admin/user/change-users-status
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        ids        用户ID 序列(必须的) e.g:1,22,23,31
     * * string        status     要变更的状态(必须的)
     *
     * @return json data":{"total":4,"pagesize":20,"page":1,"rows":[{uid:用户ID，result:执行结果},{uid:用户ID，result:执行结果}]}
     *
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 18:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getChangeUsersStatus(Request $request){
        $ids=e($request->get('ids'));
        $status=e($request->get('status'));
        $idsArray=explode(',',$ids);
        $idData=[];
        foreach($idsArray as $id)
        {
            $idData[]=intval($id);
        }
        $userModel=new User();
        $datas=$userModel->getUserProfileByIds($idData);
        $returnData=[];
        foreach($datas as $data)
        {
            $data->validated=$status;
            $result=$data->save();
            $returnData[]=[
                'uid'=>$data->id,
                'result'=>$result
            ];
        }
        return response()->json(
            $this->success_rows(1,'获取成功',count($idsArray),20,1,$returnData)
        );
    }

    /**
     *
     * @api POST /api/1.0/private/admin/user/del-many
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        ids        多个用户ID,逗号隔开(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postDelMany(Request $request){
        $ids=e($request->get('ids'));
        $idsArray=explode(',',$ids);

        $userModel=new User();
        $studentModel=new Student();
        $teacherModel=new Teacher();
        DB::beginTransaction();
        try{
            if(empty($idsArray))
            {
                throw new\Exception('请选择被删除的用户');
            }
            $result=$userModel->whereIn('id',$idsArray)->delete();
            if(!$result)
            {
                throw new\Exception('删除用户失败');
            }
            $result=$studentModel->whereIn('id',$idsArray)->delete();
            if(!$result)
            {
                throw new\Exception('删除学生失败');
            }
            $result=$teacherModel->whereIn('id',$idsArray)->delete();
            if(!$result)
            {
                throw new\Exception('删除教师失败');
            }
            DB::commit();
            return response()->json(
                $this->success_data(['result'=>true],1,'修改成功')
            );
        }
        catch (\Exception $ex)
        {
            DB::rollback();
            return response()->json(
                $this->fail($ex)
            );
        }
    }
}