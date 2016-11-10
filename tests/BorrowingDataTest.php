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


class BorrowingDataTest  extends TestCase
{
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