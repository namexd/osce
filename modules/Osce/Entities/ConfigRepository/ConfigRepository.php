<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 17:21
 */

namespace Modules\Osce\Entities\ConfigRepository;


class ConfigRepository extends AbstractConfig
{
    private $className;

    public function setModel($model = 'Modules\Osce\Entities\ConfigRepository\SysParam')
    {
        return $this->className = $model;
    }

    public function getModel()
    {
        return $this->className;
    }
}