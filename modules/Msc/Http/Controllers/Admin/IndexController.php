<?php namespace Modules\Msc\Http\Controllers\Admin;

use Pingpong\Modules\Routing\Controller;

class IndexController extends Controller {
	
	public function index()
	{
		return view('msc::admin.index');
	}
	
}