<?php

use Illuminate\Database\Seeder;

class GroupsSeeder extends Seeder
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
            \Modules\Msc\Entities\Groups::firstOrCreate($input);
        }
    }

    public function defaultData() {
        return [
            [
                'name'   => '一组',
                'detail' => '一组',
                'student_class_id' => '1',
            ],
            [
                'name'   => '二组',
                'detail' => '二组',
                'student_class_id' => '1',
            ],
            [
                'name'   => '三组',
                'detail' => '三组',
                'student_class_id' => '1',
            ],
            [
                'name'   => '四组',
                'detail' => '四组',
                'student_class_id' => '2',
            ],
            [
                'name'   => '五组',
                'detail' => '五组',
                'student_class_id' => '2',
            ],
            [
                'name'   => '六组',
                'detail' => '六组',
                'student_class_id' => '3',
            ],
            [
                'name'   => '七组',
                'detail' => '七组',
                'student_class_id' => '3',
            ],
            [
                'name'   => '八组',
                'detail' => '八组',
                'student_class_id' => '4',
            ],
            [
                'name'   => '九组',
                'detail' => '九组',
                'student_class_id' => '5',
            ],
            [
                'name'   => '十组',
                'detail' => '十组',
                'student_class_id' => '6',
            ],
        ];
    }
}
