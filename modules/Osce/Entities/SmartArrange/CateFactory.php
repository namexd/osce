<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 16:45
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\SmartArrange\Cate\Order;
use Modules\Osce\Entities\SmartArrange\Cate\Poll;
use Modules\Osce\Entities\SmartArrange\Cate\Random;

class CateFactory
{
    private function __construct()
    {
        throw new \Exception('为什么会想到实例化一个静态工厂类？把写代码的人拖出来打死！');
    }

    static function getCate($exam, $type) {

        switch ($type) {
            case 1:
                return new Random($exam);
            case 2:
                return new Order($exam);
            case 3:
                return new Poll($exam);
            default:
                throw new \Exception('Sequence Cate Error!');
                break;
        }
    }
}