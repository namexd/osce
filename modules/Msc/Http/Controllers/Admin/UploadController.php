<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/18 0018
 * Time: 11:33
 */

namespace Modules\Msc\Http\Controllers\Admin;



use Modules\Msc\Http\Controllers\MscController;
use Illuminate\Http\Request;
use App\Repositories\Common;
class UploadController extends MscController
{
    public function getImportUser(Request $request){
        return view("msc::admin.import");
    }
    public function postImportUser(Request $request){
        $data=Common::getExclData($request,'user');
        $coursesList= array_shift($data);
        $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.user');
        dd($data);
    }
    public function postTeachMessageExcel(Request $request) {
        $data=Common::getExclData($request,'teach');
        dd($data);
        $coursesList= array_shift($data);
        //�����ı�ͷ �������� ����� Ӣ���ֶ���
        $data=Common::arrayChTOEn($coursesList,'msc.importForCnToEn.courses');

    }
}