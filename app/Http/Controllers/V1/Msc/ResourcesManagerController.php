<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 18:33
 */

namespace App\Http\Controllers\V1\Msc;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesBorrowing;
use Modules\Msc\Entities\ResourcesCate;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Entities\ResourcesLocation;
use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\V1\ApiBaseController;
use App\Repositories\ResourcesRepository;



class ResourcesManagerController extends ApiBaseController
{
    /**
     * 根据类别筛选资源
     * @api GET /api/1.0/private/admin/resource/resources-list
     * @access publicr
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        cate_id        类别ID(必须的)
     * * string        order_name      排序字段名(必须的)
     * * string        order_type      排序方式(必须的) 1 ：Desc 0:asc
     *
     * @return json [{"id":ID,"code":"设备编号","cateid":"类别ID","category":'类别名',"manager_name":"设备负责人","manager_mobile":"设备负责人联系方式","location":"设备地址","name":"设备名称",'detail':‘设备描述’,"is_rejected":是否报废,"reject_detail":"报废说明","reject_date":报废日期,"is_appointment":‘是否接受预约’},…]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 21:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResourcesList(Request $request)
    {
        $this->validate($request, [
            'cate_id' 			=> 	'sometimes|max:16|min:1',
            'order_name' 		=> 	'sometimes',
            'order_type'		=> 	'sometimes|integer|min:0|max:1',
        ]);
        $cateId=(int)Input::get('cate_id');
        $orderName=e(Input::get('order_name'));
        $orderType=(int)Input::get('order_type');
        $where = [];
        if(!empty($cateId))
        {
            $where = [
                ['cateid','=',$cateId]
            ];
        }
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
            $order = ['id', 'desc'];
        }
        $resourcesRepository = App::make('App\Repositories\ResourcesRepository');
        $pagination = $resourcesRepository->getResourcesByParam($where, 10, $order);
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
            $this->success_rows(1,'获取成功',$pagination->total(),10,$pagination->currentPage(),$data)
        );
    }

    /**
     * 根据关键字查询资源
     * @api GET /api/1.0/private/admin/resource/resources-list-by-keyword
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        keywords        关键字(必须的)
     * * int            flag_rejected   是否报废 2=已报废 1=正常
     * * string        page            页码(必须的)
     *
     * @return json [{"id":ID,"code":"设备编号","cateid":"类别ID","category":'类别名',"manager_name":"设备负责人","manager_mobile":"设备负责人联系方式","location":"设备地址","name":"设备名称",'detail':‘设备描述’,"is_rejected":是否报废,"reject_detail":"报废说明","reject_date":报废日期,"is_appointment":‘是否接受预约’},…]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date  2015-11-11 21:10
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResourcesListByKeyword(Request $request){
        $this->validate($request, [
            'keyword' 		=> 	'sometimes',
            'flag_rejected' 		=> 	'sometimes',
        ]);
        $keyword=e(Input::get('keyword'));
        $flag_rejected=intval(Input::get('flag_rejected'));
        $resourcesRepository = App::make('App\Repositories\ResourcesRepository');
        if(!empty($flag_rejected))
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword)->orderBy('id','desc')->paginate(20);
        }
        else
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword)->where('flag_rejected','=',$flag_rejected)->orderBy('id','desc')->paginate(20);
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
     * 获取资源信息
     * @api GET /api/1.0/private/admin/resource/resources
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        资源ID(必须的)
     *
     * @return json  "data":{"id":ID,"code":"设备编号","cateid":"类别ID","category":'类别名',"images":[{"id":1,"Resources_id":1,"url":"\/www.baidu.com.jpg","order":图片排序,"descrption":"图片秒速"},……],"manager_name":"设备负责人","manager_mobile":"设备负责人联系方式","location":"设备地址","name":"设备名称",'detail':‘设备描述’,"is_rejected":是否报废,"reject_detail":"报废说明","reject_date":报废日期,"is_appointment":‘是否接受预约’}}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-12 15：23
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResources(Request $request){
        $this->validate($request, [
            'id' 		=> 	'required',
        ]);
        $id=(int)Input::get('id');
        if(empty($id))
        {
            return response()->json(
                $this->fail(new \Exception('资源不存在'))
            );
        }

        $resources= Resources::find($id);

        $categroy=$resources->categroy;
        $address=$resources->address;
        $images=$resources->images;
        if($images)
        {
            $imagesArray=$images->toArray();
        }
        else
        {
            $imagesArray=[];
        }
        $dataReturn=[
            'id'=>$resources->id,
            'code'=>$resources->code,
            'cate_id'=>$resources->cateid,
            'category'=>is_null($categroy)? '-':$categroy->name,
            'images'=>$imagesArray,
            'manager_name'=>$resources->manager_name,
            'manager_mobile'=>$resources->manager_mobile,
            'location'=>is_null($address)? '-':$address->name,
            'location_id'=>is_null($address)? '-':$address->id,
            'name'=>$resources->name,
            'detail'=>$resources->detail,
            'is_rejected'=>$resources->is_rejected,
            'reject_detail'=>$resources->reject_detail,
            'reject_date'=>$resources->reject_date,
            'is_appointment'=>$resources->is_appointment,
        ];
        return response()->json(
            $this->success_data($dataReturn,1,'获取成功')
        );
    }


    /**
     * 编辑资源
     * @api POST /api/1.0/private/admin/resource/edit-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        资源ID(必须的)
     * * string        name        资源名称(必须的)
     * * string        cate_id        资源类别ID(必须的)
     * * string        code        资源编号(必须的)
     * * string        manager_name        资源负责人姓名(必须的)
     * * string        manager_mobile        资源负责人电话(必须的)
     * * string        location_id        资源地址ID(必须的)
     * * string        detail        资源表述(必须的)
     *
     * * Array        images_path        参数中文名(必须的) e.g:<input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447430.png">
     * * Array        images        参数中文名(必须的)e.g:<input type="file" name="images[]" >
     *
     * @return json {name:设备名称,cate_id:类别ID,code:设备编号,manager_name:管理员姓名,manager_mobile:管理员电话,location_id:地址ID,detail:设备说明,images:[{'id':设备图片ID,url:'图片访问路径'}]}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditResources(Request $request){
        $formData=$request->only(['id','images_path']);
        $this->validate($request,[
            'id'=>'required|integer',
            'name'=>'required',
            'cate_id'=>'required|integer',
            'code'=>'required',
            'manager_name'=>'required',
            'manager_mobile'=>'required|mobile_phone',
            'location_id'=>'required|integer'
        ]);
        $id=(int)$formData['id'];
        $resourcesRepository = App::make('App\Repositories\ResourcesRepository');

        $connection=DB::connection('msc_mis');
        //删除不要图片
        try{
            $resourcesRepository->ResourcesImageDel($id,$formData['images_path']);
        }
        catch(\Exception $ex)
        {
            //图片已经不存在
        }
        //新增图片
        //$pathList=Common::saveImags($request,'images');
        $hasList = ResourcesImage::where('Resources_id','=',$id)->get();
        if(!empty($hasList))
        {
            $hasData=[];
            foreach ($hasList as $item) {
                $hasData[]=$item->url;
            }
            $imagePathCopy=$formData['images_path'];
            foreach($formData['images_path'] as $key=>$path)
            {
                if(in_array($path,$hasData))
                {
                    unset($imagePathCopy[$key]);
                }
            }
            $ImageNew=$imagePathCopy;
            unset($imagePathCopy);
        }
        else
        {
            $ImageNew=$formData['images_path'];
        }
        foreach($ImageNew as $item)
        {
            $data=[
                'resources_id'=>$id,
                'url'=>$item,
                'order'=>0,
                'descrption'=>''
            ];
            $result=ResourcesImage::create($data);
            if(!$result)
            {
                throw new \Exception('资源图片保存失败');
            }
        }
        $resources=Resources::find($id);
        $resourcesImageList=ResourcesImage::where('Resources_id','=',$id)->get();
        if(!empty($resources))
        {
            $resourcesData=$request->only(['name','cateid','code','manager_name','manager_mobile','location_id','detail']);
            if($resources->update($resourcesData))
            {
                $dataReturn=[
                    'name'=>$resources->id,
                    'cate_id'=>$resources->cateid,
                    'code'=>$resources->code,
                    'manager_name'=>$resources->manager_name,
                    'manager_mobile'=>$resources->manager_mobile,
                    'location_id'=>$resources->location_id,
                    'detail'=>$resources->detail,
                    'images'=>empty($resourcesImageList)? []:$resourcesImageList->toArray(),
                ];
                $connection->commit();
                return response()->json(
                    $this->success_data($dataReturn,1,'保存成功')
                );
            }
            else
            {
                $connection->rollback();
                return response()->json($this->fail(new \Exception('保存失败')));
            }
        }
    }

    /**
     * 新增资源
     * @api POST /api/1.0/private/admin/resource/add-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name        设备名称(必须的)
     * * int        cate_id        设备疯了(必须的)
     * * string        code        设备编码(必须的)
     * * string        manager_name        设备负责人(必须的)
     * * string        manager_mobile        设备负责人电话(必须的)
     * * int        location_id        地址id(必须的)
     * * string        detail        设备功能(必须的)
     * * Array        images_path        参数中文名(必须的) e.g:<input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447430.png">
     *
     * @return json {"id":ID,"code":"设备编号","cate_id":"类别ID","images":['路径1','路径二'，……],"manager_name":"设备负责人","manager_mobile":"设备负责人联系方式","location_id":"设备地址ID","name":"设备名称",'detail':‘设备描述’,"is_rejected":是否报废,"reject_detail":"报废说明","reject_date":报废日期,"is_appointment":‘是否接受预约’}}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-12 18:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddResources(Request $request){
        $formData=$request->only(['name','cate_id','code','manager_name','manager_mobile','location_id','detail']);
        $this->validate($request,[
            'name'=>'required',
            'cate_id'=>'required|integer',
            'code'=>'required',
            'manager_name'=>'required',
            'manager_mobile'=>'required|mobile_phone',
            'location_id'=>'required|integer'
        ]);
        try
        {
            $connection=DB::connection('msc_mis');
            $connection->beginTransaction();
            $resources=Resources::create($formData);
            if(!$resources)
            {
               throw new \Exception('新增资源失败');
            }
            //$pathList=Common::saveImags($request,'images');
            $pathList=$request->get('images_path');
            foreach($pathList as $item)
            {
                $data=[
                    'resources_id'=>$resources->id,
                    'url'=>e($item),
                    'order'=>0,
                    'descrption'=>''
                ];
                $result=ResourcesImage::create($data);
                if(!$result)
                {
                    throw new \Exception('资源图片保存失败');
                }
            }
            $connection->commit();
            $dataReturn=[
                'id'=>$resources->id,
                'code'=>$resources->code,
                'cate_id'=>$resources->cate_id,
                'images'=>$pathList,
                'manager_name'=>$resources->manager_name,
                'manager_mobile'=>$resources->manager_mobile,
                'location_id'=>$resources->location_id,
                'name'=>$resources->name,
                'detail'=>$resources->detail,
                'is_rejected'=>$resources->is_rejected,
                'reject_detail'=>$resources->reject_detail,
                'reject_date'=>$resources->reject_date,
                'is_appointment'=>$resources->is_appointment,
            ];
            return response()->json(
                $this->success_data($dataReturn,1,'新增成功')
            );
        }
        catch(\Exception $ex)
        {
            $connection->rollback();
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 报废设备
     * @api POST /api/1.0/private/admin/resource/rejected-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        报废设备ID(必须的)
     * * string        reject_detail        报废描述(必须的)
     *
     * @return json {'id'：设备ID,'name':设备名称,code:设备编码,is_rejected:是否报废,reject_detail:报废说明,reject_date:报废日期}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-13 16:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRejectedResources(Request $request){
        $formData=$request->only(['reject_detail','id']);
        $this->validate($request,[
            'id'=>'required|integer',
            'reject_detail'=>'required',
        ]);
        $resources=Resources::find($formData['id']);
        if(!empty($resources))
        {
            $resources->is_rejected=1;
            $resources->is_appointment=0;
            $resources->reject_detail=$formData['reject_detail'];
            $resources->reject_date=date('Y-m-d H:i:s');
            $resources->is_appointment=0;
            $result=$resources->save();
            $dataReturn=[
                'id'=>$resources->id,
                'name'=>$resources->name,
                'code'=>$resources->code,
                'is_rejected'=>$resources->is_rejected,
                'reject_detail'=>$resources->reject_detail,
                'reject_date'=>$resources->reject_date,
            ];
        }
        if($result)
        {
            return response()->json(
                $this->success_data($dataReturn,1,'报废成功')
            );
        }
        else
        {
            return response()->json($this->fail(new \Exception('报废失败')));
        }
    }

    /**
     * 删除资源(物理删除，不是报废)
     * @api GET /api/1.0/private/admin/resource/resources-del
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
    public function getResourcesDel(Request $request){
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
     * 地址新增
     * @api POST /api/1.0/private/admin/resource/add-address
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
     * 根据关键字获取地址(有翻页)
     * @api GET /api/1.0/private/admin/resource/resources-location-list
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
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }
    /**
     *  获取类别ID 列表
     * @api GET /api/1.0/private/admin/resource/categroy-list
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
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }
    /**
     *根据条件 获取 外借记录
     * @api GET /api/1.0/private/admin/resource/borrow-list
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
     * 申请外借（续借）
     * @api POST /api/1.0/private/admin/resource/add-borrow-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        code           设备编号(必须的)
     * * int        resources_id     设备资源ID(必须的)
     * * string        begindate     申请约定借出时间(必须的)
     * * string        enddate       申请(约定归还时间(必须的)
     * * string        lender        申请人ID(必须的)
     * * int        agent_id         申请人代理人id
     * * string        agent_name    申请人代理人姓名
     * * string        detail        申请描述
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-18
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddBorrowApply(Request $request){
        $this->validate($request, [
            'code'          => 	'required',
            'resources_id'  => 	'required|integer',
            'begindate'     => 	'required|date_format:Y-m-d H:i:s',
            'enddate'       => 	'required|date_format:Y-m-d H:i:s|after:begindate',
            'lender' 		=> 	'required|integer',
            'agent_id'      => 	'sometimes|integer',
            'agent_name'    => 	'sometimes',
            'detail'        => 	'sometimes',
        ]);
        $formdata=$request->only(['code','resources_id','begindate','enddate','lender','agent_id','agent_name','detail']);
        $data=[
            'code'=>e($formdata['code']),
            'resources_id'=>intval($formdata['resources_id']),
            'begindate'=>e($formdata['begindate']),
            'enddate'=>e($formdata['enddate']),
            'lender'=>intval($formdata['lender']),
            'agent_id'=>intval($formdata['agent_id']),
            'agent_name'=>e($formdata['agent_name']),
            'detail'=>e($formdata['detail'])? e($formdata['detail']):'',
        ];
        $result=ResourcesBorrowing::create($data);
        if($result)
        {
            $returnData=[
                'id'=>$result->id
            ];
            return response()->json(
                $this->success_data($returnData,1,'申请成功')
            );
        }
        else
        {
            return response()->json($this->fail(new \Exception('申请失败')));
        }
    }

    /**
     * 变更设备外借申请状态（审核外借和续借）
     * @api POST /api/1.0/private/admin/resource/examine-borrow-apply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        预约ID(必须的)
     * * string        validated        变更值(必须的)
     * * string        detail        变更说明(必须的)
     * * string        time        变更说明(必须的)
     * * string        idcard_type   证件类型(必须的)
     *
     * @return json {'id':预约ID,'validated':变更后的结果}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-18 17:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postExamineBorrowApply(Request $request){
        $this->validate($request, [
            'id'            => 	'required|integer',
            'validated'     => 	'required|integer',
            'detail'        => 	'sometimes',
            'time_start'    => 	'required',
            'time_end'      => 	'required',
            'idcard_type'   => 	'sometimes',
        ]);

        $id=(int)$request->get('id');
        $validated=(int)$request->get('validated');
        $time_start=(int)$request->get('time_start');
        $time_end=(int)$request->get('time_end');
        $apply=ResourcesBorrowing::find($id);
        try{
            if(!is_null($apply))
            {
                if($apply->status==-2)
                {
                    throw new \Exception('该申请已经作废');
                }
                if($apply->begindate>$time_start)
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                if($apply->enddate<$time_start)
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                $apply->validated=$validated;
            }
            else
            {
                throw new \Exception('没有找到该申请');
            }
            $result=$apply->save();
            if($result)
            {
                $returnData=[
                    'id'=>$result->id,
                    'validated'=>$result->validated
                ];
                $msg=Common::CreateWeiXinMessage([
                    ['title'=>'测试文本1'],
                    ['title'=>'测试文本','picUrl'=>'http://image.golaravel.com/5/c9/44e1c4e50d55159c65da6a41bc07e.jpg']
                ]);
                //Common::sendWeiXin(123,$msg);
                return response()->json(
                    $this->success_data($returnData,1,'处理成功')
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
     * @api GET /api/1.0/private/admin/resource/resources-name-list
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
        $keyword=e(Input::get('keyword'));
        $flag_rejected=intval(Input::get('flag_rejected'));
        $resourcesRepository = App::make('App\Repositories\ResourcesRepository');
        if(!empty($flag_rejected))
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword)->orderBy('id','desc')->groupBy('name')->paginate(20);
        }
        else
        {
            $pagination=$resourcesRepository->getResourcesListByKeuword($keyword)->where('flag_rejected','=',$flag_rejected)->groupBy('name')->orderBy('id','desc')->paginate(20);
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
     * 学生借出设备钱扫描设备
     * @api post /api/1.0/private/admin/resource/find-resource
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        code        设备编码(必须的)
     * * int            uid        学生ID(必须的)
     *
     * @return JSON {'name'：设备名称,'code':设备编码,'start':借出时间开始,'end'：借出时间结束}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-19 15:27
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postFindResource(Request $request){
        $this->validate($request, [
            'code' 		=> 	'required',
            'uid' 		=> 	'required|integer',
        ]);
        $uid=(int)$request->get('uid');
        $code=e($request->get('code'));
//        DB::connection('msc_mis')->enableQueryLog();
        $applyList=ResourcesBorrowing::where('lender','=',$uid)
            ->where('status','=',1)
            ->where('validated','=',1)
            ->whereRaw('unix_timestamp(begindate)< ? and unix_timestamp(enddate) > ?',[time(),time()])
            ->get();
//        $queries = DB::connection('msc_mis')->getQueryLog();
        $getedResoucesList=Resources::where('code','=',$code)->get();
        $getedResouces='';
        if(!empty($getedResoucesList))
        {
            $getedResouces=$getedResoucesList->first();
        }
        try{
            if(empty($getedResouces))
            {
                throw new \Exception('非法设备');
            }
            $has=false;
            foreach($applyList as $apply)
            {
                $resource=$apply->resources;
                if(!is_null($resource))
                {

                    if($getedResouces->name==$resource->name)
                    {
                        $has=true;
                        break;
                    }
                }
            }
            if($has==false)
            {
                throw new \Exception('没有找到相关预约');
            }
            //同类设备编号在出库时的矫正
            $apply->code=$getedResouces->code;
            $result=$apply->save();
            if($result)
            {
                $returnData=[
                    'name'=>$resource->name,
                    'code'=>$resource->code,
                    'start'=>$apply->begindate,
                    'end'=>$apply->enddate
                ];
                return response()->json(
                    $this->success_data($returnData,1,'处理成功')
                );
            }
            else
            {
                throw new \Exception('选定设备失败，请重新扫描');
            }
        }catch (\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }
    public function changeBorrowApplyStatus($id,$data){
        $apply=ResourcesBorrowing::find($id);
        $time_start=$data['begindate'];
        $time_end=$data['enddate'];
        $validated=$data['validated'];
        try{
            if(!is_null($apply))
            {
                if($apply->status==-2)
                {
                    throw new \Exception('该申请已经作废');
                }
                if($apply->begindate>$time_start)
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                if($apply->enddate<$time_end)
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                $apply->validated=$validated;
            }
            else
            {
                throw new \Exception('没有找到该申请');
            }
            $result=$apply->save();
            if($result)
            {
                $msg=Common::CreateWeiXinMessage([
                    ['title'=>'测试文本1'],
                    ['title'=>'测试文本','picUrl'=>'http://image.golaravel.com/5/c9/44e1c4e50d55159c65da6a41bc07e.jpg']
                ]);
                //Common::sendWeiXin(123,$msg);
                return true;
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
}