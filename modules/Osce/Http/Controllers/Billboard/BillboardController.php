<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 19:50
 */

namespace Modules\Osce\Http\Controllers\Billboard;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Billboard\BillboardRepository;
use Modules\Osce\Http\Controllers\CommonController;

class BillboardController extends CommonController
{
    /**
     * 着陆页
     * @url osce/billboard/index
     * @access public
     * @param Request $request
     * @param BillboardRepository $billboardRepository
     * @请求字段：
     * 考试id:exam_id
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getIndex(Request $request, BillboardRepository $billboardRepository)
    {
        $data = $billboardRepository->getData($request->input('exam_id'));
        return view('osce::billboard.index', ['data' => $data]);
    }

    
}