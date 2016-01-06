<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:44
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

class SpteacherController extends CommonController
{
    /**
     * SP老师名单的的着陆页
     * @api       GET /osce/admin/station/station-list
     * @access    public
     * @param Request $request get请求<br><br> 具体参数如下
     * int $caseId:病例id
     * array $spteacherId
     * int $teacherType
     * @param Station $model
     * @return view
     * @version   1.0
     * @author    jiangzhiheng <jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getShow(Request $request, Teacher $model)
    {
        //验证略

        //得到请求的病例id和已经选择的sp老师id
        $stationId = $request->input('station_id', '');
        $spteacherId = $request->input('spteacher_id', '');

        //得到老师的列表
        $data = $model->showTeacherData($stationId, $spteacherId);

        return $this->success_data($data);
    }
}