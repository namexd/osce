<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/18 0018
 * Time: 18:32
 */

//namespace Modules\Msc\Database\Seeders;
use Illuminate\Database\Seeder;

class ResourcesToolsCateSeeder extends Seeder{

    public function run() {
        $Teacher =   new  \Modules\Msc\Entities\Teacher();
        $teacherMessage = $Teacher->get()->toArray();
        $teachCount = count($teacherMessage);
        for ($i=0;$i<$teachCount;++$i) {
            $list = $this->dataList($teacherMessage,$i);
            \Modules\Msc\Entities\ResourcesToolsCate::Create($list);
            sleep(1);
        }
    }

    public function dataList($teacherMessage,$i) {
        $list = [];
        $teacherName = $teacherMessage[$i]['name'];
        $teacherID = $teacherMessage[$i]['id'];
        $teacherTel = '';
        $list['pid'] = rand(0,2);
        $list['repeat_max'] = rand(0,1);
        $list['name'] = '测试类别'.$i;
        $list['manager_id'] = $teacherID;
        $list['manager_name'] = $teacherName;
        $list['manager_mobile'] = $teacherTel;
        $list['location'] = '测试地址'.$i;
        $list['detail'] = '测试说明'.$i;
        $list['loan_days'] = rand(0,7);
        return $list;
    }
}