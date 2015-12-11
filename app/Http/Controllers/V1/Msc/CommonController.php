<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/16
 * Time: 16:15
 */

namespace App\Http\Controllers\V1\Msc;
use App\Http\Controllers\V1\ApiBaseController;
use Illuminate\Http\Request;
use App\Repositories\Common;

class CommonController extends  ApiBaseController
{
    /**
     * 上传单张文件
     * @api POST /api/1.0/private/admin/common/upload-image
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        image        上传文件的name(必须的)
     *
     * @return json {‘path’:'路径'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postUploadImage(Request $request){
        try{
            $path=Common::saveImage($request,'images');
        }
        catch(\Exception $ex){
            return response()->json($this->fail($ex));
        }
        if($path)
        {
            $dataReturn=[
                'path'=>$path
            ];
            die(json_encode(array('code'=>1,'data'=>$dataReturn,'messages'=>'上传成功','status'=>200)));
/*            return response()->json(
                $this->success_data($dataReturn,1,'保存成功')
            );*/
        }
        else
        {
            return response()->json($this->fail(new \Exception('保存失败')));
        }

    }
}