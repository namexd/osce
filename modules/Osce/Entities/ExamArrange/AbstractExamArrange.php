<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 15:55
 */

namespace Modules\Osce\Entities\ExamArrange;

use Illuminate\Container\Container as App;

abstract class AbstractExamArrange
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

    abstract public function getModel();

}