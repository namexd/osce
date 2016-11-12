<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 10:50
 */

namespace Modules\Osce\Entities\ConfigRepository;


interface ConfigInterface
{
    function setData(array $data);

    function getData();
}