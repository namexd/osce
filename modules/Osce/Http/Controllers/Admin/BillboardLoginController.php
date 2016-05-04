<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 17:45
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\PadLogin\Time;
use Modules\Osce\Http\Controllers\CommonController;

class BillboardLoginController extends CommonController
{
    /**
     *
     * @url 
     * @access public
     * @param PadLoginRepository $padLoginRepository
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getIndex(PadLoginRepository $padLoginRepository)
    {
        \App::bind('TimeInterface', function () {
            return new Time();
        });

        $examList = $padLoginRepository->examList(\App::make('TimeInterface'));
        return view('osce::admin.billboard_login', ['exam' => $examList]);
    }
    
    
}