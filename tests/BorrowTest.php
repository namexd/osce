<?php

/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2015/11/4
 * Time: 17:19
 */
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;


class BorrowTest  extends TestCase
{
	//获取随机学生
	private function getRandStudent(){
		$list   =   \Modules\Msc\Entities\Student::where('id','>',48)->get();
		return $this->getRandItem($list);
	}
	protected function getRandItem($list){
		if(count($list)==0) return [];
		$index  =   rand(0,count($list)-1);
		foreach($list as $key=>$item){
			if($index==$key)
			{
				return $item;
			}
		}
	}
	private function getRandStr($lenth){
		$str    =   '';
		for($i=1;$i<=$lenth;$i++)
		{
			$str.=rand(0,9);
		}
		return $str;
	}
	public function getRandOpenDevice(){
		$list   =   \Modules\Msc\Entities\ResourcesDevice::get();
		return $this->getRandItem($list);
	}
	public function testApplyTools(){
		$user   =   $this->getRandStudent();
		$userOb	=	\App\Entities\User::find($user->id);
		$openDevice     =   $this->getRandOpenDevice();
		$timeConfig     =   [
				'08:00-09:00',
				'10:00-11:00',
				'12:00-13:00',
				'14:00-17:00',
				'17:00-18:00',
		];
		$time   =   $this->getRandItem($timeConfig);
		$dateConfig   =   [
				date('Y-m-d'),
				date('Y-m-d',time()+3600*24),
				date('Y-m-d',time()+3600*24*2),
		];
		$date   =   $this->getRandItem($dateConfig);
		$timeArray	=	explode('-',$time);

		$toolsList	=	\Modules\Msc\Entities\ResourcesTools::get();
		$tools	=	$this->getRandItem($toolsList);

		$data	=	[
			'resources_tool_id'=>$tools->id,
			'begindate'     => 	$date.' '.$timeArray[0].':00',
			'enddate'       => 	$date.' '.$timeArray[1].':00',
			'detail'        => 	'测试申请'.$this->getRandStr(6),
		];
		$response=$this
				->withSession(['openid'=>$userOb->openid])
				->actingAs($userOb)
				->action('post','\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddBorrowApply','',$data);
	}
	public function testAgreeApply(){
		$list=\Modules\Msc\Entities\ResourcesBorrowing::where('apply_validated','=','0')->limit(1)->get();
		$apply=$list->first();
		if(is_null($apply))
		{
			$this->assertTrue(true);
		}
		$data=[
			'id'=>$apply->id,
			'apply_validated'=>1,
			'time_start'=>$apply->begindate,
			'time_end'=>$apply->enddate,
			'idcard_type'=>'学生证',
		];
		$response=$this->action('post','\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@postExamineBorrowingApply','',$data);
		$view = $response->getContent();
		$data=json_decode($view);
		$this->assertTrue($data->code==1);
	}
	public function testaRefuse(){
		$list=\Modules\Msc\Entities\ResourcesBorrowing::where('apply_validated','=','0')->limit(1)->get();
		$apply=$list->first();
		if(is_null($apply))
		{
			$this->assertTrue(true);
		}
		$data=[
				'id'=>$apply->id,
				'apply_validated'=>-1,
				'detail'=>'测试拒绝'
		];
		$response=$this->action('post','\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@postExamineBorrowingApply','',$data);
		$view = $response->getContent();
		$data=json_decode($view);
		$this->assertTrue($data->code==1);
	}
	public function testaTip(){
		$list=\Modules\Msc\Entities\ResourcesBorrowing::where('status','=','0')->limit(1)->get();
		$apply=$list->first();
		$data=[
				'id'=>$apply->id,
				'detail'=>'测试提示归还'
		];
		$response=$this->action('get','\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@getTipBack','',$data);
		$view = $response->getContent();
		$data=json_decode($view);
		$this->assertTrue($data->code==1);
	}

}