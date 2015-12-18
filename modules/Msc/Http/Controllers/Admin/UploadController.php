<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/18 0018
 * Time: 11:33
 */

namespace Modules\Msc\Http\Controllers\Admin;



use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\Common;
use Illuminate\Http\Request;

class UploadController extends MscController
{
    public function getTeachMessage() {
        return view('msc::admin.upload.teach_message');
    }

    public function postTeachMessageExcel(Request $request) {
        $data=Common::getExclData($request,'teach');
        dd($data);
        $coursesList= array_shift($data);
        //将中文表头 按照配置 翻译成 英文字段名
        $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.courses');

    }
}