<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/28
 * Time: 15:52
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\AreaVcr;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\RoomVcr;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Vcr;
use Modules\Osce\Entities\Pad;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use Predis\Transaction\AbortedMultiExecException;

class MachineController extends CommonController
{


    /**
     *  新增设备类别
     * @url POST /osce/admin/machine/add-category
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
     * @date 2015-12-30 11:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $data = [
            'name' => e($request->get('name')),
        ];
        try {
            $category = config('machine_category');
            if ($category) {
                return redirect()->route('osce.admin.machine.getCategoryList');
            } else {
                throw new \Exception('新增失败');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     *  新增设备类别（表单显示页面）
     * @url GET /osce/admin/machine/add-category
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
     * @date 2015-12-30 11:27
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddCategory(Request $request)
    {

        //return view();
    }

    /**
     * 获取设备列表
     * @api GET /osce/admin/machine/machine-list
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
     * @author Luohaihua <Luohaihua@misrobot.com> Zhoufuxiang 2016-01-13 15:21:48
     * @date 2015-12-30 11:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getMachineList(Request $request)
    {
        $this->validate($request, [
            'cate_id' => 'sometimes|integer',
            'name' => 'sometimes',
            'status' => 'sometimes',
            'nfc_code' => 'sometimes',
            'code' => 'sometimes',
            'student_name' => 'sometimes',
            'begin_dt' => 'sometimes',
            'end_dt' => 'sometimes',
        ]);

        $cate_id = intval($request->get('cate_id'));
        $cate_id = $cate_id == 0 ? 1 : $cate_id;
        $name = $request->get('name');
        $status = e($request->get('status'));
        $nfc_code = e($request->get('nfc_code'));
        $cate = config('osce.machine_category');
        if (is_null($cate)) {
            abort(404, '设备类别不存在，请检查数据或联系管理员');
        }
        if (!array_key_exists($cate_id, $cate)) {
            abort(404, '设备类别不存在，请检查数据或联系管理员');
        }
        $model = $this->getMachineModel($cate_id);
        $list = $model->getList($name, $status, $nfc_code);


        $machineStatuValues = $model->getMachineStatuValues();
        switch ($cate_id) {
            case 2:
                return view('osce::admin.resourceManage.equipment_manage_pad', [
                    'list' => $list,
                    'options' => $cate,
                    'machineStatuValues' => $machineStatuValues,
                    'name' => $name
                ]);
                break;
            case 3:
                return view('osce::admin.resourceManage.equipment_manage_watch', [
                    'list' => $list,
                    'options' => $cate,
                    'machineStatuValues' => $machineStatuValues,
                    'name' => $name
                ]);
                break;
            case 1:
            default:
                return view('osce::admin.resourceManage.equipment_manage_video', [
                    'list' => $list,
                    'options' => $cate,
                    'machineStatuValues' => $machineStatuValues,
                    'name' => $name
                ]);
        }
    }

    /**
     * 获取对应类别下的扩展模型(可能是临时方法，可能会在数据库正式出来以后删除，目前就只有getMachineList方法使用)
     * @access private
     * @param
     * * string        $cate_id        类别ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 14：17
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function getMachineModel($cate_id)
    {
        $config = [
            1 => 'Vcr',
            2 => 'Pad',
            3 => 'Watch',
        ];
        $name = '\Modules\Osce\Entities\\' . $config[$cate_id];
        return new $name;
    }

    /**
     * 新增设备
     * @api POST /osce/admin/invigilator/add-machine
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * 摄像机
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *  Pad
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *  腕表
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 15:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddMachine(Request $request)
    {
        $this->validate($request, [
            'cate_id' => 'required|integer'
        ]);
        $cate_id = intval($request->get('cate_id'));
        try {
            switch ($cate_id) {
                case 2:
                    $machine = $this->addPad($request);
                    break;
                case 3:
                    $machine = $this->addWatch($request);
                    break;
                case 1:
                default :
                    $machine = $this->addCameras($request);
            }

            if ($machine) {
                return redirect()->route('osce.admin.machine.getMachineList', ['cate_id' => $cate_id]);
            } else {
                throw new \Exception('新增设备失败');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex->getMessage())->withInput();
        }
    }

    /**
     * 编辑设备信息
     * @url /osce/admin/machine/edit-machine
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        id           摄像机ID(必须的)
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 15:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditMachine(Request $request)
    {
        $this->validate($request, [
            'cate_id' => 'required|integer'
        ]);

        $cate_id = intval($request->get('cate_id'));
        try {
            switch ($cate_id) {
                case 2:
                    $machine = $this->editPad($request);
                    break;
                case 3:
                    $machine = $this->editWatch($request);
                    break;
                case 1:
                default :
                    $machine = $this->editCameras($request);
            }
            if ($machine) {
                return redirect()->route('osce.admin.machine.getMachineList', ['cate_id' => $cate_id]);
            } else {
                throw new \Exception('编辑设备失败');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex->getMessage());
        }
    }

    /**
     * 新增摄像头
     * @access private
     *
     * * @param Request $request
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return pbject
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-30 18.49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function addCameras(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'ip' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required',
            'realport' => 'required',
            'channel' => 'required',
            'description' => 'sometimes',
            'status' => 'required',
            'factory' => 'required',
            'sp' => 'required',
            'purchase_dt' => 'required',
            'place' => 'required'
        ], [
            'name.required' => '设备名称必填',
            'name.unique' => '设备名称必须唯一',
            'code.required' => '设备编码必填',
            'ip.required' => '设备IP地址必填',
            'username.required' => '设备登录用户名必填',
            'password.required' => '设备登录密码必填',
            'port.required' => '设备端口必填',
            'realport.required' => '实时端口必填',
            'channel.required' => '设备网口必填',
            'status.required' => '设备状态必选',
            'factory.required' => '厂家必填',
            'sp.required' => '型号必填',
            'purchase_dt.required' => '采购日期必填',
            'place.required' => '放置地点必填',
        ]);

        $data = $request->only('name', 'code', 'ip', 'username', 'password', 'port', 'realport',
            'channel', 'description', 'status', 'factory', 'sp', 'purchase_dt', 'place');
        try {
            $model = new Vcr();
            if ($cameras = $model->addMachine($data)) {
                return $cameras;
            } else {
                throw new \Exception('新增摄像头失败');
            }

        } catch (\Exception $ex) {
            //return response()->back()->withError($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * 编辑摄像头
     * @access private
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 15:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function editCameras(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'ip' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required',
            'realport' => 'required',
            'channel' => 'required',
            'description' => 'sometimes',
            'factory' => 'required',
            'sp' => 'required',
            'purchase_dt' => 'required',
        ], [
            'id.required' => '设备ID必填',
            'name.required' => '设备名称必填',
            'code.required' => '设备编码必填',
            'ip.required' => '设备IP地址必填',
            'username.required' => '设备登录用户名必填',
            'password.required' => '设备登录密码必填',
            'port.required' => '设备端口必填',
            'realport.required' => '实时端口必填',
            'channel.required' => '设备网口必填',
            'factory.required' => '厂家必填',
            'sp.required' => '型号必填',
            'purchase_dt.required' => '采购日期必填',
        ]);

        $data = $request->only('id', 'name', 'code', 'ip', 'username', 'password', 'port', 'realport',
            'channel', 'description', 'status', 'factory', 'sp', 'purchase_dt', 'place');

        try {
            $model = new Vcr();
            if ($cameras = $model->editMachine($data)) {
                return $cameras;
            } else {
                throw new \Exception('编辑摄像头失败');
            }
        } catch (\Exception $ex) {
            //return response()->back()->withError($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * 新增摄像机表单页面
     * @url /osce/admin/machine/add-cameras
     * @access public
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 13:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddCameras()
    {
        $model = new Vcr();
        $status = $model->getMachineStatuValues();
        return view('osce::admin.resourceManage.equipment_manage_video_add', ['status' => $status]);
    }

    /**
     *
     * @url /osce/admin/machine/edit-cameras
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * int        id        摄像机ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16：11
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditCameras(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = intval($request->get('id'));
        $model = new Vcr();
        $status = $model->getMachineStatuValues();
        $vcr = Vcr::find($id);

        return view('osce::admin.resourceManage.equipment_manage_video_edit', ['item' => $vcr, 'status' => $status]);
    }

    /**
     * 新增pad
     * @access public
     *
     * * @param Request $request
     * <b>post 请求字段：</b>
     * * int           name        设备名称(必须的)
     * * string        code        设备编号(必须的)
     * * int           status      设备状态(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function addPad(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:osce_mis.pad',
            'code' => 'required',
            'factory' => 'required',
            'sp' => 'required',
            'purchase_dt' => 'required',
            'status' => 'required',
            'place' => 'required'
        ], [
            'name.required' => '设备名称必填',
            'name.unique' => '设备名称必须唯一',
            'code.required' => '设备ID必填',
            'factory.required' => '厂家必填',
            'sp.required' => '型号必填',
            'purchase_dt.required' => '采购日期必填',
            'status.required' => '设备状态必填',
            'place.required' => '放置地点必填'
        ]);

        $user = Auth::user();
        if (empty($user)) {
            throw new \Exception('未找到当前操作人信息');
        }
        $data = [
            'name' => e($request->get('name')),
            'code' => $request->get('code'),
            'factory' => e($request->get('factory')),
            'sp' => $request->get('sp'),
            'purchase_dt' => $request->get('purchase_dt'),
            'status' => $request->get('status'),
            'create_user_id' => $user->id,
            'place' => e($request->input('place'))
        ];

        try {
            $model = new Pad();
            if ($pad = $model->addMachine($data)) {
                return $pad;
            } else {
                throw new \Exception('新增PAD失败');
            }

        } catch (\Exception $ex) {
            return response()->back()->withError($ex->getMessage());
        }
    }

    private function editPad(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'status' => 'sometimes',
            'factory' => 'required',
            'sp' => 'required',
            'purchase_dt' => 'required',
        ], [
            'id.required' => 'ID必填',
            'name.required' => '设备名称必填',
            'code.required' => '设备ID必填',
            'status.required' => '状态必填',
            'factory.required' => '厂家必填',
            'sp.required' => '型号必填',
            'purchase_dt.required' => '采购日期必填',
        ]);
        $data = [
            'id' => $request->get('id'),
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'status' => $request->get('status'),
            'factory' => e($request->get('factory')),
            'sp' => $request->get('sp'),
            'purchase_dt' => $request->get('purchase_dt'),
            'place' => $request->input('place')
        ];

        try {
            $model = new Pad();
            if ($cameras = $model->editMachine($data)) {
                return $cameras;
            } else {
                throw new \Exception('编辑摄像机失败');
            }

        } catch (\Exception $ex) {
            return response()->back()->withError($ex->getMessage());
        }
    }

    /**
     * 新增摄像机表单页面
     * @url /osce/admin/machine/add-pad
     * @access public
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 13:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddPad()
    {
        $model = new Pad();
        $status = $model->getMachineStatuValues();
        return view('osce::admin.resourceManage.equipment_manage_pad_add', ['status' => $status]);
    }

    /**
     * 编辑Pad信息
     * @url /osce/admin/machine/edit-pad
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * int        id        摄像机ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16：11
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditPad(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = intval($request->get('id'));
        $model = new Pad();
        $status = $model->getMachineStatuValues();
        $pad = Pad::find($id);

        return view('osce::admin.resourceManage.equipment_manage_pad_edit', ['item' => $pad, 'status' => $status]);
    }

    /**
     * 新增腕表
     * @access public
     *
     * * @param Request $request
     * <b>post 请求字段：</b>
     * * int           name        设备名称(必须的)
     * * string        code        设备编号(必须的)
     * * int           status      设备状态(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function addWatch(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:osce_mis.watch,name',
            'code' => 'required|unique:osce_mis.watch',
            'factory' => 'required',
            'sp' => 'required',
            'nfc_code' => 'sometimes|unique:osce_mis.watch,nfc_code',
            'status' => 'required',
            'purchase_dt' => 'required',
            'place' => 'required'
        ], [
            'name.required' => '腕表名称必填',
            'name.unique' => '腕表名称必须唯一',
            'code.required' => '腕表ID必填',
            'code.unique' => '腕表ID已存在',
            'factory.required' => '厂家必填',
            'sp.required' => '型号必填',
            'status.required' => '状态必选',
            'purchase_dt.required' => '采购日期必填',
            'nfc_code.unique' => 'nfc标签代码值必须唯一',
            'place.required' => '放置地点必填'
        ]);

        $user = Auth::user();
        if (empty($user)) {
            throw new \Exception('未找到当前操作人信息');
        }

        $data = [
            'code' => $request->get('code'),
            'name' => $request->get('name'),
            'status' => $request->get('status'),
            'description' => $request->get('description'),
            'factory' => $request->get('factory'),
            'sp' => $request->get('sp'),
            'create_user_id' => $user->id,
            'purchase_dt' => $request->get('purchase_dt'),
            'nfc_code' => $request->get('nfc_code'),
            'place' => $request->input('place')
        ];

        try {
            $model = new Watch();
            if ($watch = $model->addMachine($data)) {
//                $action='新增';
//                $data=array(
//                    'create_user_id'=>$data['create_user_id'],
//                    'action'        =>$action,
//                    'context'       =>array('name'=>$data['name'],'code'=>$data['code'],'status'=>$data['status']),
//                );
//                $watchModel=new WatchLog();
//                $watchModel->historyRecord($data);
                return $watch;
            } else {
                throw new \Exception('新增腕表失败');
            }

        } catch (\Exception $ex) {
            return response()->back()->withError($ex->getMessage());
        }
    }

    private function editWatch(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'nfc_code' => 'sometimes',
            'code' => 'required',
            'factory' => 'required',
            'sp' => 'required',
            'status' => 'sometimes',
            'purchase_dt' => 'required',
        ], [
            'id.required' => '设备ID必填',
            'name.required' => '设备名称必填',
            'code.required' => '腕表编码必填',
            'factory.required' => '生产厂家必填',
            'sp.required' => '型号规格必填',
            'status.required' => '设备状态必填',
            'purchase_dt.required' => '采购日期必填',
        ]);

        $code = $request->get('code');
        $id = Watch::where('code', $code)->select('id')->first();
        if ($id) {
            $id = $id->id;
            if ($id != $request->get('id')) {
                return redirect()->back()->withErrors('设备编号已存在');
            }
        }
        $nfc = $request->get('nfc_code');
        if ($nfc) {
            $watch_id = Watch::where('nfc_code', $nfc)->select()->first();
            if ($watch_id) {
                $watch_id = $watch_id->id;
                if ($id != $watch_id) {
                    return redirect()->back()->withErrors('nfc标签代码值已存在');
                }
            }
        }

        $data = [
            'id' => $request->get('id'),
            'name' => $request->get('name'),
            'factory' => $request->get('factory'),
            'nfc_code' => $request->get('nfc_code'),
            'sp' => $request->get('sp'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
            'purchase_dt' => $request->get('purchase_dt'),
            'place' => $request->input('place')
        ];
        $code = Watch::where('id', $request->get('id'))->select('code')->first()->code;
        if ($code != $request->get('code')) {
            $data['code'] = $request->get('code');
        }

        try {
            $model = new Watch();
            if ($cameras = $model->editMachine($data)) {
//                $action='编辑';
//                $data=array(
//                    'create_user_id'=>$data['create_user_id'],
//                    'action'        =>$action,
//                    'context'       =>array('name'=>$data['name'],'code'=>$data['code'],'status'=>$data['status']),
//                    'watch_id'       =>$data['id'],
//                );
//                $watchModel=new WatchLog();
//                $watchModel->historyRecord($data);
                return $cameras;
            } else {
                throw new \Exception('编辑摄像头失败');
            }

        } catch (\Exception $ex) {
            return response()->back()->withError($ex->getMessage());
        }
    }

    /**
     * 新增腕表单页面
     * @url /osce/admin/machine/add-watch
     * @access public
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 13:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddWatch()
    {
        $model = new Watch();
        $status = $model->getMachineStatuValues();
        return view('osce::admin.resourceManage.equipment_manage_watch_add', ['status' => $status]);
    }

    /**
     * 编辑腕表单页面
     * @url /osce/admin/machine/edit-watch
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * int        id        摄像机ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16：11
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditWatch(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = intval($request->get('id'));
        $model = new Watch();
        $status = $model->getMachineStatuValues();
        $watch = Watch::find($id);

        return view('osce::admin.resourceManage.equipment_manage_watch_edit', ['item' => $watch, 'status' => $status]);
    }

    /**
     * 监考设备的删除
     * @api       POST /osce/admin/machine/postMachineDelete
     * @access    public
     * @param Request $request post请求<br><br>
     * @param Machine $machine
     * @return view
     * @internal param Machine $model Machine
     * @version   1.0
     * @author    Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postMachineDelete(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required|integer',
                'cate_id' => 'required|integer'
            ]);
            //获取删除的id
            $id = $request->input('id');
            $cate_id = $request->input('cate_id');
            if ($cate_id == 1) {
                if (!StationVcr::where('vcr_id', $id)->get()->isEmpty()) {
                    throw new \Exception('该设备已于其他设备关联,无法删除!');
                }
                if (!RoomVcr::where('vcr_id', $id)->get()->isEmpty()) {
                    throw new \Exception('该设备已于其他设备关联,无法删除!');
                }
                if (!AreaVcr::where('vcr_id', $id)->get()->isEmpty()) {
                    throw new \Exception('该设备已于其他设备关联,无法删除!');
                }
            }
            if ($cate_id == 3) {
                if ($result = WatchLog::where('watch_id', $id)->first()) {
                    throw new \Exception('该设备使用过,无法删除!');
                }
            }
            $model = $this->getMachineModel($cate_id);
            //通过id删除相应的设备
            if ($result = $model->where('id', $id)->·()) {
                return $this->success_data(['删除成功！']);
            } else {
                throw new \Exception('该设备已于其他设备关联,无法删除!');
            }
        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     * 判断名称是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
            'cate' => 'required',
        ]);

        $id = $request->get('id');
        $cate = $request->get('cate');
        $name = $request->get('name');
        $code = $request->get('code');
        //实例化模型
        $model = $this->getMachineModel($cate);
        //存在设备ID
        if (!empty($code)) {
            $model = $model->where('code', $code);
        } else {
            $model = $model->where('name', $name);
        }
        //存在ID，为编辑
        if (!empty($id)) {
            $model = $model->where('id', '<>', $id);
        }
        $result = $model->first();
        if ($result) {
            return json_encode(['valid' => false]);
        } else {
            return json_encode(['valid' => true]);
        }
    }

    /**
     *腕表使用记录列表
     * @method GET
     * @url /machine/watch-log-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string          code             设备ID(必须的)
     * * string          student_name     使用人(必须的)
     * * datetime        begin_dt         开始时间(必须的)
     * * datetime        end_dt           结束时间(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getWatchLogList(Request $request)
    {
        $this->validate($request, [
            'code' => 'sometimes',
            'student_name' => 'sometimes',
            'begin_dt' => 'sometimes',
            'end_dt' => 'sometimes',
        ]);

        $code = $request->get('code');
        $studentName = $request->get('student_name');
        $beginDt = $request->get('begin_dt');
        $endDt = $request->get('end_dt');

        $watchLogModel = new WatchLog();
        $list = $watchLogModel->getList(trim($code), trim($studentName), $beginDt, $endDt);

        foreach ($list as $item) {
            //TODO zhoufuxiang 反序列化出错
            try {
                $item->context = unserialize($item->context);
            } catch (\Exception $ex) {
                $item->context = '';
                continue;
            }
        }

        return view('osce::admin.resourceManage.equipment_manage_watch_uselist')->with([
            'list' => $list,
            'code' => $code,
            'student_name' => $studentName,
            'begin_dt' => $beginDt,
            'end_dt' => $endDt
        ]);
    }
}