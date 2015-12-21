<?php

use Illuminate\Database\Seeder;

class ResourcesLabVcrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vcrs=$this->getVcrs();
        $labs=$this->getLabs();
        for($i=1;$i<10;$i++){
            \Modules\Msc\Entities\ResourcesLabVcr::firstOrCreate([
                'resources_lab_id'  =>  $labs[array_rand($labs)],
                'vcr_id'            =>  $vcrs[array_rand($vcrs)],
            ]);
        }
    }
    public function getVcrs(){
        $vcrs=\Modules\Msc\Entities\Vcr::all();
        foreach($vcrs as $item){
            $data[]=$item->id;
        }
        return $data;
    }
    public function getLabs(){
        $labs=\Modules\Msc\Entities\ResourcesClassroom::all();
        foreach($labs as $item){
            $data[]=$item->id;
        }
        return $data;
    }
}
