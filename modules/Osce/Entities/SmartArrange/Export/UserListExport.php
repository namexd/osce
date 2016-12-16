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
    /**
     * 返回该文件的文件名
     * @access public
     * @return string
     * @version 3.6
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-01
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getFilename()
    {
        return 'StudentList';
    }

    /**
     * 将查出来的对象转换为合适的数组返回
     * @access public
     * @param Collection $collection
     * @return array
     * @throws \Exception
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-01
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function objToArray(Collection $collection)
    {
        if ($collection->isEmpty()) {
            throw new \Exception('当前考试安排没有保存！，无法导出xlxs文件！');
        }


        /*
         * 该数组为返回的具体数据
         * 第一行为行头
         */
        $data[] = config('osce.smart_arrange.title');

        //设定个数组的索引
        $k = 1;

        //循环遍历对象集合，将合适的数据插入数组中
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

        //返回
        return $data;
    }
}