<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\ResourcesTools;
use Modules\Msc\Entities\ResourcesToolsItems;
use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesImage;
use DB;
class ResourceController extends MscWeChatController {

	//新增资源
	public function getResourceAdd()
	{
		return view('msc::wechat.resource.resource_add');
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
	public function postResourcesAddOp(Request $request){
		$request['cate_id']		= 1;
		$request['location_id'] = 1;
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
			return redirect()->intended('/msc/wechat/resource/resource-list');
		}
		catch(\Exception $ex)
		{
			$connection->rollback();
			return response()->json($this->fail($ex));
		}
	}

	//编辑资源
	public function getResourceEdit()
	{

		return view('msc::wechat.resource.add');

	}
	//资源管理
	public function getResourceManage()
	{

		return view('msc::wechat.resource.resource_manage');

	}

	/**
	 * 资源列表
	 * @api GET /msc/wechat/resources/resource-list
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
	 * @date 2015-11-24 14:48
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getResourceList()
	{
		$data=[
			'TOOLS'=>'可借设备',
			'CLASSROOM'=>'教室'
		];
		return view('msc::wechat.resource.resource_list',['options'=>$data]);
	}

	/**
	 * 根据条件获取资源列表（有翻页）
	 * @api GET /msc/wechat/resource/resource-paginate
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * string        type        类型(必须的)
	 * * string        keyword     资源名称关键字(可选)
	 *
	 * @return
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-24 15:44
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getResourcePaginate(Request $request){
		$this->validate($request, [
				'type'			=> 'required',
				'keyword' 		=> 	'sometimes',
		],[
			'type.required'=>'资源类型必须'
		]);
		$type=e($request->get('type'));
		$keyword=urldecode(e($request->get('keyword')));
		try{
			if($type=='TOOLS')
			{
				$builder=new ResourcesTools();
			}
			if($type=='CLASSROOM')
			{
				$builder=new ResourcesClassroom();
			}
			if(!is_null($keyword))
			{
				$builder=$builder->where('name','like','%'.$keyword.'%');
			}
			$pagination	=$builder->orderBy('id','desc')->paginate(20);

			$list=[];
			foreach($pagination as $item)
			{

				$resources=$item->resources;
				$imagesList=is_null($resources)? []:$resources->images;
				$image=count($imagesList)? $imagesList->first():'';
				$path=empty($image)? '':$image->url;
				$data=[
					'image'=>$path,
					'name'=>$item->name,
					'id'=>$item->id,
				];
				$list[]=$data;
			}
			return response()->json(
					$this->success_rows(1,'获取成功',$pagination->lastPage(),20,$pagination->currentPage(),$list)
			);
		}
		catch(\Exception $ex)
		{
			return response()->json($this->fail($ex));
		}
	}

	/**
	 * 资源详情  微信端
	 * @api GET /msc/wechat/resource/resource
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
	 * @date 2015-11-24 18:04
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getResource(Request $request){
		$this->validate($request, [
				'type'			=> 'required',
				'id'			=> 'required',
		]);
		$type=e($request->get('type'));
		$id=(int)$request->get('id');
		$data=[
			'type'=>$type,
			'id'=>$id,
		];
		try{
			return $this->getResourceInfo($data);
		}catch (\Exception $ex)
		{
			return redirect()->back()->withErrors($ex);
		}

	}
	private function getResourceInfo($data){
		$type=$data['type'];
		$id=$data['id'];
		if($type=='TOOLS')
		{
			$builder=new ResourcesTools();
		}
		if($type=='CLASSROOM')
		{
			$builder=new ResourcesClassroom();
		}
		$resourcesInfo=$builder->where('id','=',$id)->get();
		if($resourcesInfo)
		{
			$info=$resourcesInfo->first();
			$resources=$info->resources;
			$images=$resources->images;
			return view('msc::wechat.resource.resource_list_detail',['info'=>$info,'resources'=>$resources,'images'=>$images,]);
		}
		else
		{
			throw new \Exception('没有找到该资源');
		}
	}

	/**
	 * 微信扫一扫查看 资源 信息
	 * @api GET /msc/wechat/resource/resource-view
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
	public function getResourceView(Request $request){
		$this->validate($request, [
				'id'			=> 'required',
		]);
		$id=intval($request->get('id'));
		$resource=Resources::find($id);
		$data=[
				'type'=>$resource->type,
				'id'=>$resource->id,
		];
		return $this->getResourceInfo($data);
	}
}