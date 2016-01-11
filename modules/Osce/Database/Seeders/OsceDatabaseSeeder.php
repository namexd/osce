<?php namespace Modules\Osce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OsceDatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this   ->  call(\AreaSeeder::class);
		$this   ->  command ->  info('Area Data Creating......');

		$this   ->  call(\CaseSeeder::class);
		$this   ->  command ->  info('Case Data Creating......');

		Model::reguard();
	}

}