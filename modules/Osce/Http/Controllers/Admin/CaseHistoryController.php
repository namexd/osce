<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Factory;
use Modules\Osce\Entities\CaseHistory as CaseHistory;
class CaseHistoryController extends CommonController
{
    /**
     * 获取病历列表
     * @api       GET /osce/admin/place/case-history-list
     * @access    public
     * @param Request $request get请求<br><br>
     *                         <b>get请求字段：</b>
     *                         string        keyword         关键字
     *                         string        order_name      排序字段名 枚举 e.g 1:设备名称 2:预约人 3:是否复位状态自检 4:是否复位设备
     *                         string        order_by        排序方式 枚举 e.g:desc,asc
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCaseHistoryList(Request $request)
    {
        //验证暂时空置

        //获得提交的各个值
        $formData = $request->only('keyword','order_name','order_by');
        //在模型中拿到数据
        $caseHistory = new CaseHistory();
        $data = $caseHistory->getList($formData);
        dd($data);
    }

    
}