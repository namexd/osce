<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/11/12
 * Time: 10:09
 */

namespace Modules\Msc\Repositories;


abstract class BaseRepository
{
    protected $model;
    protected function setSearchModel($model){
        $this->model=$model;
    }
    protected function getModelSearchFeild(){
        $search=$this->model->search;
        if(empty($search))
        {
            return [];
        }
        else
        {
            return $search;
        }
    }
    protected function getSearchWhere($keyword){
        $fields=$this->getModelSearchFeild();
        if(empty($fields))
        {
            return $this->model;
        }
        else
        {

            $model=$this->model;
        }
        foreach($fields as $feild)
        {
            $model=$model->orWhere($feild,'like','%'.$keyword.'%');
        }
        return $model;
    }
}