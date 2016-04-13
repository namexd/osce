<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 17:17
 */

namespace Modules\Osce\Entities\ConfigRepository;

use Illuminate\Container\Container as App;

abstract class AbstractConfig
{
    private $app;

    protected $model;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    public function makeModel()
    {
        $model = $this->app->make($this->getModel());

        return $this->model = $model;
    }

    abstract function getModel();

    public function create(array $data)
    {
        return $this->model->create($data);
    }

}