<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:01
 */

namespace Modules\Osce\Entities\Drawlots;

use Modules\Osce\Entities\Drawlots\Traits\SmartTraits;
use Modules\Osce\Entities\Drawlots\Validator\CheckTraits;
abstract class AbstractDrawlots
{
    use CheckTraits, SmartTraits;

    protected $params = null;

    public function setParams(array $params)
    {
        $this->params = $params;
    }
}