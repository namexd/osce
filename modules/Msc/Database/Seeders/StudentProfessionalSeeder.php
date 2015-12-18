<?php

use Illuminate\Database\Seeder;

class StudentProfessionalSeeder extends Seeder
{
    public function run()
    {
        foreach($this->defaultData() as $input)
        {
            \Modules\Msc\Entities\StdProfessional::firstOrCreate($input);
        }
    }
    public function defaultData(){
        return  [
            [
                'name'  =>  '临床医学',
                'code'   =>  '10001',
            ],
            [
                'name'  =>  '口腔专业',
                'code'   =>  '10002',
            ],
            [
                'name'  =>  '护理',
                'code'   =>  '10003',
            ],
            [
                'name'  =>  '公共卫生',
                'code'   =>  '10004',
            ],
            [
                'name'  =>  '防疫',
                'code'   =>  '10005',
            ],
            [
                'name'  =>  '康复保健',
                'code'   =>  '10006',
            ]
        ];
    }
}
