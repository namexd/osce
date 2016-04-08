<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/7
 * Time: 15:43
 */

namespace Modules\Osce\Entities\SmartArrange;


use Modules\Osce\Entities\SmartArrange\Entity\RoomMode;
use Modules\Osce\Entities\SmartArrange\Entity\StationMode;

class ModeFactory
{
    private function __construct()
    {
        throw new \Exception('为什么会想到实例化一个静态工厂类！把写代码的人抓出来打死！');
    }

    static public function getMode($exam)
    {
        try {
            switch ($exam->sequence_mode) {
                case 1:

                    return new RoomMode();
                case 2:

                    return new StationMode();
                default:
                    throw new \Exception('Sequence Mode error!');
                    break;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}