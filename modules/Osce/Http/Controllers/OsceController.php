<?php namespace Modules\Osce\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class OsceController extends Controller {
	
	public function index()
	{
		return view('osce::layouts.admin');
	}
	public function test()
	{
		return view('osce::index');
	}
}