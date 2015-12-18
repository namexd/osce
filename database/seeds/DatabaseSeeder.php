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
        $this   ->  call(GroupsSeeder::class);
        $this   ->  command ->  info('Group Data Creating......');
        $this   ->  call(StudentGroupSeeder::class);
        $this   ->  command ->  info('StudentGroup Data Creating......');
        $this   ->  call(StudentClassSeeder::class);

        $this   ->  command ->  info('StudentClass Data Creating......');
        $this   ->  call(TeacherSeeder::class);
        $this   ->  command ->  info('Teacher Data Creating......');
        $this   ->  call(ResourcesLabVcrSeeder::class);
        $this   ->  command ->  info('ResourcesLabVcrSeeder Data Creating......');
        $this   ->  call(VcrSeeder::class);
        $this   ->  command ->  info('Vcr Data Creating......');
        Model::reguard();
    }
}