<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 10:50
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamDraftFlowTemp;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
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
            $result = ExamDraftFlowTemp::create($data);
            if(!$result){
                throw new \Exception('保存临时考站失败');
            }else{
                return response()->json(
                    $this->success_data($result->id, 1, 'success')
                );
            }

        }catch (\Exception $ex){

            return response()->json(
                $this->fail($ex)
            );

        }


}

//新增考站里面的子对象
    public function postExamDraft(Request $request){
        $this->validate($request,[
            'exam_id'=>'required',
            'station_id'=>'required',
            'room_id'=>'required',
            'subject_id'=>'required',
            'ctrl_type'=>'required',
            'old_draft_flow_id'=>'required',
            'old_draft_id'=>'required',
            'used'=>'required',
            'user_id'=>'required',
        ]);

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



}