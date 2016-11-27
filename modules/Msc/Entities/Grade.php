<?php
/**
 * Created by PhpStorm.
 * 年级虚拟模型
 * User: fengyell <Luohaihua@163.com>
 * Date: 2015/11/6
 * Time: 11:07
 */

namespace Modules\Msc\Entities;


class Grade
{
    protected $GradeList=[];
    /**
     * 获取年级列表
     * @access public
     *
     * @return object
     *
     * @version 1.0
     * @author fengyell <Luohaihua@163.com>
     * @date 2015-11-06 11：13
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     *
     */
    public function getGradeList(){
        $year=date('Y');
        $GradeList=$this->GradeList;
        for($i=0;$i<=9;$i++)
        {
            $year-=1;
            $GradeList[]=['id'=>$year,'name'=>$year.'级'];
        }
        $this->GradeList=$GradeList;
        return $this->GradeList;
    }
}