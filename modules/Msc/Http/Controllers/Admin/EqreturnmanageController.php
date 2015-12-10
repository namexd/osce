<?php namespace Modules\Msc\Http\Controllers\Admin;

use Pingpong\Modules\Routing\Controller;

class EqreturnmanageController extends Controller {
	
	public function apply()
	{
		return view('msc::admin.eqreturnmanage.apply');
	}
	public function borrowed()
	{
		return view('msc::admin.eqreturnmanage.borrowed');
	}
	public function history()
	{
		return view('msc::admin.eqreturnmanage.history');
	}
	
}