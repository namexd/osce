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
     * @method POST
     * @url /msc/admin/dept/add-dept
     * @access public
     * @param $data
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AddDept(Request $request){
        $this->validate($request,[]);
        $data = [

        ];
        $DeptInfo = $this->TeacherDept->AddDept($data);
        return response()->json(
            $this->success_rows(1,'添加成功',$DeptInfo)
        );
    }

    /**
     * @method POST
     * @url /msc/admin/dept/add-dept
     * @access public
     * @param $data
     * @return json
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015年12月29日14:58:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function UpdateDept(Request $request){
        $this->validate($request,[]);
        $data = [

        ];
        $DeptInfo = $this->TeacherDept->AddDept($data);
        return response()->json(
            $this->success_rows(1,'添加成功',$DeptInfo)
        );
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
        $this->validate($request,[]);
        $data = [

        ];
        $DeptInfo = $this->TeacherDept->AddDept($data);
        return response()->json(
            $this->success_rows(1,'添加成功',$DeptInfo)
        );
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