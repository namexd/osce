<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 14:05
 */

namespace Modules\Osce\Http\Controllers\Admin\Branch;

use Modules\Osce\Entities\QuestionBankEntities\ExamControl;
use Modules\Osce\Http\Controllers\CommonController;
use Illuminate\Http\Request;


/**考试监控
 * Class ExamControlController
 * @package Modules\Osce\Http\Controllers\Admin\Branch
 */

class ExamControlController extends CommonController
{
    /**正在考试列表
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\View\View
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function getExamlist()
    {
        $examControlModel = new ExamControl();
        $data = $examControlModel->getDoingExamList();
        //dd($data);
        return view('osce::admin.testMonitor.monitor_test', [
            'data'      =>$data,
        ]);
    }

    /**终止考试数据交互
     * @method
     * @url /osce/
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postStopExam2(Request $request)
    {
        $data=array(
            'examScreeningStudentId' =>$request->input('170'), //考试场次-学生关系id
            'description' =>$request->input('2'), //终止考试原图

        );
        dd($data);

        $examControlModel = new ExamControl();
        $result = $examControlModel->stopExam($data);
        dd($result);

        return response()->json(['status'=>'1','info'=>'保存成功']);
    }
    public function postStopExam()
    {
        $data=array(
            'examScreeningStudentId' =>170, //考试场次-学生关系id
            'description' =>2, //终止考试原图

        );

        $examControlModel = new ExamControl();
        $result = $examControlModel->stopExam($data);
        dd($result);

    }




















}