<?php

use Illuminate\Database\Seeder;

class VcrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1;$i<10;$i++){
            \Modules\Msc\Entities\Vcr::firstOrCreate([
                'name' => '摄像机'.$i,
                'code' => '10000'.$i,
                'ip'   => '127.0.0.1',
                'username' => str_random(6),
                'password' => str_random(10),
                'port'     => 30+$i,
                'channel'  => str_random(10),
                'description' => str_random(10)
            ]);
        }
    }
}
