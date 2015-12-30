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
    /**
     * 改变表的status字段的状态
     * @param array $changeStatus
     * @return mixed
     */
    protected function changeStatus(array $changeStatus)
    {
        foreach ($changeStatus as $item) {
            $id = $item['id'];
            $status = $item['status'];
            $model = $this->find($id);
            $model -> status = $status;
            $result = $model -> save();
        }

        return $result;
    }



}