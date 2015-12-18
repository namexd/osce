<?php namespace Modules\Msc\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class MscDatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		for ($i = 1;$i<=10;++$i) {
			DB::table('student_class')->insert([
				'code' => '100'.$i,
				'name' => '测试班级'.$i,
			]);
		}
	}
}