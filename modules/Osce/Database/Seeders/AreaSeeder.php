<?php
/**
 * Created by PhpStorm.
 * User: jiangzhiheng
 * Date: 2016/1/8
 * Time: 14:45
 */

use Illuminate\Database\Seeder;


class AreaSeeder extends Seeder
{

    public function run()
    {
        $list = $this->area();
        foreach ($list as $item) {
            \Modules\Osce\Entities\Area::firstOrCreate($item);
        }
    }

    public function area()
    {
        $data = [[
            'name' => '考场',
            'code' => '101',
            'cate' => '1',
            'created_user_id' => null,
        ],[
            'name' => '中控室',
            'code' => '102',
            'cate' => '2',
            'created_user_id' => null,
        ],[
            'name' => '走廊',
            'code' => '103',
            'cate' => '3',
            'created_user_id' => null,
        ],[
            'name' => '侯考区',
            'code' => '104',
            'cate' => '4',
            'created_user_id' => null,
        ]
        ];
        return $data;
    }
}