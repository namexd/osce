<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/1
 * Time: 9:31
 */

namespace Modules\Osce\Entities\SmartArrange\Export;


use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Files\NewExcelFile;

class UserListExport extends NewExcelFile
{
    public function getFilename()
    {
        return 'StudentList';
    }

    public function objToArray(Collection $collection)
    {
        if ($collection->isEmpty()) {
            throw new \Exception('当前考试安排没有保存！');
        }

        $data[] = config('osce.smart_arrange.title');

        $k = 1;

        foreach ($collection as $items) {
            foreach ($items as $j => $value) {
                if (0 == $j) {
                    $data[$k] = [
                        $value->grade_class,
                        $value->code,
                        $value->name,
                        $value->mobile,
                        date('Y-m-d',strtotime($value->begin_dt)),
                        date('H:i',strtotime($value->begin_dt)),
                        date('H:i',strtotime($value->end_dt))
                    ];
                } else {
                    $data[$k] = [
                        ' ',
                        ' ',
                        ' ',
                        ' ',
                        date('Y-m-d',strtotime($value->begin_dt)),
                        date('H:i',strtotime($value->begin_dt)),
                        date('H:i',strtotime($value->end_dt))
                    ];
                }
                $k++;
            }
        }
        return $data;

    }
}