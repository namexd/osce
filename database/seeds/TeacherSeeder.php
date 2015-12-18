<?php

use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
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
            \Modules\Msc\Entities\Teacher::firstOrCreate($input);
        }
    }
   
    public function defaultData(){
        return [
            [
                'name' => '蒲丹',
                'code' => '8012',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '马俊荣',
                'code' => '10069',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '何霄',
                'code' => '14277',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '张超',
                'code' => '10623',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '韩英',
                'code' => '10716',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '赵蓉',
                'code' => '1710',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '周舟',
                'code' => '9764',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '贺漫青',
                'code' => '15752',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '赵清江',
                'code' => '1756',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '岳中伟',
                'code' => '2396',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '曾多',
                'code' => '16346',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
            [
                'name' => '熊茂琦',
                'code' => '14064',
                'teacher_dept' =>1,
                'validated' => 1,
            ],
        ];
    }
}
