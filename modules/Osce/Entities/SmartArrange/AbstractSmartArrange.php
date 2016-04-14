<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 11:26
 */

namespace Modules\Osce\Entities\SmartArrange;

use Modules\Osce\Entities\SmartArrange\Traits\CheckTraits;
use Modules\Osce\Entities\SmartArrange\Traits\SQLTraits;
use Illuminate\Container\Container as App;


abstract class AbstractSmartArrange implements SmartArrangeInterface
{
    use CheckTraits, SQLTraits;

    private $app;

    protected $model;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * 类的名字
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-13 11:31
     */
    abstract function model();

    public function makeModel()
    {
        $model = $this->app->make($this->model());

        return $this->model = $model;
    }

    

}