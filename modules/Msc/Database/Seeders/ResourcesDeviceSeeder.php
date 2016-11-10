<?php

use Illuminate\Database\Seeder;

class ResourcesDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list   =$this->defaultData();

        foreach($list as $item)
        {
            \Modules\Msc\Entities\ResourcesDevice::firstOrCreate($item);
        }
    }

    public function defaultData(){
        $list   =   \Modules\Msc\Entities\ResourcesClassroom::where('opened','=',2)->get();
        $data   =   [];
        foreach($list as $item)
        {
            $data[]  =[
                'resources_lab_id'          =>  $item->id,
                'name'                      =>  '测试设备'.rand(10000,99999),
                'code'                      =>  rand(10000,99999),
                'resources_device_cate_id'  =>  $this->getCate()->id,
                'max_use_time'              =>  0,
                'warning'                   =>  '',
                'detail'                    =>  '测试记录',
                'status'                    =>  '1',
            ];
        }
        return $data;
    }
    public function getCate(){
        $list   =   \Modules\Msc\Entities\ResourcesDeviceCate::get();
        return  $this   ->  getRandItem($list);
    }
    protected function getRandItem($list){
        if(count($list)==0) return [];
        $index  =   rand(0,count($list)-1);
        foreach($list as $key=>$item){
            if($index==$key)
            {
                return $item;
            }
        }
    }
}
