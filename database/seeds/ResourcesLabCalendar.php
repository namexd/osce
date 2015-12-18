<?php

use Illuminate\Database\Seeder;

class ResourcesLabCalendar extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data   =   $this->defaultData();
        foreach($data as $calendar){
            \Modules\Msc\Entities\ResourcesLabCalendar::firstOrCreate($calendar);
        }
    }
    public function timeList(){
        return [
            '08:00:00-09:00:00',
            '10:00:00-11:00:00',
            '11:00:00-12:00:00',
            '12:00:00-13:00:00',
            '13:00:00-14:00:00',
            '14:00:00-15:00:00',
        ];
    }
    public function defaultData(){
        $openlabList    =   \Modules\Msc\Entities\ResourcesClassroom::where('opened','=',0) ->get();
        $time   =   $this->getRandItem($this->timeList());
        $timeArray  =   explode('-',$time);
        $data   =   [];
        foreach($openlabList as $lab)
        {

            $calendar   =   [
                'resources_lab_id'  =>  $lab    ->id,
                'week'              =>  implode(',',[1,2,3,4,5]),
                'begintime'         =>  $timeArray[0],
                'endtime'           =>  $timeArray[1],
            ];
            $data[]     =   $calendar;
        }
        return $data;
    }
    protected function getRandItem($list){
        $index  =   rand(0,count($list)-1);
        foreach($list as $key=>$item){
            if($index==$key)
            {
                return $item;
            }
        }
    }
}
