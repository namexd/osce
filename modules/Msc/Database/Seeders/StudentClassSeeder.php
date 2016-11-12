<?php
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder{
    public function run()
    {
        foreach($this->defaultData() as $input)
        {
            \Modules\Msc\Entities\StudentClass::firstOrCreate($input);
        }
    }
    public function defaultData(){
        return  [
            [
                'code'   =>  '10001',
                'name'  =>  '一班',
            ],
            [
                'code'   =>  '10002',
                'name'  =>  '二班',
            ],
            [
                'code'   =>  '10003',
                'name'  =>  '三班',
            ],
            [
                'code'   =>  '10004',
                'name'  =>  '四班',
            ],
            [
                'code'   =>  '10005',
                'name'  =>  '五班',
            ],
            [
                'code'   =>  '10006',
                'name'  =>  '六班',
            ]
        ];
    }
}
