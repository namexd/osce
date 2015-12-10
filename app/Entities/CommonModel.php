<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/17
 * Time: 16:50
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

abstract class CommonModel extends  Model
{
    public function searchByKeyword($keyword,$feild=''){
        $fields=$this->getModelSearchFeild();
        $model=$this;
        if(empty($field))
        {
            throw new \Exception('请设置搜索的字段');
        }
        if( in_array($feild,$fields))
        {
            $model=$model->orWhere($feild,'like','%'.$keyword.'%');
        }
        else
        {
            throw new \Exception('该字段不允许被搜索');
        }
        return $model;
    }
    protected function getModelSearchFeild(){
        if(empty($this->search))
        {
            throw new \Exception('请设置允许搜索的字段');
        }
        else
        {
            return $this->search;
        }
    }
}