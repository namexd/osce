<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/16
 * Time: 16:21
 */

namespace Modules\Osce\Http\Controllers\Api;

use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;

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
    public function postEditorUpload(Request $request){
        if ($request->hasFile('upfile'))
        {
            $file   =   $request->file('upfile');
            $path   =   'osce/attach/'.date('Y-m-d').'/'.rand(1000,9999).'/';
            $destinationPath    =   public_path($path);
            $fileName           =   $file->getClientOriginalName();
            $file   ->  move($destinationPath,iconv("UTF-8","gb2312",$fileName));
            $pathReturn    =   '/'.$path.$fileName;
        }
        echo json_encode(
            array(
                "state" => 'SUCCESS',
                "url" => $pathReturn,
                "title" => $fileName,
                "original" => $file->getClientOriginalExtension(),
                "type" => $file->getClientMimeType(),
                "size" => $file->getClientSize()
            )
        );
    }

    /**
     *
     * @url /osce/api/communal-api/attch-upload
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAttchUpload(Request $request){

        $data   =   [
            'path'  =>  ''
        ];
        if ($request->hasFile('attchment'))
        {
            $file   =   $request->file('attchment');
            $path   =   'osce/attach/'.date('Y-m-d').'/'.rand(1000,9999).'/';
            $destinationPath    =   public_path($path);
            //.'.'.$file->getClientOriginalExtension()
            $fileName           =   $file->getClientOriginalName();
            $file->move($destinationPath,$fileName);
            $pathReturn    =   '/'.$path.$fileName;
            $data   =   [
                'path'=>$pathReturn,
                'name'=>$fileName
            ];
        }
        echo json_encode(
            $this->success_data($data,1,'上传成功')
        );
    }

    public function getEditorUpload(Request $request){
        $json   =   file_get_contents(public_path('osce/admin/plugins/js/plugins/UEditor/php/').'config.json');
        $CONFIG =   json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $json), true);
        $result =  json_encode($CONFIG);
        echo $result;
    }
}