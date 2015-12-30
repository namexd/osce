<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 14:35
 */

namespace Modules\Osce\Entities;


use Illuminate\Database\Eloquent\Model;

class CommonModel extends Model
{
    public function changeStatus($formData)
    {
        try {
            $id = $formData['id'];
            $status = $formData['status'];
            $place = $this->firstOrFail($id);
            $place->status = $status;
            return $place->save();
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}