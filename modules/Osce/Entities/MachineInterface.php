<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 16:37
 */

namespace Modules\Osce\Entities;


interface  MachineInterface
{
    public function getMachineStatuValues();
    public function addMachine($data);
    public function editMachine($data);
}