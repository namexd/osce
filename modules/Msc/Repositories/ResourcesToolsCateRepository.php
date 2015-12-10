<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 20:20
 */

namespace Modules\Msc\Repositories;

use Modules\Msc\Entities\ResourcesToolsCate;
use Modules\Msc\Entities\ResourcesImage;
//use Modules\Msc\Repositories\BaseRepository;
//use Modules\Msc\Entities\ResourcesBorrowing;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Repositories\Common;



class ResourcesToolsCateRepository extends BaseRepository
{
    public function __construct(ResourcesToolsCate $resourcesToolsCate)
    {
        $this->model = $resourcesToolsCate;
    }

    /**
     * 获取设备类别树
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $pid        类别父id(必须的)
     *
     * @return array
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-26 9:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCateTree ($pid=0)
    {
        $list = $this->model->where('pid', $pid)->get();

        $data = [];
        foreach ($list as $k => $v)
        {
            $temp = [];
            $temp['id'] = $v->id;
            $temp['name'] = $v->name;
            $data[$k] = $temp;
            $data[$k]['sub'] = $this->getCateTree($v->id);
        }

        return $data;
    }


}