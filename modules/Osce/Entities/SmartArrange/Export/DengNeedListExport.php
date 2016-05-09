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
            $tempArray = [];
            foreach ($item as $j => $value) {
                if (array_key_exists($value->student_id, $tempArray)) {
                    continue;
                }
                if (0 == $j) {
                    $tempArray[$value->student_id] = [
                        $value->code,
                        $value->name,
                        date('m-d H:i', strtotime($value->begin_dt))
                    ];
                } else {
                    $tempArray[$value->student_id] = [
                        $value->code,
                        $value->name,
                        ' '
                    ];
                }

            }
            $data = array_merge($data, $tempArray);
        }
        return $data;
    }
}