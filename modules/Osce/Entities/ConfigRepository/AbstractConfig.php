<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/13
 * Time: 17:17
 */

namespace Modules\Osce\Entities\ConfigRepository;

use Illuminate\Container\Container as App;

abstract class AbstractConfig implements ConfigInterface
{
    private $app;

    protected $model;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * 返回对象实例
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-13 20:25
     */
    public function makeModel()
    {
        $this->setModel();
        $model = $this->app->make($this->getModel());

        return $this->model = $model;
    }

    /**
     * 获取模型中的数据
     * @author Jiangzhiheng
     * @time 2016-04-14 11:00
     */
    public function getData()
    {
        return $this->model->getData();
    }

    /**
     * 将数据写入模型
     * @param array $data
     * @author Jiangzhiheng
     * @time 2016-04-14 11:01
     */
    public function setData(array $data)
    {
        return $this->model->setData($data);
    }

    /**
     * 返回对象的类名
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-13 20：27
     */
    abstract function getModel();

    /**
     * 设置类名
     * @param $model
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-14 10:14
     */
    abstract function setModel();

}