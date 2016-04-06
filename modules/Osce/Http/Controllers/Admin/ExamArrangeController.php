<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 10:50
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Invigilator;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;

class ExamArrangeController extends CommonController
{
    //考试安排着陆页
    //新增考试安排的站

    public function postAddExamFlow(Request $request){
        try{
            $this->validate($request,[
                'exam_id'=>'required',
                'name'=>'required',
                'order'=>'required',
                'exam_gradation_id'=>'required',
            ]);
            $examId = $request->get('exam_id');
            $name = $request->get('name');
            $order = $request->get('order');
            $examGradationId = $request->get('exam_gradation_id');
            $data =[
                'exam_id'=>$examId,
                'name'=>$name,
                'order'=>$order,
                'exam_gradation_id'=>$examGradationId,
                'exam_screening_id'=>'',
            ];
            //先保存到临时表
//            if(){
//
//            }

        }catch (\Exception $ex){

            return response()->json(
                $this->fail($ex)
            );

        }


}




    //获取考场接口
    public function getRoomList(Request $request){
        $this->validate($request,[
            'station_name'=>'sometimes',
//            'id'=>'required',
        ]);
        $name = $request->get('station_name');

        $id = $request->get('id');
        $roomModel = new Room();
        $roomData = $roomModel -> showRoomList($keyword = '', $type = '0', $id = '');

        return response()->json(
            $this->success_data($roomData, 1, 'success')
        );
        

    }



    //获取考站接口
    public function getStationList(Request $request){
        $this->validate($request,[
            'station_name'=>'sometimes',
//            'id'=>'required',
        ]);
        $name = $request->get('station_name');
        
        $id = $request->get('id');
        //查询出已用过的考站
        
        $stationModel = new Station();
        $stationData = $stationModel -> showList($stationIdArray = [],$ajax = true,$name);

//        dd($stationData);

        return response()->json(
            $this->success_data($stationData, 1, 'success')
        );

        
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

    /**
     * 根据考试项目 获取对应下的考官、SP（接口）
     * @param Request $request
     */
    public function getInvigilatesBySubject(Request $request){
        try{
            //验证
            $this->validate($request, [
                'subject_id' => 'required|integer',
                'type'       => 'required|integer'
            ]);
            $subject_id = intval($request->get('subject_id'));
            $type       = intval($request->get('type'));
            $teacherSubject = new TeacherSubject();
            //根据考试项目 获取对应的考官
            $invigilates= $teacherSubject->getTeachers($subject_id, $type);

            return response()->json(
                $this->success_data($invigilates, 1, 'success')
            );

        } catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }
    }

}