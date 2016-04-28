<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/27
 * Time: 17:21
 */

namespace Modules\Osce\Entities\ConfigRepository\Data;

use Illuminate\Container\Container as App;
class DataFromDB
{
    private $name = [];

    private $app;

    private $models;

    function __construct(array $name)
    {
        $this->app = new App();
        $this->name = $name;
    }

    function getModel()
    {
        $models = [];
        foreach ($this->name as $item) {
            $models[] = $this->app->make($item);
        }

        return $this->models = $models;
    }

//    function getData(array $params)
//    {
//        $data = [];
//        foreach ($this->models as $model) {
//            $data[$model->getTable()] =
//        }
//    }
}