<?php

use Illuminate\Database\Seeder;

class LabCoursesSeeder extends Seeder
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
            \Modules\Msc\Entities\ResourcesClassroomCourses::firstOrCreate($input);
        }
    }
    public function defaultData(){

        $classroomList   =   \Modules\Msc\Entities\ResourcesClassroom::get();
        $coursesList     =   \Modules\Msc\Entities\Courses::get();
        $data   =   [];
        foreach($classroomList as $item){
            $index  =   rand(0,count($coursesList)-1);
            $data[] =   [
                'resources_lab_id'  =>  $item->id,
                'course_id'         =>  $coursesList[$index]->id,
            ];
        }
        return $data;
    }
}
