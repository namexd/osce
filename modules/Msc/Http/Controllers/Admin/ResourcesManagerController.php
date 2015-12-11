<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 18:33
 */

namespace Modules\Msc\Http\Controllers\Admin;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesBorrowing;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesToolsCate;
use Modules\Msc\Entities\ResourcesTools;
use Modules\Msc\Entities\ResourcesToolsItems;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Entities\ResourcesLocation;
use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class ResourcesManagerController extends MscController
{
	// 二维码 http://api.mis.local/msc/wechat/resource/resource-view?id=1&code=123
	
    public function getTest(){
    }

    /**
     * 资源列表(设备)
     * @method GET /msc/admin/resources-manager/resources-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           cate_id         类别ID(必须的)
     * * string        order_name      排序字段名(必须的)
     * * string        order_type      排序方式(必须的) 1 ：Desc 0:asc
     * * string        keywords        关键字(必须的)
     * * int           page            页码(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 14:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * togo： Luohaihua 2015-11-26 ResourcesList 方法的封装
     */
    public function getResourcesList(Request $request)
    {
        $pagination = $this->ResourcesList($request);
        $data = [];

        foreach ($pagination as $item)
        {
            // 获取设备code信息
            $resourcesToolsItems = ResourcesToolsItems::where('resources_tool_id', $item->id)->get();

            $temp = [];
            foreach ($resourcesToolsItems as $resourcesToolsItem)
            {
                $temp[] = $resourcesToolsItem->code;
            }

            $categroy = $item->categroy;
            //$address = $resources->address;
            $resources = $item->resources;
            $data[] = [
                'id'             => $item->id, // 设备单品id
                'name'           => $item->name, // 设备名称
                'categoryName'   => is_null($categroy) ? '-' : $categroy->name, // 设备所属分类
                'manager_name'   => $item->manager_name, // 设备负责人
                'manager_mobile' => $item->manager_mobile, // 设备负责人电话
                'locationName'   => $item->location, // 设备位置
                'resourcesId'    => is_null($resources)? '': $resources->id, // 生成二维码所需要的resource_id
                'codes'          => $temp, // code信息
            ];
        }

        //dd($data);
        return view('msc::admin.resourcemanage.Existing', ['list'=>$data, 'pagination'=>$pagination]);
    }

    /**
     *
     * @api GET /msc/admin/resources-manager/resources-list-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int           cate_id         类别ID(必须的)
     * * string        order_name      排序字段名(必须的)
     * * string        order_type      排序方式(必须的) 1 ：Desc 0:asc
     * * string        keywords        关键字(必须的)
     * * int           page            页码(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResourcesListData(Request $request){
        $pagination = $this->ResourcesList($request);
        $paginationArray = $pagination->toArray();

        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }
    private function ResourcesList(Request $request){
        $this->validate($request, [
            'cate_id' 			=> 	'sometimes|integer',
            'order_name' 		=> 	'sometimes|max:50',
            'order_type'		=> 	'sometimes|integer|min:0|max:1',
            'keyword' 		    => 	'sometimes',
        ]);

        $cateId    = (int)Input::get('cate_id');
        $orderName = e(Input::get('order_name'));
        $orderType = (int)Input::get('order_type');
        $keyword   = urldecode(Input::get('keyword'));

        $where = [];
        if(!empty($orderName))
        {
            if($orderType)
            {
                $order = [$orderName, 'desc'];
            }
            else
            {
                $order = [$orderName, 'asc'];
            }
        }
        else
        {
            $order = ['id', 'desc']; // 默认按照ID降序排列
        }

        $resourcesToolsRepository = App::make('Modules\Msc\Repositories\ResourcesToolsRepository');
        if(empty($keyword))
        {
            if(!empty($cateId))
            {
                $where[] = ['cate_id', '=', $cateId];
            }
            $pagination = $resourcesToolsRepository->getResourcesByParams($where, 10, $order);
        }
        else
        {
            $bulider = $resourcesToolsRepository->getResourcesListByKeyword($keyword, 'name');
            if(!empty($cateId))
            {
                $bulider = $bulider->where('cate_id','=',$cateId);
            }
            $pagination = $bulider->orderBy($order[0],$order[1])->paginate(10);
        }

        return $pagination;
    }

    /**
     * 获取一类资源的信息
     * @method GET /msc/admin/resources-manager/resources?id=n(n为正整数)
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        资源ID(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 17:49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getResources(Request $request){
        $this->validate($request, [
            'id' 	=> 	'required|integer',
        ]);

        $id = (int)Input::get('id');

        $resources = ResourcesTools::find($id);
        if (!$resources)
        {
            return response()->json(
                $this->fail(new \Exception('资源不存在'))
            );
        }

        $categroy = $resources->categroy;
        $items    = $resources->items;
        if (!$resources->resources)
        {
           throw new \Exception('资源不存在');
        }

        $images = $resources->resources->images;
        if($images)
        {
            $imagesArray = $images->toArray();
        }
        else
        {
            $imagesArray = [];
        }

        $data = [
            'id'             => $resources->id, // 设备单品id
            'name'           => $resources->name, // 设备名称
            'categoryName'   => is_null($categroy) ? '-' : $categroy->name, // 设备所属分类
            'manager_name'   => $resources->manager_name, // 设备负责人
            'manager_mobile' => $resources->manager_mobile, // 设备负责人电话
            'locationName'   => $resources->location, // 设备位置
            'detail'         => $resources->detail, // 功能描述
            'image'          => $imagesArray, // 设备图片
            'items'          => $items, // 该类设备包含的单品列表
        ];

        return view('msc::admin.resourcemanage.Existing_read', ['resource'=>$data]);
    }

    /**
     * 资源类别管理列表
     * @method GET /msc/admin/resources-manager/resources-cate-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-25 14:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getResourcesCateList (Request $request)
    {
        //$resourcesToolsCateRepository = App::make('\Modules\Msc\Repositories\ResourcesToolsCateRepository');
        //$cateTree = $resourcesToolsCateRepository->getCateTree();

        // 获取第一级分类
        $firstLvCateList = ResourcesToolsCate::where('pid', 0)->get()->toArray();

        return view ('msc::admin.resourcemanage.cate_list', ['list'=>$firstLvCateList]);
    }

    /**
     * 根据pid创建一个资源分类实体
     * @method POST
     * @url /msc/admin/resources-manager/add-cate-by-pid
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        $pid        分类pid
     *
     * @return int 新增分类实体id/false
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-11 15:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddCateByPid (Request $request)
    {
        $this->validate($request,[
            'pid'  => 'required|integer',
        ]);

        $pid = $request->input('pid');

        $data = [
            'repeat_max'     => 0,
            'pid'            => $pid,
            'name'           => '',
            'manager_id'     => 0,
            'manager_name'   => '',
            'manager_mobile' => '',
            'location'       => '',
            'detail'         => '',
            'loan_days'      => 0,
        ];

        $cate = ResourcesToolsCate::create($data);

        if ($cate instanceof ResourcesToolsCate)
        {
            $cateId = $cate->id;

            return response()->json(
                $this->success_data($cateId)
            );
        }
        else
        {
            return response()->json(false);
        }
    }

    /**
     * 新增资源工具类别
     * @method POST /msc/admin/resources-manager/add-resources-tools-cate
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           $pid           类别父id(必须的)-若pid不存在，默认添加到第一级；pid存在，添加到该级下面
     * * string        $name          类别名称(必须的)
     *
     * @return json-成功返回新增子分类id,name-失败返回false
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-25 14:17
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddResourcesToolsCate (Request $request)
    {
        $this->validate($request,[
            'pid'  => 'sometimes|integer',
            'name' => 'required|max:50|min:0',
        ]);

        $pid  = $request->input('pid', 0);
        $name = urldecode(e($request->input('name')));

        $data = [
            'pid' => $pid,
            'name' => $name,
        ];

        $resourceCate = ResourcesToolsCate::create($data);
        if ($resourceCate)
        {
            return response()->json(
                [
                    'id'   => $resourceCate->id,
                    'name' => $resourceCate->name,
                ]
            );
        }
        else
        {
            return response()->json(false);
        }
    }

    /**
     * 修改资源工具类别名称
     * @method POST /msc/admin/resources-manager/edit-resources-tools-cate
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           $id            类别id(必须的)
     * * string        $name          新类别名称(必须的)
     *
     * @return boolean
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-26 10:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditResourcesToolsCate (Request $request)
    {
        $this->validate($request,[
            'id'   => 'required|integer',
            'key' => 'required',
        ]);
        $key = $request->input('key');
        $val = $request->input('val');
        $id   = $request->input('id');
        $resourcesToolsCate = ResourcesToolsCate::find($id);
        $resourcesToolsCate->$key = $val;

        return response()->json($resourcesToolsCate->save());
    }

    /**
     * 删除资源工具类别-如果该分类下面有设备，则不允许删除
     * @method POST /msc/admin/resources-manager/del-resources-tools-cate
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           $id            类别id(必须的)
     *
     * @return mixed-如果该分类下面没有设备，则进行删除，返回bool;反之返回有几个设备(数目)
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-26 10:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postDelResourcesToolsCate (Request $request)
    {
        $this->validate($request,[
            'id' => 'required|integer',
        ]);

        $id = $request->input('id');

        // 判断该分类下面是否有设备
        $resourcesTools = ResourcesTools::where('cate_id', $id)->get();
        if (!$resourcesTools)
        {
            return response()->json(count($resourcesTools));
        }
        else
        {
            $resourcesToolsCate = ResourcesToolsCate::find($id);
            return response()->json($resourcesToolsCate->delete());
        }
    }

    /**
     * 新增资源-表单
     * @method GET /msc/admin/resources-manager/add-resources
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 18:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddResources(Request $request)
    {
        $resourcesCateList = ResourcesToolsCate::where('pid', '=', '0')->get();

        return view('msc::admin.resourcemanage.add', ['resourcesCateList'=>$resourcesCateList]);
    }

    /**
     * 异步获取设备子分类
     * @method GET /msc/admin/resources-manager/ajax-resources-tools-cate
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        设备父分类id(必须的)
     *
     * @return json
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-25 16:49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAjaxResourcesToolsCate (Request $request)
    {
        $this->validate($request,[
            'id' => 'required|integer',
        ]);

        $pid = (int) Input::get('id');
        $childCateList = ResourcesToolsCate::where('pid', '=', $pid)->get();

        return response()->json($childCateList->toArray());
    }

    /**
     * 新增资源
     * @method POST /msc/admin/resources-manager/add-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        resources_type      新增资源的TYPE(必须的)  外借设备为 TOOLS  教室 为ClASSROOM
     * * string        name                设备名称(必须的)
     * * int           cate_id             设备名称(必须的)
     * * string        manager_name        设备负责人(必须的)
     * * string        manager_mobile      设备负责人电话(必须的)
     * * string        location            地址(必须的)
     * * string        detail              设备功能(必须的)
     * * Array         code                编码(必须的)<input type="hidden" name="code[]" value="123415123">
     * * Array         images_path         图片路径(必须的) e.g:<input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447430.png">
     *
     * @return Response
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 18:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddResources(Request $request){
        $resources_type=$request->get('resources_type');
        switch($resources_type){
            case 'TOOLS':
                $view=$this->addToolsResources($request);
                break;
            case 'CLASSROOM':
                //$view=$this->addClassRommResources($request);
                break;
            default:
                return redirect()->back()->withErrors(['没有选择新增资源type']);
        }
        return $view;
    }

    /*
    * 新增外借设备
    */
    private function addToolsResources(Request $request){
        $this->validate($request,[
            'repeat_max'     => 'sometimes|max:4|min:0',
            'name'           => 'required|max:50|min:0',
            'cate_id'        => 'required|integer',
            'manager_name'   => 'required|max:50|min:0',
            'manager_mobile' => 'required|mobile_phone',
            'location'       => 'required|max:50|min:0',
            'detail'         => 'sometimes|max:255|min:0',
            'loan_days'      => 'sometimes|integer',
        ]);

        $formData    = $request->only(['repeat_max', 'name', 'cate_id', 'manager_name', 'manager_mobile', 'location', 'detail', 'loan_days']);
        $codeList    = $request->get('code');
        $imagesArray = $request->input('images_path');

        $connection = DB::connection('msc_mis');
        try
        {
            foreach($codeList as $code)
            {
                if(empty($code))
                {
                    throw new \Exception('编码不能为空');
                }
            }
            $connection->beginTransaction();
            $formData['total'] = count($codeList);
            $formData['repeat_max'] = empty($formData['repeat_max']) ? 0 : $formData['repeat_max'];
            $formData['loan_days']  = empty($formData['loan_days']) ? 0 : $formData['loan_days'];

            $resources = ResourcesTools::create($formData);
            if(!$resources)
            {
                throw new \Exception('新增资源失败');
            }

            $_formData = [
                'type'        => 'TOOLS',
                'item_id'     => $resources->id,
                'description' => '',
            ];

            $_resources = Resources::create($_formData);
            if(!$_resources)
            {
                throw new \Exception('新增资源失败');
            }

            if (!empty($imagesArray))
            {
                foreach($imagesArray as $item)
                {
                    $data=[
                        'resources_id' => $_resources->id,
                        'url'          => $item,
                        'order'        => 0,
                        'descrption'   => '',
                    ];
                    $result = ResourcesImage::create($data);
                    if(!$result)
                    {
                        throw new \Exception('资源图片保存失败');
                    }
                }
            }

            if (!empty($codeList))
            {
                foreach($codeList as $code)
                {
                    $itemData = [
                        'resources_tool_id' => $resources->id,
                        'code'              => $code,
                        'status'            => 1,
                    ];
                    $result = ResourcesToolsItems::create($itemData);
                    if(!$result)
                    {
                        throw new \Exception('资源编码保存失败');
                    }
                }
            }

            $connection->commit();
            return redirect()->action('\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@getAddResources');
        }
        catch(\Exception $ex)
        {
            $connection->rollback();
            return redirect()->back()->withErrors($ex); // todo with some status code
        }
    }

    /**
     * 编辑资源-表单
     * @method GET /msc/admin/resources-manager/edit-resources
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        ID        资源id(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 18:37
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditResources(Request $request)
    {
        $this->validate($request, [
            'id'   => 	'required|integer',
        ]);

        $id = (int)Input::get('id');

        $resources = ResourcesTools::find($id);
        if(!$resources)
        {
            throw new \Exception('资源不存在');
        }

        $categroy  = $resources->categroy;
        if (!$resources->resources)
        {
            throw new \Exception('资源不存在');
        }

        $images = $resources->resources->images;
        $items  = $resources->items;
        if($images)
        {
            $imagesArray = $images->toArray();
        }
        else
        {
            $imagesArray = [];
        }

        $data = [
            'id'             => $resources->id, // 设备单品id
            'name'           => $resources->name, // 设备名称
            //'categoryName'   => is_null($categroy) ? '-' : $categroy->name, // 设备所属分类
            'cate_id'        => $resources->cate_id, // 设备分类id
            'manager_name'   => $resources->manager_name, // 设备负责人
            'manager_mobile' => $resources->manager_mobile, // 设备负责人电话
            'locationName'   => $resources->location, // 设备位置
            'image'          => $imagesArray, // 设备图片
            'detail'         => $resources->detail, //设备描述
            'items'          => $items, // 该类设备下面单品列表
        ];

        if($categroy)
        {
            $data['cate_pid'] = $categroy->pid;
        }
        else
        {
            $data['cate_pid'] = null;
        }

        $resourcesToolsCateRepository = App::make('\Modules\Msc\Repositories\ResourcesToolsCateRepository');
        $cateTree = $resourcesToolsCateRepository->getCateTree();

        return view('msc::admin.resourcemanage.Existing_edit', ['resource'=>$data, 'cateTree'=>$cateTree]);
    }

    /**
     * 编辑资源
     * @method POST /msc/admin/resources-manager/edit-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id                资源ID(必须的)
     * * string        name              资源名称(必须的)
     * * int           cate_id           资源类别ID(必须的)
     * * string        manager_name      资源负责人姓名(必须的)
     * * string        manager_mobile    资源负责人电话(必须的)
     * * string        location          资源地址(必须的)
     * * string        detail            资源表述(必须的)
     * * Array         images_path       图片 e.g:<input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447430.png">
     * @return Response
     *
     * @version 0.2
     * @author wangjiang <Luohaihua@misrobot.com>
     * @date 2015-11-23 18:44
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditResources(Request $request){
        $this->validate($request,[
            'id'             => 'required|integer',
            'name'           => 'required|max:50|min:0',
            'cate_id'        => 'required|integer',
            'manager_name'   => 'required|max:50|min:0',
            'manager_mobile' => 'required|mobile_phone',
            'location'       => 'required|max:50|min:0',
            'detail'         => 'sometimes|max:255|min:0',
        ]);

        $formData = $request->only(['id', 'images_path']);
        $id = (int)$formData['id'];
        $resourcesRepository = App::make('\Modules\Msc\Repositories\ResourcesRepository');

        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();

        //删除修改后删除的图片
        $resourcesTools = ResourcesTools::find($id);
        try{
            $resourcesRepository->ResourcesImageDel($resourcesTools->resources->id, $formData['images_path']);
        }
        catch(\Exception $ex)
        {
            throw new \Exception('删除图片失败');
        }

        //添加修改后增加的图片
        $hasList = ResourcesImage::where('resources_id', $resourcesTools->resources->id)->get();
        if(!empty($hasList))
        {
            $hasData = [];
            foreach ($hasList as $item) {
                $hasData[] = $item->url;
            }
            $imagePathCopy = $formData['images_path'];
            if ($imagePathCopy)
            {
                foreach($formData['images_path'] as $key=>$path)
                {
                    if(in_array($path, $hasData))
                    {
                        unset($imagePathCopy[$key]);
                    }
                }
            }
            $ImageNew = $imagePathCopy;
            unset($imagePathCopy);
        }
        else
        {
            $ImageNew = $formData['images_path'];
        }

        if (!empty($ImageNew))
        {
            foreach($ImageNew as $item)
            {
                $data = [
                    'resources_id' => $resourcesTools->resources->id,
                    'url'          => $item,
                    'order'        => 0,
                    'descrption'   => '',
                ];
                $result = ResourcesImage::create($data);
                if(!$result)
                {
                    throw new \Exception('资源图片保存失败');
                }
            }
        }

        // 修改资源信息
        $resourcesData = $request->only(['name', 'cate_id', 'manager_name', 'manager_mobile', 'location_id', 'detail']);
        if($resourcesTools->update($resourcesData))
        {
            $connection->commit();
            return back();
        }
        else
        {
            $connection->rollback();
            return back();
        }
    }

    /**
     * 报废资源-表单
     * @method GET /msc/admin/resources-manager/rejected-resources
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        资源编号(必须的)
     *
     * @return View
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 19:49
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRejectedResources(Request $request)
    {
        $this->validate($request, [
            'id' 		=> 	'required|integer',
        ]);

        $id = (int)Input::get('id');

        $resources = ResourcesTools::find($id);
        if(!$resources)
        {
            return response()->json(
                $this->fail(new \Exception('资源不存在'))
            );
        }
				
		$resourcesItems = [];
		foreach ($resources->items as $resourcesToolsItems)
		{
            if ($resourcesToolsItems->status != -1)
            {
                $resourcesItems[] = $resourcesToolsItems->toArray();
            }
		} 		
		$resources = $resources->toArray();


        return response()->json(
        	[
				'resources'      => $resources,
				'resourcesItems' => $resourcesItems,
			]
		);                                           
    }

    /**
     * 报废设备
     * @method POST /msc/admin/resources-manager/rejected-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id                   报废设备ID(必须的,resourcesTools)
     * * string        code                 报废设备的编码(必须的，resourcesToolsItems)
     * * string        reject_detail        报废描述(必须的)
     *
     * @return json {'id'：设备ID,'name':设备名称,code:设备编码,is_rejected:是否报废,reject_detail:报废说明,reject_date:报废日期}
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 20:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postRejectedResources(Request $request){
        $this->validate($request,[
            'id'            => 'required|integer',
            'reject_detail' => 'required|max:255|min:0',
            'code'          => 'required',
        ]);

        $formData = $request->only(['reject_detail' ,'id', 'code']);

        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();

        if (0 == $formData['code'])
        {
            // 报废该类型下面所有编号设备
            $resourcesItems = ResourcesToolsItems::where('resources_tool_id', $formData['id'])->get();
            foreach ($resourcesItems as $item)
            {
                $item->status        = -1; // 已报废
                $item->reject_detail = $formData['reject_detail'];
                $item->reject_date   = date('Y-m-d H:i:s');

                $result = $item->save();
                if(!$result)
                {
                    $connection->rollback();
                    return back();
                }
            }
        }
        else
        {
            $resourcesItemBuilder = ResourcesToolsItems::where('resources_tool_id', $formData['id']);
            $resourcesItem = $resourcesItemBuilder->where('code', $formData['code'])->firstOrFail();

            $resourcesItem->status        = -1; // 已报废
            $resourcesItem->reject_detail = $formData['reject_detail'];
            $resourcesItem->reject_date   = date('Y-m-d H:i:s');

            $result = $resourcesItem->save();
            if(!$result)
            {
                $connection->rollback();
                return back();
            }
        }

        $connection->commit();
        return back();
    }

    /**
     * 批量报废设备
     * @method POST /msc/admin/resources-manager/rejected-resources-all
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        ids        报废设备ID数组(必须的,resourcesTools)
     *
     * @return Msg
     *
     * @version 0.2
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-24 20:12
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postRejectedResourcesAll(Request $request){
        $this->validate($request,[
            'ids'=>'required|array',
        ]);

        $formData = $request->input('ids');

        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();

        foreach ($formData as $id)
        {
            $resourcesTools = ResourcesTools::find($id);
            if (!$resourcesTools)
            {
                throw new \Exception('该类资源不存在');
            }

            foreach ($resourcesTools->items as $item)
            {
                $item->status        = -1; // 已报废
                $item->reject_detail = '批量报废';
                $item->reject_date   = date('Y-m-d H:i:s');

                $result = $item->save();
                if(!$result)
                {
                    $connection->rollback();
                    return response()->json(
                        $this->fail(new \Exception('批量报废失败'))
                    );
                }
            }
        }

        $connection->commit();
        return response()->json(
            $this->success_data($formData, 1, '批量报废成功')
        );
    }









    /**
     * 删除资源(物理删除，不是报废)
     * @method POST /msc/admin/resources-manager/resources-del
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        资源ID(必须的)
     *
     * @return json  ['result:true']
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postResourcesDel(Request $request){
        $this->validate($request, [
            'id' 		=> 	'required',
        ]);
        $id=(int)Input::get('id');
        $resources=Resources::find($id);
        $result=$resources->delete();
        if($result)
        {
            $imagesData=ResourcesImage::where('Resources_id','=',$id)->get();
            $imageslist=[];
            foreach($imagesData as $images)
            {
                $imageslist[]=$images->url;
            }
            //如果有图片 则删除 图片
            if(!empty($imageslist))
            {
                $resourcesRepository = App::make('App\Repositories\ResourcesRepository');
                try{
                    $resourcesRepository->ResourcesImageDel($id,$imageslist);
                }
                catch(\Exception $ex)
                {
                    //图片已经不存在
                }
            }
            return response()->json(
                $this->success_data(['result'=>true],1,'删除成功')
            );
        }
        else
        {
            return response()->json($this->fail(new \Exception('删除失败')));
        }
    }

    /**
     * 增加资源地址-表单
     * @method GET /msc/admin/resources-manager/add-address
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return response
     *
     * @version 1.0
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-20 18:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddAddress(Request $request)
    {
        return view('msc::admin.resources.manager.address');
    }

    /**
     * 地址新增
     * @method POST /msc/admin/resources-manager/add-address
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name        地址(必须的)
     * * string        code        地址编码(必须的)（可重复）
     * * string        pid        上级地址ID(必须的)
     * * string        description        地址描述(必须的)
     *
     * @return json {'id':地址ID,'code':地址编码,'name':地址,'pid':上级地址ID,'level':地址层级,'description':地址描述,}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddAddress(Request $request){
        $formData=$request->only(['name','code','pid','description']);
        $this->validate($request,[
            'name'=>'required',
            'code'=>'required',
            'pid'=>'required',
        ]);
        $formData['name']=e($formData['name']);
        $formData['code']=e($formData['code']);
        $formData['pid']=intval($formData['pid']);
        if(empty($formData['pid']))
        {
            $level=0;
        }
        else
        {
            $parentLocation=ResourcesLocation::find($formData['pid']);
            $level=$parentLocation->level+1;//子集的层级永远比父级多1
        }
        $formData['level']=$level;

        $location=ResourcesLocation::create($formData);
        if($location)
        {
            $returnData=[
                'id'=>$location->id,
                'code'=>$location->code,
                'name'=>$location->name,
                'pid'=>$location->pid,
                'level'=>$location->level,
                'description'=>$location->description,
            ];
            return response()->json(
                $this->success_data($returnData,1,'新增成功')
            );
        }
        else
        {
            return response()->json($this->fail(new \Exception('新增失败')));
        }
    }

    /**
     * 根据关键字获取地址(有翻页)-资源地址列表
     * @method GET /msc/admin/resources-manager/resources-location-list
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        keyword        关键字(必须的)
     *
     * @return json {'id':地址ID,'code':地址编码,'name':地址,'pid':上级地址ID,'level':地址层级,'description':地址描述,}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-13 17:29
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResourcesLocationList(Request $request){
        $this->validate($request, [
            'keyword' 		=> 	'sometimes'
        ]);
        $keyword=e(Input::get('keyword'));
        $flag_rejected=e(Input::get('flag_rejected'));

        $resourcesLocationRepository = App::make('App\Repositories\ResourcesLocationRepository');
        $pagination= $resourcesLocationRepository->getResourcesLocationListByKeuword($keyword)->orderBy('id','desc')->paginate(20);
        $paginationArray=$pagination->toArray();

        return view('msc::admin.resources.manager.address-list');
        /*
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
        */
    }

    /**
     * 获取类别列表
     * @method GET /msc/admin/resources-manager/categroy-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * * string        pid        父级ID
     *
     * @return json {name:'名称',pid:'父id',level:'层级',description:'备注',}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCategroyList(Request $request){
        $pid=(int)$request->get('pid');
        $pid=$pid? $pid:0;
        $resourcesCate=new ResourcesCate();
        $pagination=$resourcesCate->where('pid','=',$pid)->orderBy('id','desc')->paginate(20);
        $paginationArray=$pagination->toArray();

        return view('msc::admin.resources.manager.category-list');
        /*
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
        */
    }

    /**
     *根据条件 获取 外借记录
     * @api GET /msc/admin/resources-manager/borrow-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        code             编号
     * * int        resources_id     资源ID
     * * string        begindate        预约借出时间
     * * string        enddate          预约归还时间
     * * string        real_begindate   实际借出时间
     * * string        real_enddate     实际归还时间
     * * int        lender           借出人id
     * * int        agent_id         借出代理人id
     * * string        agent_name       借出代理人姓名
     * * int        loan_operator_id     借出_经办人id
     * * int        return_operator_id   归还_经办人id(
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getBorrowList(Request $request){
        $this->validate($request, [
            'code'          => 	'sometimes',
            'resources_id'  => 	'sometimes|integer',
            'begindate'     => 	'sometimes|date_format:Y-m-d',
            'enddate'       => 	'sometimes|date_format:Y-m-d',
            'real_begindate'=> 	'sometimes|date_format:Y-m-d H:i:s',
            'real_enddate'  => 	'sometimes|date_format:Y-m-d H:i:s',
            'lender'        => 	'sometimes',
            'agent_id' 		=> 	'sometimes|integer',
            'agent_name'    => 	'sometimes',
            'loan_operator_id'  => 	'sometimes|integer',
            'return_operator_id'=> 	'sometimes|integer',
        ]);
        $formData=$request->only(['code','resources_id','begindate','enddate','real_begindate','real_enddate','lender','agent_id','agent_name','loan_operator_id','return_operator_id']);
        $where=[];
        foreach($formData as $key=>$data)
        {
            $whereParam=[];
            if(empty($data))
            {
                continue;
            }
            if(in_array($key,['begindate','real_begindate']))
            {
                $whereParam=[$key,'>',$data];
            }
            if(in_array($key,['enddate','real_enddate']))
            {
                $whereParam=[$key,'<',$data];
            }
            if(in_array($key,['resources_id','agent_id','loan_operator_id','return_operator_id']))
            {
                $whereParam=[$key,'=',intval($data)];
            }
            if(in_array($key,['code','lender','agent_name']))
            {
                $whereParam=[$key,'=',e($data)];
            }
            if(!empty($whereParam))
            {
                $where[]=$whereParam;
            }
        }

        $ResourcesBorrowingModel=new ResourcesBorrowing();
        if(empty($where))
        {
            $model=$ResourcesBorrowingModel;
        }

        foreach($where as $param)
        {
            $model=$ResourcesBorrowingModel->where($param[0],$param[1],$param[2]);
        }
        $pagination= $model->orderBy('id','desc')->paginate(20);
        $paginationArray=$pagination->toArray();
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }
    public function postBorrowApplyList(){

    }

    /**
     * 审核申请 列表
     * @api GET /msc/admin/resources-manager/examine-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-21
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getExamineList(Request $request){
        $resourcesRepository = App::make('Modules\Msc\Repositories\ResourcesRepository');
        $where=[
            ['status','=',1],
            ['apply_validated','=',0]
        ];
        $pid=e($request->get('pid'));
        if(!is_null($pid))
        {
            if($pid==1)
            {
                $where[]=['pid','>',0];
            }
            else
            {
                $where[]=['pid','=','0'];
            }
        }
        $borrowBuilder=$resourcesRepository->getResourcesBorrowBuilderByWhere($where);
        $pagination=$borrowBuilder->orderBy('begindate','asc')->paginate(20);
        return view('msc::admin.returnmanage.applyList',['pagination'=>$pagination]);
    }

    /**
     * 变更设备外借申请状态（审核外借和续借）
     * @api POST /msc/admin/resources-manager/examine-borrow-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        预约ID(必须的)
     * * string        apply_validated        变更值(必须的 通过为1  不通过 为-1)
     * * string        detail        变更说明(拒绝必须的)
     * * string        time_start        变更说明(通过必须)
     * * string        time_end        变更说明(通过必须)
     * * string        idcard_type   证件类型(通过必须)
     *
     * @return json {'id':预约ID,'validated':变更后的结果}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-18 17:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postExamineBorrowingApply(Request $request){

        $this->validate($request, [
            'id'            => 	'required|integer',
            'apply_validated'     => 	'required|integer',
            'detail'        => 	'sometimes',
            'time_start'    => 	'sometimes',
            'time_end'      => 	'sometimes',
            'idcard_type'   => 	'sometimes',
        ]);
        $id=(int)$request->get('id');
        $validated=(int)$request->get('apply_validated');
        $time_start=e($request->get('time_start'));
        $time_end=e($request->get('time_end'));
        if($validated==1)//通过审核 时   取件时间必须
        {
            if(is_null($time_start)&&is_null($time_end))
            {
                return redirect()->back()->withErrors(new \Exception('时间必须'));
            }
        }

        $ResourcesRepository=App::make('Modules\Msc\Repositories\ResourcesRepository');

        $data=[
            'begindate'=>$time_start,
            'enddate'=>$time_end,
            'validated'=>$validated
        ];
        try{
            if($ResourcesRepository->changeBorrowApplyStatus($id,$data))
            {
                return response()->json(
                    $this->success_data(['result'=>true,'id'=>$id])
                );
            }
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 根据设备名称关键字获取设备名称列表（名称无重复）
     * @api GET /msc/admin/resources-manager/resources-name-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        keyword        关键字(必须的)
     * * string        flag_rejected  是否报废
     *
     * @return json [{"id":ID,"code":"设备编号","cateid":"类别ID","category":'类别名',"manager_name":"设备负责人","manager_mobile":"设备负责人联系方式","location":"设备地址","name":"设备名称",'detail':‘设备描述’,"is_rejected":是否报废,"reject_detail":"报废说明","reject_date":报废日期,"is_appointment":‘是否接受预约’},…]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResourcesNameList(Request $request){
        $this->validate($request, [
            'keyword' 		=> 	'sometimes',
            'flag_rejected' 		=> 	'sometimes',
        ]);
        $keyword=urldecode(e(Input::get('keyword')));
        $flag_rejected=intval(Input::get('flag_rejected'));

        $resourcesRepository = App::make('\Modules\Msc\Repositories\ResourcesRepository');
        if(empty($flag_rejected))
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword,'name')->orderBy('id','desc')->groupBy('name')->paginate(20);
        }
        else
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword,'name')->where('flag_rejected','=',$flag_rejected)->groupBy('name')->orderBy('id','desc')->paginate(20);
        }
        //$paginationArray=$pagination->toArray();
        $data=[];
        foreach($pagination as $resources)
        {
            $categroy=$resources->categroy;
            $address=$resources->address;
            $data[]=[
                'id'=>$resources->id,
                'code'=>$resources->code,
                'cateid'=>$resources->cateid,
                'category'=>is_null($categroy)? '-':$categroy->name,
                'manager_name'=>$resources->manager_name,
                'manager_mobile'=>$resources->manager_mobile,
                'location'=>is_null($address)? '-':$address->name,
                'name'=>$resources->name,
                'detail'=>$resources->detail,
                'is_rejected'=>$resources->is_rejected,
                'reject_detail'=>$resources->reject_detail,
                'reject_date'=>$resources->reject_date,
                'is_appointment'=>$resources->is_appointment,
            ];
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$data)
        );
    }


    /**
     * 现有外借设备 列表
     * @api GET /msc/admin/resources-manager/borrowed-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getBorrowedList(){
        $pagination=ResourcesBorrowing::where('status','=',0)->paginate(20);
        //$paginationArray=$pagination->toArray();
        return view('msc::admin.returnmanage.borrowedList',['pagination'=>$pagination]);
    }

    /**
     * 外借历史
     * @api GET /msc/admin/resources-manager/borrowed-record
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getBorrowedRecord(Request $request){
        $this->validate($request, [
            'code'          => 	'sometimes',
            'resources_id'  => 	'sometimes|integer',
            'begindate'     => 	'sometimes|date_format:Y-m-d',
            'enddate'       => 	'sometimes|date_format:Y-m-d',
            'real_begindate'=> 	'sometimes|date_format:Y-m-d H:i:s',
            'real_enddate'  => 	'sometimes|date_format:Y-m-d H:i:s',
            'lender'        => 	'sometimes',
            'agent_id' 		=> 	'sometimes|integer',
            'agent_name'    => 	'sometimes',
            'loan_operator_id'  => 	'sometimes|integer',
            'return_operator_id'=> 	'sometimes|integer',
        ]);
        $formData=$request->only(['code','resources_id','begindate','enddate','real_begindate','real_enddate','lender','agent_id','agent_name','loan_operator_id','return_operator_id']);
        $where=[];
        $whereRawArrray=[];
        $whereRawData=[];
        foreach($formData as $key=>$data)
        {
            $whereParam=[];
            if(empty($data))
            {
                continue;
            }
            if(in_array($key,['begindate','real_begindate']))
            {
                $whereRawArrray[]='unix_timestamp('.$key.') > ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['enddate','real_enddate']))
            {
                $whereRawArrray[]='unix_timestamp('.$key.') < ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['resources_id','agent_id','loan_operator_id','return_operator_id']))
            {
                $whereParam=[$key,'=',intval($data)];
            }
            if(in_array($key,['code','lender','agent_name']))
            {
                $whereParam=[$key,'=',urldecode(e($data))];
            }
            if(!empty($whereParam))
            {
                $where[]=$whereParam;
            }
        }

        $ResourcesBorrowingModel=App::make('Modules\Msc\Repositories\ResourcesRepository');
        $model=$ResourcesBorrowingModel->getResourcesBorrowBuilderByWhere($where);
        if(!empty($whereRawArrray))
        {
            $model=$model->whereRaw(implode(' and  ',$whereRawArrray,$whereRawData));
        }
        $pagination= $model->orderBy('id','desc')->paginate(20);
        return view('msc::admin.resources.borrow.list');
    }

    /**
     * 外籍统计
     * @api GET /msc/wechat/resources-manager/statistics
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStatistics(){
        return view('msc::admin.returnmanage.historyList_tu');
    }

    /**
     *
     * @api GET /msc/admin/resources-manager/statistics-data
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        real_begindate        开始时间(必须的)
     * * string        real_enddate        结束时间(必须的)
     * * string        status               归还时设备状态(必须的) 1=正常(已归还) 0=借出未归还 -1=作废(预约已过期) -2=作废(取消预约)  -3=超期未归还 4=已归还但有损坏  5=超期归还',
     * * string        grade                年级
     * * string        professional         专业
     *
     * @return json { "borrowCount" => '数量'，"time" => "月份""status" => '状态'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-26 12:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStatisticsData(Request $request){
        $total=$this->statisticsBorrow($request);
        return response()->json(
            $this->success_rows(1,'获取成功',count($total),count($total),1,$total->toArray())
        );
    }
    /**
     *
     * @api GET /msc/admin/resources-manager/statistics-excl
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        real_begindate        开始时间(必须的)
     * * string        real_enddate        结束时间(必须的)
     * * string        status               归还时设备状态(必须的) 1=正常(已归还) 0=借出未归还 -1=作废(预约已过期) -2=作废(取消预约)  -3=超期未归还 4=已归还但有损坏  5=超期归还',
     * * string        grade                年级
     * * string        professional         专业
     *
     * @return csv 文件
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-26 12:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStatisticsExcl(Request $request){
        $total=$this->statisticsBorrow($request);
        $str=iconv('utf-8','gb2312','月份,数量,状态')."\n";
        if(empty(count($total)))
        {
            $str .=iconv('utf-8','gb2312','无,无,无')."\n";
        }
        else
        {
            foreach($total as $row)
            {
                $count = iconv('utf-8','gb2312',$row['borrowCount']); //中文转码
                $time = iconv('utf-8','gb2312',$row['time']);
                $status = iconv('utf-8','gb2312',$row['status']);
                $str .= $time.",".$count.",".$status."\n"; //用引文逗号分开
            }
        }
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$str); //导出
    }
    private function export_csv($filename,$data){
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }
    private function statisticsBorrow(Request $request){
        $this->validate($request, [
            'code'          => 	'sometimes',
            'resources_id'  => 	'sometimes|integer',
            'begindate'     => 	'sometimes|date_format:Y-m-d',
            'enddate'       => 	'sometimes|date_format:Y-m-d',
            'real_begindate'=> 	'sometimes|date_format:Y-m-d H:i:s',
            'real_enddate'  => 	'sometimes|date_format:Y-m-d H:i:s',
            'status'        => 	'sometimes',
            'grade'        => 	'sometimes',
            'professional'        => 	'sometimes',
        ]);
        $formData=$request->only(['code','resources_id','begindate','enddate','real_begindate','real_enddate','lender','status']);
        $grade=(int)$request->get('grade');
        $professional=(int)$request->get('professional');
        if(!empty($grade))
        {
            $studentWhere[]=['grade','=',$grade];
        }
        if(!empty($professional))
        {
            $studentWhere[]=['professional','=',$professional];
        }
        $where=[];
        $whereRawArrray=[];
        $whereRawData=[];
        if(is_null($formData['status']))
        {
            $formData['status']=4;
        }
        foreach($formData as $key=>$data)
        {
            $whereParam=[];
            if(empty($data))
            {
                continue;
            }
            if(in_array($key,['begindate','real_begindate']))
            {
                $whereRawArrray[]='unix_timestamp(resources_tools_borrowing.'.$key.') > ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['enddate','real_enddate']))
            {
                $whereRawArrray[]='unix_timestamp(resources_tools_borrowing.'.$key.') < ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['lender','status']))
            {
                $whereParam=[$key,'=',intval($data)];
            }
            if(in_array($key,['code','agent_name']))
            {
                $whereParam=[$key,'=',urldecode(e($data))];
            }
            if(!empty($whereParam))
            {
                $where[]=$whereParam;
            }
        }
        $builder=ResourcesBorrowing::LeftJoin('student',function($join){
            $join->on('resources_tools_borrowing.lender','=','student.id');
        });
        foreach($where as $param)
        {
            $builder=$builder->where('resources_tools_borrowing.'.$param[0],$param[1],$param[2]);
        }
        if(!empty($whereRawArrray))
        {
            $builder=$builder->whereRaw(implode(' and  ',$whereRawArrray,$whereRawData));
        }
        if(!empty($studentWhere))
        {
            foreach($studentWhere as $sparam)
            {
                $builder=$builder->where('student.'.$sparam[0],$sparam[1],$sparam[2]);
            }
        }
        $total=$builder->groupBy('time')->select(DB::raw('count(*) as borrowCount,date_format(resources_tools_borrowing.real_enddate,"%Y-%m") as time,resources_tools_borrowing.status as status'))->get();
        return $total;
    }
    /**
     * 历史外借设备
     * @api GET /msc/admin/resources-manager/record-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-29 19:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRecordList(){
        return view('msc::admin.returnmanage.borrowedList');
    }

    /**
     * 外借历史记录
     * @api GET /msc/admin/resources-manager/borrow-record-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getBorrowRecordList(Request $request){
        $this->validate($request, [
            'is_gettime'=>'sometimes|integer|min:|max:2',
            'keyword'=>'sometimes',
            'real_begindate'=> 	'sometimes|date_format:Y-m-d H:i:s',
            'real_enddate'  => 	'sometimes|date_format:Y-m-d H:i:s',
            'status'=> 	'sometimes|integer',
        ]);

        $formData=$request->only(['is_gettime','keyword','real_begindate','real_enddate','status']);
        $where=[];
        $whereRawArrray=[];
        $whereRawData=[];
        foreach($formData as $key=>$data)
        {
            $whereParam=[];
            if(empty($data))
            {
                continue;
            }
            if(in_array($key,['begindate','real_begindate']))
            {
                $whereRawArrray[]='unix_timestamp('.$key.') > ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['enddate','real_enddate']))
            {
                $whereRawArrray[]='unix_timestamp('.$key.') < ? ';
                $whereRawData[]=$data;
            }
            if(in_array($key,['resources_id','agent_id','loan_operator_id','return_operator_id','status']))
            {
                $whereParam=[$key,'=',intval($data)];
            }
            if(in_array($key,['code','lender','agent_name']))
            {
                $whereParam=[$key,'=',urldecode(e($data))];
            }
            if(!empty($whereParam))
            {
                $where[]=$whereParam;
            }
        }

        $ResourcesBorrowingModel=App::make('Modules\Msc\Repositories\ResourcesRepository');
        $model=$ResourcesBorrowingModel->getResourcesBorrowBuilderByWhere($where);

        if($formData['is_gettime']==1)
        {
            $model=$model->whereIn('status',[-3,-5]);
        }
        if($formData['is_gettime']==2)
        {
            $model=$model->whereIn('status',[1]);
        }
        if(!empty($whereRawArrray))
        {
            $model=$model->whereRaw(implode(' and  ',$whereRawArrray,$whereRawData));
        }
        $pagination= $model->orderBy('id','desc')->paginate(20);
        return view('msc::admin.returnmanage.historyList',['pagination'=>$pagination]);
    }

    /**
     * 历史记录详情
     * @api GET /msc/admin/resources-manager/record-info
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRecordInfo(Request $request){
        $this->validate($request, [
            'id'            => 	'required|integer'
        ]);
        $id=e($request->get('id'));
        $info=ResourcesBorrowing::find($id);
        $resourcesToolItem=$info->resourcesToolItem;
        $resourcesTool=$resourcesToolItem->resourcesTools;
        $resources=$resourcesTool->resources;
        $images=$resources->images;

        return view('msc::admin.returnmanage.historyList_detail',['images'=>$images,'info'=>$info,'resourcesTool'=>$resourcesTool,'resourcesToolItem'=>$resourcesToolItem]);
    }

    /**
     * 提醒归还
     * @api GET /msc/admin/resources-manager/tip-back
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        外借订单ID(必须的)
     * * string        detail    消息内容(为空时，系统发送默认消息)
     *
     * @return json {result:true，id:被提醒外借申请单id}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-25 11:51
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getTipBack(Request $request){
        $this->validate($request, [
            'id'            => 	'required|integer',
            'detail'        => 	'sometimes',
        ]);
        $id=$request->get('id');
        $detail=$request->get('detail');

        $apply=ResourcesBorrowing::find($id);
        try{
            if(is_null($apply))
            {
                throw new \Exception('没有找到该外借申请');
            }
            if($apply->status<=0)
            {
                $user=$apply->lenderInfo;
                $toolsItem=$apply->resourcesToolItem;
                if(is_null($toolsItem))
                {
                    throw new \Exception('设备不存在，请检查数据');
                }
                $tools=$toolsItem->resourcesTools;

                if(is_null($user))
                {
                    throw new \Exception('借阅人不存在，请检查数据');
                }
                $openid=$user->openid;
                if(empty($detail))
                {
                    $msgContent='请及时归还设备'.$tools->name.':'.$toolsItem->code;
                }
                else
                {
                    $msgContent=$detail;
                }
                $msg=Common::CreateWeiXinMessage([
                    ['title'=>$msgContent]
                ]);
                //Common::sendWeiXin($openid,$msg);
                return response()->json(
                    $this->success_data(['result'=>true,'id'=>$id])
                );
            }
            else
            {
                throw new \Exception('设备不在外借状态,不能提醒');
            }
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 获取教室列表
     * @api GET /msc/admin/resources-manager/classroom-list
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        keyword          教室名称
     * * string        page             页码(
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getClassroomList(Request $request){
        $keyword=urldecode($request->get('keyword'));
        $pagination=ResourcesClassroom::where('name','like','%'.$keyword.'%')->paginate(config('msc.page_size'));
        $data=[];
        foreach($pagination as $item)
        {
            $item=[
                'id'=>$item->id,
                'name'=>$item->name,
            ];
            $data[]=$item;
        }
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->lastPage(),20,$pagination->currentPage(),$data)
        );
    }
}