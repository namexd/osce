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

    /**
     * 封装的排序方法，所有模型都能使用此方法
     * @param $orderName  e.g:1,2,3
     * @param $orderBy    e.g:desc,asc
     * @param array $paramArray
     */
    protected function order($builder,$orderName = 1,$orderBy,array $paramArray = ['created_at'])
    {
        foreach ($paramArray as $key => $item) {
            if ($orderName == ($key+1)) {
                $orderName = $item;
            }
        }
        return $builder->orderBy($orderName,$orderBy);
    }


}