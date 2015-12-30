<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 14:09
 */

namespace Modules\Osce\Repositories;

use Modules\Osce\Entities\Place;
use Modules\Osce\Entities\PlaceCate;
class Factory
{
    public static function place()
    {
        $place = new Place();
        return $place;
    }

    public static function placeCate()
    {
        $placeCate = new PlaceCate();
        return $placeCate;
    }
}