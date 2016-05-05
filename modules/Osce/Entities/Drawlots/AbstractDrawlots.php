<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/2
 * Time: 10:01
 */

namespace Modules\Osce\Entities\Drawlots;


abstract class AbstractDrawlots
{
    protected $params = null;

    protected $draw = null;

    protected $student = null;

    protected $studentObj = null;

    protected $station = null;

    protected $screen = null;

    protected $validator = null;

    public function __construct()
    {
        \App::bind('DrawInterface', function () {
            return new HuaxiSmarty();
        });

        $this->draw = \App::make('DrawInterface');

    }

    public function setParams($params)
    {
        $this->params = $params;
    }
}