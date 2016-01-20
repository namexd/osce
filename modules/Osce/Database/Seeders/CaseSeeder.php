<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/10
 * Time: 11:31
 */
use Illuminate\Database\Seeder;

class CaseSeeder extends Seeder
{
    public function run()
    {
        $list = $this->caseData();
        foreach ($list as $item) {
            \Modules\Osce\Entities\CaseModel::firstOrCreate($item);
        }
    }

    public function caseData()
    {
        $data = [];
        $list = [];
        for ($i=0;$i<50;++$i) {
            $data['name'] = '测试病例' . mt_rand(1,50);
            $data['code'] = mt_rand(1,50);
            $data['description'] = '测试备注' . mt_rand(1,50);
            $data['created_user_id'] = mt_rand(42,98);
            $list[] = $data;
        }
    }
}