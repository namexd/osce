<?php

use Illuminate\Database\Seeder;

class TeacherDeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->defaultData() as $input)
        {
            \Modules\Msc\Entities\TeacherDept::firstOrCreate($input);
        }
    }
    public function defaultData(){
        return  [
            [
                'name'  =>  '儿科',
                'code'   =>  '10001',
            ],
            [
                'name'  =>  '脑外科',
                'code'   =>  '10002',
            ],
            [
                'name'  =>  '血液科',
                'code'   =>  '10003',
            ],
            [
                'name'  =>  '皮肤科',
                'code'   =>  '10004',
            ],
            [
                'name'  =>  '骨科',
                'code'   =>  '10005',
            ],
            [
                'name'  =>  '牙科',
                'code'   =>  '10006',
            ]
        ];
    }
}
