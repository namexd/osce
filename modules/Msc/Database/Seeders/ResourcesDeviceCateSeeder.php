<?php

use Illuminate\Database\Seeder;

class ResourcesDeviceCateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Teacher =   new  \Modules\Msc\Entities\Teacher();
        $teacherMessage = $Teacher->get()->toArray();
        $teachCount = count($teacherMessage);
        $list = [];


        for ($i=0;$i<$teachCount;++$i) {
            $teacherName = $teacherMessage[$i]['name'];
            $teacherID = $teacherMessage[$i]['id'];
            $teacherTel = '';
            $list['pid'] = 0;
            $list['name'] = '测试类别'.$i;
            $list['manager_id'] = $teacherID;
            $list['manager_name'] = $teacherName;
            $list['manager_mobile'] = $teacherTel;
            $list['location'] = '测试地址'.$i;
            $list['detail'] = '测试说明'.$i;
            \Modules\Msc\Entities\ResourcesDeviceCate::Create($list);
            sleep(1);
        }
    }



}
