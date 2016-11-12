<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 14:35
 */

namespace Modules\Osce\Entities;


use Illuminate\Database\Eloquent\Model;

abstract class CommonModel extends Model
{

    public $timestamps = true;
    public $incrementing = true;

    /**
     * 删除数据
     * @param $id
     * @return mixed
     */
    public function deleteData($id)
    {
        return $this->where($this->table.'.id',$id)->delete();
    }

    /**
     * 修改数据
     * @param $id
     * @param $formData
     */
    public function updateData($id, $formData)
    {
        return $this->where($this->table . '.id', $id)->update($formData);
    }

    /**
     * 插入数据
     * @param $formData
     * @return static
     */
    public function insertData($formData)
    {
        $result = $this->create($formData);
        return $result;
    }
}