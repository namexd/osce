<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 10:50
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Http\Controllers\CommonController;

class ExamArrangeController extends CommonController
{
    //考试安排着陆页
    //获取考场接口
    //获取考站接口
    public function getStationData(){
        //查询出该站是否已选择过该考站
        //如果有就不再显示该考站数据
        //如果没有就显示所有考站数据
        
    }



}