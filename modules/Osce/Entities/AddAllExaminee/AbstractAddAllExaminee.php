<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/20
 * Time: 17:08
 */

namespace Modules\Osce\Entities\AddAllExaminee;

use Illuminate\Container\Container as App;
use Illuminate\Http\Request;

abstract class AbstractAddAllExaminee
{
    private $app;

    protected $model;

    abstract public function model();

    public function __construct(App $app, Request $request, $fileName)
    {
        $this->app = $app;
        $this->makeModel($request, $fileName);
    }

    public function makeModel(Request $request, $fileName)
    {
        $model = $this->app->make($this->model());

        $this->model = $model;
        $this->model->setData($request, $fileName);
    }
}