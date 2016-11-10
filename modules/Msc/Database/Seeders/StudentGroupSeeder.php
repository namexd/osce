<?php

use Illuminate\Database\Seeder;

class StudentGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach($this->defaultData() as $input)
        {
            \Modules\Msc\Entities\StdGroup::firstOrCreate($input);
        }
    }

    public function defaultData() {
        return [
            [
                'group_id' => '1',
                'student_id' => '1',
            ],
            [
                'group_id' => '1',
                'student_id' => '2',
            ],
            [
                'group_id' => '1',
                'student_id' => '3',
            ],
            [
                'group_id' => '2',
                'student_id' => '4',
            ],
            [
                'group_id' => '2',
                'student_id' => '5',
            ],
            [
                'group_id' => '2',
                'student_id' => '6',
            ],
            [
                'group_id' => '2',
                'student_id' => '7',
            ],
            [
                'group_id' => '3',
                'student_id' => '8',
            ],
            [
                'group_id' => '3',
                'student_id' => '9',
            ],
            [
                'group_id' => '3',
                'student_id' => '10',
            ],
            [
                'group_id' => '4',
                'student_id' => '11',
            ],
            [
                'group_id' => '4',
                'student_id' => '12',
            ],
        ];
    }
}
