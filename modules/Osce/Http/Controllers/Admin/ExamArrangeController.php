<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 10:50
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Http\Controllers\CommonController;

class ExamArrangeController extends CommonController
{
    //考试安排着陆页
    //获取考场接口
    //获取考站接口
    public function getStationData(){
        //查询出该站是否已选择过该考站
        //如果有就不再显示该考站数据
        //如果没有就显示所有考站数据
        
    }

    /**
     * 获取考试项目（异步）
     * @param Request $request
     * @author Zhoufuxiang 2016-04-06
     * @return string
     */
    public function getAllSubjects(Request $request){
        try{
            $data = Subject::where('archived','<>',1)->select(['id','title'])->get();

            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        }catch (\Exception $ex){
            return $this->fail($ex);
        }
    }


    /**
     * 保存编辑考试基本信息
     * @url GET /osce/admin/exam-arrange/invigilate-arrange
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        考试id(必须的)
     *
     * @return view
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getInvigilateArrange(Request $request){
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得exam_id
        $id = $request->input('id');
        $data = [];
        return view('osce::admin.examManage.examiner_manage', ['id'=>$id, 'data' => $data]);
    }


}