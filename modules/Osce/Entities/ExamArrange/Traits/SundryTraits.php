<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 18:19
 */

namespace Modules\Osce\Entities\ExamArrange\Traits;


use Illuminate\Database\Eloquent\Collection;

trait SundryTraits
{
    /**
     * 获取集合与其去重集合的差
     * @param Collection $data1
     * @param Collection $data2
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-14 18:23
     */
    public function getDiff(Collection $data1, Collection $data2)
    {
        $keys1 = $data1->keys();
        $keys2 = $data2->keys();

        $diff = $keys1->diff($keys2);
        return $diff = $diff->first();
    }
}