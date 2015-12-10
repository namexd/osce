<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/1 0001
 * Time: 15:23
 */

namespace Modules\Msc\Repositories;
use Modules\Msc\Entities\ResourcesClassroomPlan;

class Common{

    public static function classroomEmptyTime($roomId,$time){

        $list=ResourcesClassroomPlan::leftJoin(
            'resources_lab_courses',
            function($join){
                $join->on('resources_lab_courses.id','=','resources_lab_plan.resources_lab_course_id');
            })  ->where('resources_lab_courses.resources_lab_id','=',$roomId)
            ->whereRaw(
                'unix_timestamp(currentdate)> ?  ',
                [strtotime(date('Y-m-d'))]
            )->get();
        $lastTime=time();
        $emptyTime=[];
        foreach($list as $item){
            $startDateTime=strtotime($item->currentdate.' '.$item->begintime);
            $endDateTime=strtotime($item->currentdate.' '.$item->endtime);

            if($startDateTime-$lastTime>=$time&&date('H',$lastTime+$time)<=22)
            {
                $emptyTime[]=[
                    'start'=>$lastTime,
                    'end'=>$startDateTime,
                ];
            }
            if(date('H',$lastTime+$time)<=22)
            {
                //如果结束时间为 当晚最后一节课，那么开始时间为第二天早上8点
                $lastTime=strtotime($item->currentdate)+115200;
            }
            else
            {
                $lastTime=$endDateTime;
            }

        }
        $emptyTime[]=[
            'start'=>$lastTime,
            'end'=>$lastTime+$time,
        ];
        $timeEmpty=[];
        foreach($emptyTime as $thisTime)
        {
            $timeEmpty[]=[
                $thisTime['start'],
                $thisTime['end']
            ];
        }
        return $timeEmpty;
    }


    //汉字转数字 100以内
    public static function hanzi2num($str){
        $numarr = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100);
        $hanziarr = array("一","二","三","四","五","六","七","八","九","十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","二十一","二十二","二十三","二十四","二十五","二十六","二十七","二十八","二十九","三十",
            "三十一","三十二","三十三","三十四","三十五","三十六","三十七","三十八","三十九","四十","四十一","四十二","四十三","四十四","四十五","四十六","四十七","四十八","四十九","五十","五十一","五十二","五十三","五十四","五十五","五十六","五十七",
            "五十八","五十九","六十","六十一","六十二","六十三","六十四","六十五","六十六","六十七","六十八","六十九","七十","七十一","七十二","七十三","七十四","七十五","七十六","七十七","七十八","七十九","八十","八十一","八十二","八十三","八十四",
            "八十五","八十六","八十七","八十八","八十九","九十","九十一","九十二","九十三","九十四","九十五","九十六","九十七","九十八","九十九","一百");
        foreach($hanziarr as $k=>$v){
            if(strpos($str,$v)!==false){
                $rstr = str_replace($v,$numarr[$k],$str);
            }
        }
        return $rstr;
    }

    // 根据开始时间(eg:8:00)和结束时间、单节时长(min) 划分时间段
    public static function devide_time_sec ($beginTime, $endTime, $length)
    {

        if(!is_int(intval($length)) || 0 >= $length)
        {
            throw new \Exception('设备使用一次所需时间必须大于零');
        }

        $beginTime = strtotime($beginTime);
        $endTime   = strtotime($endTime);
        $length    = $length * 60;

        $data = [];
        $temp = 0;
        for ($i=$beginTime; $i<=$endTime; $i+=$length)
        {
            $temp += $length;
            $data[] = date('H:i', $temp).'-'.date('H:i', ($temp+$length));
        }

        return $data;
    }

}