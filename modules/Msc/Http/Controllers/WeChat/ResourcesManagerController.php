<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 18:33
 */

namespace Modules\Msc\Http\Controllers\WeChat;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesBorrowing;
use Modules\Msc\Entities\ResourcesCate;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Entities\ResourcesLocation;
use Modules\Msc\Entities\ResourcesToolsCate;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Http\Controllers\MscWeChatController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use App\Entities\User;
use Modules\Msc\Entities\ResourcesToolsItems;
use Modules\Msc\Entities\ResourcesTools;
use DB;

class ResourcesManagerController extends MscWeChatController
{


/*    public function getTest(){
        return view('msc::wechat.borrowreturn.borrow_student_manage');
    }*/

    //学生外借管理
    public function getBorrowStudentManage(){
        $data=[
            'js'=> $this->GenerationJsSdk()
        ];

        return view('msc::wechat.resourceborrow.borrow_manage',$data);
	}

    //测试地址 /msc/wechat/resources-manager/test
    public function getTest(){
    	return view('msc::wechat.personalcenter.mycourse');
		//return view('msc::wechat.opendevice.mycourse');
	}


    //老师外借管理
    public function getBorrowTeacherManage(){
         $data=[
            'js'=> $this->GenerationJsSdk()
        ];
        return view('msc::wechat.resource.borrow_manage',$data);

    }
    //我的续借 /msc/wechat/resources-manager/appointment
    public function getAppointment(){
        return view('msc::wechat.personalcenter.personalinfo_myborrow_renew');
    }

    /**
     *  获取类别ID 列表
     * @api GET /msc/wechat/resources-manager/categroy-list
     *
     */
    public function getCategroyList(Request $request){
        $pid=(int)$request->get('pid');
        $pid=$pid? $pid:0;
        $resourcesCate=new ResourcesToolsCate();
        $pagination=$resourcesCate->where('pid','=',$pid)->orderBy('id','desc')->paginate(20);
        $paginationArray=$pagination->toArray();
        return response()->json(
            $this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
        );
    }

    //外借历史
    public function getBorrowHistory(ResourcesBorrowing $ResourcesBorrowing,User $user){

        $page = Input::get('page');

        $BorrowingBuilder = $ResourcesBorrowing->where('real_enddate','<>',' ')->where('real_enddate','<',date('Y-m.d H:i:s'));
        $historyList = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->orderBy('id')->paginate(9);


        foreach($historyList as $k => $v){
            $historyList[$k]['begindate'] = date('m.d',strtotime($historyList[$k]['begindate']));
            $historyList[$k]['enddate'] = date('m.d',strtotime($historyList[$k]['enddate']));
            $historyList[$k]['real_begindate'] = date('m.d',strtotime($historyList[$k]['real_begindate']));
            $historyList[$k]['real_enddate'] = date('m.d',strtotime($historyList[$k]['real_enddate']));
        }

        $data = [
            'historyList'=>$historyList
        ];
        //如果有页码 并且大于1 的时候返回json数据
        if(!empty($page)&&$page>1){
            return response()->json(
                $this->success_rows(1,'获取成功',$historyList->total(),20,$historyList->currentPage(),array('historyList'=>$historyList->toArray()))
            );
        }else{
            return view('msc::wechat.resource.borrow_lishi',$data);
        }
    }

    //获取外借历史数据
    public function getBorrowHistoryData(ResourcesBorrowing $ResourcesBorrowing){

        $BorrowingBuilder = $ResourcesBorrowing->where('real_enddate','!=',' ')->where('real_enddate','<',date('Y-m.d H:i:s'));
        $enddate = Input::get('enddate');
        $begindate = Input::get('begindate');
        if(!empty($enddate) && !empty($begindate)){
            $BorrowingBuilder = $BorrowingBuilder->where('begindate','<=',$enddate)->where('enddate','>=',$enddate)->orWhere('begindate','<=',$begindate)->where('enddate','>=',$begindate);
        }
        $historyList = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->orderBy('id')->paginate(7);

        foreach($historyList as $k => $v){
            $historyList[$k]['begindate'] = date('m.d',strtotime($historyList[$k]['begindate']));
            $historyList[$k]['enddate'] = date('m.d',strtotime($historyList[$k]['enddate']));
            $historyList[$k]['real_begindate'] = date('m.d',strtotime($historyList[$k]['real_begindate']));
            $historyList[$k]['real_enddate'] = date('m.d',strtotime($historyList[$k]['real_enddate']));
        }

        return response()->json(
            $this->success_rows(1,'获取成功',$historyList->total(),20,$historyList->currentPage(),array('historyList'=>$historyList->toArray()))
        );
    }

    //历史详情
    public function getBorrowHistoryDetail(ResourcesBorrowing $ResourcesBorrowing){
        $id = Input::get('id');
        $BorrowingBuilder = $ResourcesBorrowing->where('id','=',$id);
        $historyDetail = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->get()->first();
        $data = [
            'historyDetail'=>$historyDetail
        ];
        if(!empty($historyDetail)){
            $historyDetail['begindate'] = date('m.d',strtotime($historyDetail['begindate']));
            $historyDetail['enddate'] = date('m.d',strtotime($historyDetail['enddate']));
            $historyDetail['real_begindate'] = date('m.d',strtotime($historyDetail['real_begindate']));
            $historyDetail['real_enddate'] = date('m.d',strtotime($historyDetail['real_enddate']));
        }

        return view('msc::wechat.resource.borrow_lishi_detail',$data);

    }
    //归还提醒
    public function getBorrowNowAttention(){
        return view('msc::wechat.resource.borrow_now_attention');
    }
    /**
     * 学生申请外借表单
     * @api GET /msc/wechat/resources-manager/add-borrow-apply
     *
     */
    public function getAddBorrowApply(){

        return view('msc::wechat.resourceborrow.borrow_apply');

    }

    /**
     * 申请外借（续借）处理
     *
     */
    public function postAddBorrowApply(Request $request){

        $this->validate($request, [
            'resources_tool_id'=>'required|integer',
            'begindate'     => 	'required',
            'enddate'       => 	'required',
            'detail'        => 	'required',
        ]);

        DB::beginTransaction();
        $lender = Auth::user();
        $request['code'] = rand(1,9).rand(1,9).rand(1,9).rand(1,9);
        $formData=$request->only(['resources_tool_id','begindate','enddate','lender','detail','pid']);
        $result = DB::connection('msc_mis')->table('resources_tools_borrowing')->insert([
            'code'=>$request['code'] ,
            'resources_tool_id'=>intval($formData['resources_tool_id']),
            'begindate'=>e($formData['begindate']),
            'enddate'=>e($formData['enddate']),
            'lender'=>empty($lender)?1:$lender->id,
            'detail'=>e($formData['detail'])? e($formData['detail']):'',
            'pid'=>empty($request['pid'])?0:$request['pid'],
            'created_at'=>date('Y-m.d,H:i:s'),
            'updated_at'=>date('Y-m.d,H:i:s')
        ]);

        if($result)
        {
            $openid = \Illuminate\Support\Facades\Session::get('openid','');
            $this->sendMsg('工具编号为：'.$formData['resources_tool_id'].',申请成功！审核中，请耐心等待。',$openid);
            return view('msc::wechat.index.index_success');
            //$this->sendMsg('工具编号为：'.$formData['resources_tool_id'].',申请成功！审核中，请耐心等待。');
             //return redirect()->action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@getWaitExamineBorrowApply');
        }
        else
        {
            DB::rollback();
            return view('msc::wechat.index.index_error',array('error_msg'=>'操作失败'));
        }

    }

    /**
     * 等待审核列表
     * @api GET /msc/wechat/resources-manager/wait-examine-borrow-apply
     * @access public
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getWaitExamineBorrowApply(){
/*        $user=Auth::user();
        $where=[
            ['lender','=',$user->id],
            ['status','=',1],
            ['validated','=',0],
        ];
        $ResourcesBorrowingModel=App::make('Modules\Msc\Repositories\ResourcesRepository');
        $model=$ResourcesBorrowingModel->getResourcesBorrowBuilderByWhere($where);
        $pagination= $model->orderBy('id','desc')->paginate(20);*/
        //return view('msc::pagination',['data'=>$pagination]);
        return view('msc::wechat.resourceborrow.borrow_apply_wait');
    }


    /**
     * 根据设备名称关键字获取设备名称列表（名称无重复）
     * @api GET /msc/wechat/resources-manager/resources-name-list
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
        ]);
        $keyword=e(Input::get('keyword'));
        $resourcesToolsRepository = App::make('Modules\Msc\Repositories\ResourcesToolsRepository');
        $pagination=$resourcesToolsRepository->getResourcesListByKeyword($keyword,'name')->groupBy('name')->orderBy('id','desc')->paginate(20);

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
     * 学生扫描外借设备信息着陆
     *
     */
    public function getFindResource(ResourcesToolsItems $resourcesToolsItems,ResourcesBorrowing $resourcesBorrowing){
        $code = Input::get('code');
        $Items = $resourcesToolsItems->where('code','=',$code)->with('resourcesTools')->get()->first();
        $user=Auth::user();
        $user_id = empty($user)?2:$user->id;
        if($Items['status'] == 1){
            $borrowing = $resourcesBorrowing->where('resources_tool_id','=',$Items['resources_tool_id'])->where('lender','=',$user_id)->where('apply_validated','=','0')->get()->first();
            $Items['begindate'] = date('m.d',strtotime($borrowing['begindate']));
            $Items['enddate'] = date('m.d',strtotime($borrowing['begindate']));
            $Items['borrowingId'] = $borrowing['id'];
            $data = [
                'item' =>   $Items
            ];
            return view('msc::wechat.resourceborrow.borrow_confirm',$data);
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'设备信息不匹配'));
        }
    }
    /*
     * 学生确认  外借该设备
     *
     * */
    public function postStudentConfirm(Request $request,ResourcesBorrowing $resourcesBorrowing){
        $requests = $request->all();
        $rew = $resourcesBorrowing->where('id','=',$requests['borrowingId'])->update(array('apply_validated'=>1,'resources_tool_item_id'=>$requests['resources_tool_item_id']));
        if($rew){
            $openid = \Illuminate\Support\Facades\Session::get('openid','');
            $this->sendMsg('确认外借成功！',$openid);
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'确认失败！'));
        }

    }

    /**
     * 学生借出设备前扫描设备(处理页面)
     *
     */
    public function postFindResource(Request $request){
        return redirect()->action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@getBorrowManage');
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

            $apply->status=4;//锁定申请
            $result=$apply->save();
            if($result)
            {
                $returnData=[
                    'name'=>$resource->name,
                    'code'=>$resource->code,
                    'start'=>$apply->begindate,
                    'end'=>$apply->enddate
                ];
                //redirect('/msc/wechat/resources-manager/borrow-manage');
                return redirect()->action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@getBorrowManage');
            }
            else
            {
                throw new \Exception('选定设备失败，请重新扫描');
            }
        }catch (\Exception $ex)
        {
            return redirect()->back();
        }
    }


    /**
     * 外借时老师扫描设备 后查看到的 确认表单着陆页
     *
     */
    public function getSureBorrow(ResourcesToolsItems $resourcesToolsItems,ResourcesBorrowing $resourcesBorrowing,ResourcesTools $resourcesTools){
        $code = Input::get('code');
        $Items = $resourcesToolsItems->where('code','=',$code)->get()->first();
        $BorrowingList = [];
        if(!empty($Items['id'])){
            $BorrowingList = $resourcesBorrowing->where('resources_tool_item_id','=',$Items['id'])->where('apply_validated','=',1)->with('user','resourcesTool')->get()->first();
            $BorrowingList['begindate'] = date('m.d',strtotime($BorrowingList['begindate']));
            $BorrowingList['enddate'] = date('m.d',strtotime($BorrowingList['enddate']));
            $BorrowingList['code'] = $code;
        }
        $data = [
            'BorrowingList'=>$BorrowingList
        ];
        if(!empty($BorrowingList)){
            return view('msc::wechat.resource.borrow_confirm',$data);
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'设备信息不匹配'));
        }


    }
    /*
     * /*    DB::beginTransaction();
    DB::commit();
    DB::rollback();
     *
     *
     * 老师确认外借
     *
     * */
    public function postTeacherConfirm(Request $request,ResourcesBorrowing $resourcesBorrowing,ResourcesTools $resourcesTools,User $user,ResourcesToolsItems $resourcesToolsItems){
        $requests = $request->all();
        $user = Auth::user();
        $user_id = empty($user)?5:$user->id;
        DB::connection('msc_mis');
        if(!empty($requests['BorrowingId'])){
            DB::beginTransaction();
            $rew = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->update(array('real_begindate'=>date('Y-m-d H:i:s',time()),'loan_validated'=>1,'status'=>0,'loan_operator_id'=>$user_id));
            if($rew){
                $BorrowingInfo = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->get()->first();
                $ToolsInfo = $resourcesTools->where('id','=',$BorrowingInfo['resources_tool_id'])->get()->first();
                $return =$resourcesTools->where('id','=',$BorrowingInfo['resources_tool_id'])->update(array('loaned'=>($ToolsInfo['loaned']+1)));
                if($return){
                    DB::commit();
                    $userInfo = $user->where('id','=',$BorrowingInfo->lender)->get()->first();
                    //dd($BorrowingInfo->resources_tool_item_id);
                    $ToolsItemsInfo = $resourcesToolsItems->where('id','=',$BorrowingInfo->resources_tool_item_id)->get()->first();
                    //dd($ToolsItemsInfo->code);
                    $openid = \Illuminate\Support\Facades\Session::get('openid','');
                    $this->sendMsg('外借成功，设备名称:'.$ToolsInfo->name.',设备编号：'.$ToolsItemsInfo->code,$openid);
                }else{
                    DB::rollback();
                    return view('msc::wechat.index.index_error',array('error_msg'=>'确认外借失败'));
                }
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'数据有误'));
        }

    }




    /**
     * 设备外借归还管理页面(教师用)
     *
     */
    public function getBorrowBackManage(){
        return view('msc::wechat.resource.borrow_manage_surereturn');
  /*      $user=Auth::user();
        $UserRepository=App::make('App\Repositories\UserRepository');
        $userProfile=$UserRepository->getUserProfile($user->id);
        if($userProfile['usr_type']=='teacher')
        {
             return view('msc::wechat.borrowreturn.borrow_teacher_manage');
        }*/
    }
    /**
     * 设备外借功能选择页面(学生用:外借申请，借用时扫一扫入口)
     *
     */
    public function getBorrowManage(){
        return view('msc::wechat.resourceborrow.borrow_manage');
    }


    /**
     * 现有外借设备
     *
     */
    public function getRecordList(ResourcesBorrowing $ResourcesBorrowing){

        $page = Input::get('page');
        $BorrowingBuilder = $ResourcesBorrowing->where('status','=','0');
        $nowList = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->orderBy('id')->paginate(20);

        foreach($nowList as $k => $v){
            $nowList[$k]['begindate'] = date('m.d',strtotime($nowList[$k]['begindate']));
            $nowList[$k]['enddate'] = date('m.d',strtotime($nowList[$k]['enddate']));
            $nowList[$k]['real_begindate'] = date('m.d',strtotime($nowList[$k]['real_begindate']));
            $nowList[$k]['real_enddate'] = date('m.d',strtotime($nowList[$k]['real_enddate']));
        }

        $data = [
            'nowList'=>$nowList
        ];
        //如果有页码 并且大于1 的时候返回json数据
        if(!empty($page)&&$page>1){
            return response()->json(
                $this->success_rows(1,'获取成功',$nowList->total(),20,$nowList->currentPage(),array('nowList'=>$nowList->toArray()))
            );
        }else{
            return view('msc::wechat.resource.borrow_now',$data);
        }
    }
    /**
     * 现有外借设备数据
     *
     */
    public function getRecordListData(ResourcesBorrowing $ResourcesBorrowing){
        $page = Input::get('page');
        $BorrowingBuilder = $ResourcesBorrowing->where('status','=','0');
        $nowList = $BorrowingBuilder->with('user','resourcesTool','resourcesToolItem')->orderBy('id')->paginate(5);
        foreach($nowList as $k => $v){
            $nowList[$k]['begindate'] = date('m.d',strtotime($nowList[$k]['begindate']));
            $nowList[$k]['enddate'] = date('m.d',strtotime($nowList[$k]['enddate']));
            $nowList[$k]['real_begindate'] = date('m.d',strtotime($nowList[$k]['real_begindate']));
            $nowList[$k]['real_enddate'] = date('m.d',strtotime($nowList[$k]['real_enddate']));
        }
        $data = [
            'nowList'=>$nowList
        ];
        return response()->json(
            $this->success_rows(1,'获取成功',$nowList->total(),20,$nowList->currentPage(),array('nowList'=>$nowList->toArray()))
        );

    }

    //老师归还时扫一扫
    public function getTeacherManageSurereturn(ResourcesToolsItems $resourcesToolsItems,ResourcesBorrowing $resourcesBorrowing){
        $code = Input::get('code');
        if(!empty($code)){
            $ToolsItems = $resourcesToolsItems->where('code','=',$code)->get()->first();
            if(!empty($ToolsItems['id'])){
                $BorrowingInfo = $resourcesBorrowing->where('resources_tool_item_id','=',$ToolsItems['id'])->where('status','=',0)->with('user','resourcesTool','resourcesToolItem')->get()->first();
                if(!empty($BorrowingInfo)){
                    $BorrowingInfo['begindate'] = date('m.d',strtotime($BorrowingInfo['begindate']));
                    $BorrowingInfo['enddate'] = date('m.d',strtotime($BorrowingInfo['enddate']));
                    $BorrowingInfo['real_begindate'] = date('m.d',strtotime($BorrowingInfo['real_begindate']));
                    $BorrowingInfo['real_enddate'] = date('m.d',strtotime($BorrowingInfo['real_enddate']));
                    $data = [
                        'BorrowingInfo'=>$BorrowingInfo
                    ];
                    return view('msc::wechat.resource.borrow_manage_surereturn',$data);
                }else{
                    return view('msc::wechat.index.index_error',array('error_msg'=>'没有对应的外借记录'));
                }
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'没有该设备的信息'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'没有设备编号'));
        }
    }
/*    DB::beginTransaction();
    DB::commit();
    DB::rollback();
$new = serialize($stooges);
print_r($new);echo "<br />";
print_r(unserialize($new));
*/
    //老师归还时扫一扫之后的确认
    public function postTeacherConfirmBack(Request $request,ResourcesBorrowing $resourcesBorrowing,ResourcesToolsItems $resourcesToolsItems,ResourcesTools $resourcesTools){
        $requests = $request->all();
        DB::connection('msc_mis');
        if(!empty($requests['BorrowingId']) && !empty($requests['Grade']) ){
            if($requests['Grade'] == 2){
                DB::beginTransaction();

                $new = '';
                if(!empty($requests['images'])){
                    $new = serialize($requests['images']);
                }
                $rew = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->update(array('status'=>4,'bad_images'=>$new,'bad_description'=>$requests['bad_description']));
                if($rew){
                    $BorrowingInfo = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->get()->first();
                    $return = $resourcesToolsItems->where('id','=',$BorrowingInfo['resources_tool_item_id'])->update(array('status'=>0));
                    if($return){
                        DB::commit();
                        dd('损坏提交成功');
                    }else{
                        DB::rollback();
                       dd('损坏提交失败');
                    }
                }else{
                    return view('msc::wechat.index.index_error',array('error_msg'=>'归还失败'));
                }
            }elseif($requests['Grade'] == 1){
                $rew = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->update(array('status'=>1));
                if($rew){
                    $BorrowingInfo = $resourcesBorrowing->where('id','=',$requests['BorrowingId'])->get()->first();
                    $ToolsInfo = $resourcesTools->where('id','=',$BorrowingInfo['resources_tool_id'])->get()->first();
                    $return = $resourcesTools->where('id','=',$BorrowingInfo['resources_tool_id'])->update(array('loaned'=>($ToolsInfo['loaned']-1),));
                    if($return){
                        DB::commit();
/*                        $openid = \Illuminate\Support\Facades\Session::get('openid','');
                        $this->sendMsg('归还成功',$openid);*/
                        dd('归还成功');
                    }else{
                        DB::rollback();
/*                        $openid = \Illuminate\Support\Facades\Session::get('openid','');
                        $this->sendMsg('归还失败',$openid);*/
                        dd('归还失败');
                    }
                }else{
                    return view('msc::wechat.index.index_error',array('error_msg'=>'归还失败'));
                }
            }else{
                return view('msc::wechat.index.index_error',array('error_msg'=>'没有该类操作'));
            }
        }else{
            return view('msc::wechat.index.index_error',array('error_msg'=>'非法操作'));
        }
    }

    /**
     * 新增资源
     * @method POST /msc/wechat/resources-manager/add-resources
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        resources_type       新增资源的TYPE(必须的)  外借设备为 TOOLS  教室 为ClASSROOM
     * * string        name                设备名称(必须的)

     * * int           cate_id             设备名称(必须的)
     * * string        manager_name        设备负责人(必须的)
     * * string        manager_mobile      设备负责人电话(必须的)
     * * string        location            地址(必须的)
     * * string        detail              设备功能(必须的)
     * * Array        code                编码(必须的)<input type="hidden" name="code[]" value="123415123">
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
                $view=$this->addClassRommResources($request);
                break;
            default:
                return redirect()->back()->withErrors(['没有选择新增资源type']);
        }
        return $view;
    }
    /*
    * 新增外接设备
    */
    private function addToolsResources(Request $request){
        $this->validate($request,[
            'repeat_max'=>'sometimes|integer',
            'name'=>'required',
            'cate_id'=>'required|integer',
            'manager_id'=>'sometimes|integer',
            'manager_name'=>'sometimes',
            'manager_mobile'=>'sometimes|mobile_phone',
            'location' => 'required',
            'detail' => 'required|max:255|min:0',
            'loan_days' => 'sometimes|min:0',
        ]);
        $formData = $request->only(['repeat_max', 'name', 'cate_id', 'manager_id','manager_name', 'manager_mobile',  'location', 'detail','loan_days','total','images_path']);
        $codeList=$request->get('code');

        try
        {
            foreach($codeList as $code)
            {
                if(empty($code))
                {
                    throw new \Exception('编码不能为空');
                }
            }
            $connection = DB::connection('msc_mis');
            $connection->beginTransaction();
            $formData['total']=count($codeList);
            $formData['repeat_max']=empty( $formData['repeat_max'])? 0: $formData['repeat_max'];
            $formData['manager_id']=empty( $formData['manager_id'])? 0: $formData['manager_id'];
            $formData['loan_days']=empty($formData['loan_days'])? 0:$formData['loan_days'];

            $resources = ResourcesTools::create($formData);
            if(!$resources)
            {
                throw new \Exception('新增资源失败');
            }
            $_formData = [
                'type' => 'TOOLS',
                'item_id' => $resources->id,
                'description' => ''
            ];

            $_resources = Resources::create($_formData);
            if(!$_resources)
            {
                throw new \Exception('新增资源失败');
            }
            $resourcesRepository = App::make('\Modules\Msc\Repositories\ResourcesRepository');
            try{
                //删除不要的图片
                $resourcesRepository->ResourcesImageDel($_resources->id,$formData['images_path']);
            }catch (\Exception $ex)
            {
            }
            //新增图片
            //$pathList=Common::saveImags($request,'images');
            $hasList = ResourcesImage::where('Resources_id','=',$_resources->id)->get();
            if(empty($formData['images_path']))
            {
                $formData['images_path']=[];
            }
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
                    'resources_id'=>$_resources->id,
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

            //$pathList = Common::saveImags($request, 'images_path');
            foreach($codeList as $code)
            {
                $itemData=[
                    'resources_tool_id'=>$resources->id,
                    'code'=>$code,
                    'status'=>1,
                ];
                $result=ResourcesToolsItems::create($itemData);
                if(!$result)
                {
                    throw new \Exception('资源编码保存失败');
                }
            }
            dd($result);
            $connection->commit();
            return redirect()->action('\Modules\Msc\Http\Controllers\WeChat\ResourceController@getResourceAdd');
            // return back()->withInput(); @todo with some status code
        }
        catch(\Exception $ex)
        {
            dd(123);
            $connection->rollback();
            return redirect()->back()->withErrors($ex);
        }
    }
    /*
    * 新增教室
    */
    private function addClassRommResources(Request $request){

    }



    
}