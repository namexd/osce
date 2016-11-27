<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@163.com>
 * Date: 2015/11/17
 * Time: 16:50
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

abstract class CommonModel extends  Model
{
    public function searchByKeyword($keyword,$feild=''){
        $fields=$this->getModelSearchFeild();
        $model=$this;
        if(empty($feild))
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
    public function getLeftJoinBuilder($joinTable,$param=[]){
        if(empty($param))
        {
            $param=[
                'where'=>[],
                'whereIn'=>[],
                'orWhere'=>[],
                'whereRaw'=>[],
                'order'=>[],
            ];
        }
        $pathArray  =   explode('\\',get_class($this));
        $thisMoulde  =   array_pop($pathArray);
        $modelNameToTableNameArray=[
            $thisMoulde=>$this->table
        ];
        //获取模型名和数据表的关联清单
        foreach($joinTable as $relation)
        {
            $model      =   new $relation['modelName'];
            $pathArray  =   explode('\\',$relation['modelName']);
            $modelName  =   array_pop($pathArray);
            $modelNameToTableNameArray[$modelName]  =   $model  ->  getTable();
        }
        $builder=$this;
        //关联表
        foreach($joinTable as $relation)
        {
            $tableNameList  =   array_keys($relation);
            if(!array_key_exists($tableNameList[1],$modelNameToTableNameArray))
            {
                throw new   \Exception('请开发者检查关联查询表名与模型名');
            }
            $builder    =   $builder    ->   leftJoin(
                $model      ->  getTable(),
                function($join) use ($modelNameToTableNameArray,$relation,$tableNameList){
                     $join  ->on(
                         $modelNameToTableNameArray[$tableNameList[1]].'.'.$relation[$tableNameList[1]],
                         '=',
                         $modelNameToTableNameArray[$tableNameList[2]].'.'.$relation[$tableNameList[2]]
                     );
                }
            );
        }
        $this->filterBuilder($builder,$param,$modelNameToTableNameArray);
    }
    protected function filterBuilder($builder,$param,$modelNameToTableNameArray){
        $whereList      =   $param['where'];
        $whereInList    =   $param['whereIn'];
        $orWhereList    =   $param['orWhere'];
        $whereRawList   =   $param['whereRaw'];
        $orderList      =   $param['order'];

        if(!empty($whereList))
        {
            foreach($whereList as $where)
            {
                if(is_array($where[0]))
                {
                    $builder    =   $builder->  where($where[0][0].'.'.$where[0][1],$where[1],$where[2]);
                }
                else
                {
                    $builder    =   $builder->  where($where[0],$where[1],$where[2]);
                }
            }
        }
        if(!empty($whereInList))
        {
            foreach($whereInList as $whereIn)
            {
                if(is_array($whereIn[0]))
                {
                    $builder    =   $builder->  whereIn($whereIn[0][0].'.'.$whereIn[0][1],$whereIn[1]);
                }
                else
                {
                    $builder    =   $builder->  where($where[0],$where[1]);
                }
            }
        }
        if(!empty($orWhereList))
        {
            foreach($orWhereList as $orWhere)
            {
                if(is_array($orWhere[0]))
                {
                    $builder    =   $builder->  where($orWhere[0][0].'.'.$orWhere[0][1],$orWhere[1],$orWhere[2]);
                }
                else
                {
                    $builder    =   $builder->  where($orWhere[0],$orWhere[1],$orWhere[2]);
                }
            }
        }
        if(!empty($whereRawList))
        {
            foreach($whereRawList as $whereRaw)
            {
                //[
                    //'unix_timestamp()= ? and unix_timestamp()<=? or unix_timestamp()>=?'
                    //['currentdate'=>123,'begintime'=>123]
                //]
                //'unix_timestamp(currentdate)= ? and unix_timestamp(begintime)<=? or unix_timestamp(endtime)>=? ',
                if(is_array($whereRaw[0]))
                {
                    $builder    =   $builder->  whereRaw();
                }
                else
                {
                    $builder    =   $builder->  whereRaw();
                }
            }
        }


    }
}