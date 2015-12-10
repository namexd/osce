<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 20:20
 */

namespace Modules\Msc\Repositories;

use Modules\Msc\Entities\ResourcesTools;
use Modules\Msc\Entities\ResourcesImage;
//use Modules\Msc\Repositories\BaseRepository;
//use Modules\Msc\Entities\ResourcesBorrowing;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Repositories\Common;



class ResourcesToolsRepository extends BaseRepository
{
    public function __construct(ResourcesTools $resourcesTools)
    {
        $this->model = $resourcesTools;
    }

    /**
     * 根据条件获取资源列表
     * @method GET /msc/admin/resources-manager/resources-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $where           筛选条件(必须的)
     * * string        $pagesize        一页几条数据(必须的)
     * * string        $order           排序(必须的)
     *
     * @return pagination
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 16:07
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getResourcesByParams ($where, $pagesize=10, $order=['id','desc'])
    {
        if (empty($where)) {
            $model = $this->model;
        } else {
            $model = $this->model;
            foreach ($where as $param) {
                $model = $model->where($param[0], $param[1], $param[2]);
            }
        }
        return $model->orderBy($order[0], $order[1])->paginate($pagesize);
    }

    /**
     * pc端资源列表页搜索
     * @access public
     *
     * * string        $keyword        搜索关键字(必须的)
     * * string        $feild          搜索字段(必须的)
     *
     * @return Model Builder
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 19:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getResourcesListByKeyword($keyword,$feild=''){
        return $this->model->searchByKeyword($keyword,$feild);
    }

}