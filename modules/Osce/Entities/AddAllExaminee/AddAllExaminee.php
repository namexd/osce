<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/20
 * Time: 17:15
 */

namespace Modules\Osce\Entities\AddAllExaminee;


use App\Repositories\Common;
use Illuminate\Http\Request;

class AddAllExaminee
{
    private $data;

    public function setData(Request $request, $fileName)
    {
        return $this->data = Common::getExclData($request, $fileName);
    }

    public function wipeOffSheet(array $data)
    {
        return $this->data = array_shift($data);
    }

}