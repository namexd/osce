<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/16
 * Time: 16:21
 */

namespace Modules\Osce\Http\Controllers\Api;

use Modules\Osce\Http\Controllers\CommonController;

class CommunalApiController extends CommonController
{
    /**
     * 文本编辑器 上传图片 接口
     * @url GET /osce/admin/communal-api/editor-upload
     * @access public
     *
     * <b>get请求字段：</b>
     * * string        images        图片文件(必须的)
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditorUpload(){
        echo 123;
    }
}