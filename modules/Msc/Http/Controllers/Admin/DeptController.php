<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2015-12-29 13:58
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Modules\Msc\Entities\TeacherDept;
use Illuminate\Http\Request;
use Modules\Msc\Http\Controllers\MscController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
/**
 * Class DeptController
 * @package Modules\Msc\Http\Controllers\Admin
 */
class DeptController extends MscController
{
    /**
     * DeptController constructor.
     */
    public function __construct()
    {
        $this->TeacherDept = new TeacherDept;
    }

    /**
     * @method get
     * @url /msc/admin/dept/dept-list
     * @access public
     * @return
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月30日18:09:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function DeptList(){
        dd('ddd');
        return view();
    }

    /**
     * @method POST
     * @url /msc/admin/dept/add-dept
     * @access public
     * @param $request
     * name     科室名称
     * pid      父级id
     * level    等级
     * description  介绍
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AddDept(Request $request){

        $this->validate($request,[
            'name'   => 'required|max:50',
            'pid'   => 'required|integer',
            'level'  => 'required|integer',
            'description' => 'required|max:255'
        ]);
        $user = Auth::user();
        $requests = $request->all();
        $data = [
            'name'   =>  $requests['name'],
            'pid'   => $requests['pid'],
            'level'  => $requests['level'],
            'description' => $requests['description'],
            'created_user_id' => empty($user->id)?1:$user->id
        ];
        $DeptInfo = $this->TeacherDept->AddDept($data);
        if($DeptInfo){
            return response()->json(
                $this->success_rows(1,'添加成功',$DeptInfo)
            );
        }else{
            return response()->json(
                $this->success_rows(2,'添加失败')
            );
        }

    }

    /**
     * @method POST
     * @url /msc/admin/dept/update-dept
     * @access public
     * @param $request
     * name     科室名称
     * description  介绍
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function UpdateDept(Request $request){
        $this->validate($request,[
            'id'   => 'required|integer',
            'name'   => 'required|max:50',
            'description' => 'required|max:255'
        ]);
        $requests = $request->all();
        $data = [
            'name'   =>  $requests['name'],
            'description' => $requests['description'],
        ];
        $DeptInfo = $this->TeacherDept->UpdateDept($requests['id'],$data);
        if($DeptInfo){
            return response()->json(
                $this->success_rows(1,'更新成功',$DeptInfo)
            );
        }else{
            return response()->json(
                $this->success_rows(2,'更新失败')
            );
        }

    }

    /**
     * @method POST
     * @url /msc/admin/dept/del-dept
     * @access public
     * @param $data
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function DelDept(Request $request){
        $this->validate($request,[
            'id'   => 'required|integer',
        ]);

        $requests = $request->all();
        $DeptIdArr[] = intval($requests['id']);
        //递归找出所有子级
        $IdArr = $this->TeacherDept->GetChildIdArr($requests['id']);

        $IdArr = array_merge($DeptIdArr,$IdArr);
        $DeptInfo = $this->TeacherDept->DelDept($IdArr);
        if($DeptInfo){
            return response()->json(
                $this->success_rows(1,'删除成功',count($IdArr),$IdArr)
            );
        }else{
            return response()->json(
                $this->success_rows(2,'删除失败')
            );
        }

    }

    /**
     * @method POST
     * @url /msc/admin/dept/select-dept
     * @access public
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SelectDept(){
        $DeptInfo = $this->TeacherDept->SelectDept();
        $Depts = $this->NodeMerge($DeptInfo);
        return response()->json(
            $this->success_rows(1,'获取成功',$Depts)
        );
    }


    /**
     * @access private
     * @param $node
     * @param int $pid
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日15:44:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private  function NodeMerge($node,$pid=0){
        $arr = array();
        foreach($node as $v){
            if($v['pid'] == $pid){
                $v["child"] = $this->NodeMerge($node,$v["id"]);
                $arr[] = $v;
            }
        }
        return  $arr ;
    }


    /**
     * @method
     * @url /msc/admin/dept/pid-get-dept
     * @access public
     * @param int $pid
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日16:41:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function PidGetDept(){
        $pid = Input::get('pid');
        $Depts = [];
        if($pid > 0 || $pid == '0'){
            $Depts = $this->TeacherDept->PidSelectDept($pid);
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$Depts)
        );
    }

}