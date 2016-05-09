<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/5/9
 * Time: 22:41
 */

namespace Modules\Osce\Entities\SmartArrange\Export;


use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Files\NewExcelFile;

class DengNeedListExport extends NewExcelFile
{
    public function getFilename()
    {
        return 'StudentList';
    }

    public function objToArray(Collection $collection)
    {
        if ($collection->isEmpty()) {
            throw new \Exception('当前排考计划没有保存');
        }
        
        //第一行为行头
        $data[] = ['学号', '姓名', '考试时间'];

        //设定数组的索引
        $k = 1;
//        dd($collection);
        //遍历集合,拼装数组
        foreach ($collection as $item) {
            foreach ($item as $j => $value) {
                if (0 == $j) {
                    $data[] = [
                        $value->code,
                        $value->name,
                        $value->begin_dt
                    ];
                } else {
                    $data[] = [
                        $value->code,
                        $value->name,
                        ' '
                    ];
                }

            }
        }
//        dd($data);
        return $data;
    }
}