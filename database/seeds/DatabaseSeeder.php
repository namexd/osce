<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this   ->  call(ResourcesLabSeeder::class);
		$this   ->  command ->  info('lab Data Creating......');
        $this   ->  call(StudentProfessionalSeeder::class);
		$this   ->  command ->  info('Professional Data Creating......');
        $this   ->  call(TeacherDeptSeeder::class);
        $this   ->  command ->  info('TeacherDept Data Creating......');
        Model::reguard();
    }
}