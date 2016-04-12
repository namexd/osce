<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 10:50
 */

namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;
use Modules\Osce\Entities\ExamDraftFlowTemp;
use Modules\Osce\Entities\ExamDraftTemp;
use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class ExamArrangeController extends CommonController
{

    /**
     * 新增考试安排的站
     * @url GET /osce/admin/exam-arrange/add-exam-flow
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */


    public function postAddExamFlow(Request $request)
    {
        try {
            $this->validate($request, [
                'exam_id' => 'required',
                'name' => 'required',
                'order' => 'required',
                'exam_gradation_id' => 'sometimes', //阶段
                'type' => 'sometimes',
            ]);
            $examId = $request->get('exam_id');
            $name = $request->get('name');
            $order = $request->get('order');
            $examGradationId = $request->get('exam_gradation_id');
            $type = $request->get('type');


            //判断操作人是否一致

            //获取当前操作信息
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('未找到当前操作人信息');
            }
            $data = [
                'exam_id' => $examId,
                'name' => $name,
                'order' => $order,
                'exam_gradation_id' => $examGradationId,
                'exam_draft_flow_id' => $request->get('flow_id'),
                'user_id' => $user->id,
                'ctrl_type' => $type,
            ];


            if(is_null($type)){
                $data['ctrl_type'] = 1;
            }

            //先保存到临时表
            if ($type == 3) {

                $examDraftFlow = ExamDraftFlowTemp::find($data['exam_draft_flow_id']);

                $examDraftFlow->ctrl_type =1 ;

                $examDraftFlow->exam_gradation_id = $examGradationId ;

                if($examDraftFlow->save()){
                    return response()->json(
                        $this->success_data([], 1, 'success')
                    );
                }

            }

            $result = ExamDraftFlowTemp::create($data);

                if ($result&&$type != 2) {

                    //新增一条空的考站的子站数据
                    $DraftData = [
                        'exam_id' => $examId,
                        'old_draft_flow_id' => $result->id,
                        'ctrl_type' => 4,
                        'used' => 0,
                        'add_time' => date('Y-m-d H:i:s',time()+1),
                        'user_id' => $user->id,
                    ];
                    $DraftResult = ExamDraftTemp::create($DraftData);

                    if (!$DraftResult) {
                        throw new \Exception('保存临时考站失败');
                    }

                } else {
                    throw new \Exception('保存临时考站失败');

                }
            return response()->json(
                $this->success_data(['id' => $result->id, 'draft_id' => $DraftResult->id], 1, 'success')
            );
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );

        }

    }


    /**
     * 新增考站里面的子对象到临时表
     * @url GET /osce/admin/exam-arrange/add-exam-draft
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */


    public function postAddExamDraft(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required',
            'type' => 'required',

            'subject' => 'sometimes',      //考试项目
            'station' => 'sometimes',      //考站
            'room' => 'sometimes',      //考场
            'chioce' => 'sometimes',      //选考
        ]);

        $type = $request->get('type');
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject');
        $stationId = $request->get('station');
        $roomId = $request->get('room');
        $DraftId = $request->get('draft_id');

        try {
            //获取当前操作信息
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('未找到当前操作人信息');
            }
            $data = [
                'exam_id' => $examId,
                'old_draft_flow_id' => $request->get('flow_id'),
                'old_draft_id'=>null,
                'user_id' => $user->id,
                'subject_id' => $subjectId,
                'station_id' => $stationId,
                'room_id' => $roomId,
                'add_time'  =>  date('Y-m-d H:i:s'),
                'used' => 0,
                'ctrl_type' => $request->get('type'),
            ];

            if ($type == 2) {
                $data['old_draft_id'] =$DraftId ;
            }

            if ($type == 3) {

                $ExamDraftTempType  = ExamDraftTemp::find($DraftId);
                if(!is_null($subjectId)){
                    $ExamDraftTempType->subject_id =$data['subject_id'];
                }
                if(!is_null($stationId)){
                    $ExamDraftTempType->station_id =$data['station_id'];
                }
                if(!is_null($roomId)){
                    $ExamDraftTempType->room_id =$data['room_id'];
                }

                //根据临时表id判断是否是该之前的数据
                if($ExamDraftTempType->ctrl_type ==4 ||$ExamDraftTempType->ctrl_type ==6){
                    $ExamDraftTempType->ctrl_type =6;
                }else{
                    $ExamDraftTempType->ctrl_type =1;
                }
                if($ExamDraftTempType->save()){
                    return response()->json(
                        $this->success_data(['id'=>$ExamDraftTempType->id], 1, 'success')
                    );

                }
            }

            if ($type == 4) {
                $data['ctrl_type'] = $type;
            }

            $result = ExamDraftTemp::create($data);

            if (!$result) {
                throw new \Exception('保存临时考站数据失败');
            } else {

                return response()->json(
                    $this->success_data(['id'=>$result->id], 1, 'success')
                );
            }

        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }


    }
    /**
     * 删除站接口
     * @url GET /osce/admin/exam-arrange/del-exam-flow
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */


    public function getDelExamFlow(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required',
            'flow_id' => 'required',
            'type' => 'required',
        ]);
        $id = $request->get('flow_id');
        $exam_id = $request->get('exam_id');
        $type = $request->get('type');
        try {

            $data = [
                'exam_id' => $exam_id,
                'ctrl_type' => $type,
                'exam_draft_flow_id' => $id,
            ];

            if ($type == 2) {
                $data['ctrl_type']=5;
                //是删除真实表数据就在临时表中记录下该操作
                $result = ExamDraftFlowTemp::create($data);
                
                if ($result) {
                    return response()->json(
                        $this->success_data($result->id, 1, '删除成功')
                    );
                }

            } else {
                //是删除临时表数据 则直接删除临时表中 对应记录
                //1、先删除对应小站数据
                $draftTemps = ExamDraftTemp::where('old_draft_flow_id','=',$id)->get();
                if(count($draftTemps)>0){
                    foreach ($draftTemps as $draftTemp) {
                        if(!$draftTemp->delete()){
                            throw new \Exception('对应小站数据删除失败！');
                        }
                    }
                }
                //1、再删除对应站的数据
                if (ExamDraftFlowTemp::where('id','=',$id)->delete()){
                    return response()->json(
                        $this->success_data(['id'=>$id], 1, '删除成功')
                    );
                }

//                $result = ExamDraftFlowTemp::create($data);
////                $result = ExamDraftFlowTemp::find($id);
////                $result->old_draft_flow_id = $id;
////                $result->ctrl_type = $type;
//                if ($result) {
//                    return response()->json(
//                        $this->success_data($result->id, 1, '删除成功')
//                    );
//                }
            }

        } catch (\Exception $ex) {

            return response()->json(
                $this->fail($ex)
            );

        }


    }


    //删除子站
    public function getDelExamDraft(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required',
            'draft_id' => 'required',
            'flow_id' => 'required',
            'type' => 'required',
        ]);
        $id = $request->get('draft_id');
        $exam_id = $request->get('exam_id');
        $type = $request->get('type');
        $flowId = $request->get('flow_id');
        try {

            $data = [
                'exam_id' => $exam_id,
                'ctrl_type' => $type,
                'old_draft_id' => null,
                'old_draft_flow_id' => $flowId,
                'add_time' => date('Y-m-d H:i:s'),
            ];
            if ($type == 2) {
                $data['ctrl_type']=5;
                $data['old_draft_id']=$id;
                //是删除真实表数据就在临时表中记录下该操作
                $DraftResult = ExamDraftTemp::create($data);
                if ($DraftResult) {

                    return response()->json(
                        $this->success_data($DraftResult->id, 1, '删除成功')
                    );
                }

            } else {
//                $DraftResult = ExamDraftTemp::create($data);
//
//                if ($DraftResult->save()) {

                //是删除临时表数据 则直接删除临时表中 对应记录
                if (ExamDraftTemp::where('id','=',$id)->delete()){

                    return response()->json(
                        $this->success_data(['id'=>$id], 1, '删除成功')
                    );
                }

//                $DraftResult = ExamDraftTemp::create($data);
////                $DraftResult = ExamDraftTemp::find($id);
////
////                $DraftResult->old_draft_id = $id;
////
////                $DraftResult->ctrl_type = $type;
//                if ($DraftResult->save()) {
//                    return response()->json(
//                        $this->success_data($DraftResult->id, 1, '删除成功')
//                    );
//                }
            }

        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }

    }


    //获取考场接口
    public function getRoomList(Request $request)
    {
        $this->validate($request, [
            'station_name' => 'sometimes',
            'id' => 'required',
//            'type'=>'required',
//            'draft_id'=>'required'
        ]);
        $name = $request->get('station_name');
        $id = $request->get('id');

        $roomIdArray = ExamDraftTemp::where('old_draft_flow_id', '=', $id)->get()->pluck('room_id')->toArray();
        $roomModel = new Room();
//        $roomData = $roomModel -> showRoomList($keyword = '', $type = '0', $id = '');

        $roomData = $roomModel->getRoomList($roomIdArray, $name);
        return response()->json(
            $this->success_data($roomData, 1, 'success')
        );


    }


    //获取考站接口
    public function getStationList(Request $request)
    {
        $this->validate($request, [
            'station_name' => 'sometimes',
            'id' => 'required',
//            'type'=>'required',
//            'draft_id'=>'required'
        ]);

        $name = $request->get('station_name');
        $id = $request->get('id');


        //查询出已用过的考站
        $stationIdArray = ExamDraftTemp::where('old_draft_flow_id', '=', $id)->get()->pluck('station_id')->toArray();
        $stationModel = new Station();
        $stationData = $stationModel->showList($stationIdArray, $ajax = true, $name);

        return response()->json(
            $this->success_data($stationData, 1, 'success')
        );

    }

    /**
     * 获取考试所有的阶段（异步接口）
     * @url GET /osce/admin/exam-arrange/all-gradations
     * @param Request $request
     * @author Zhoufuxiang 2016-04-06
     * @return string
     */
    public function getAllGradations(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'exam_id' => 'required|integer',
            ]);
            $exam_id = intval($request->get('exam_id'));
            $data = ExamGradation::where('exam_id', '=', $exam_id)->get();

            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }

    /**
     * 获取考试项目（异步接口）
     * @url GET /osce/admin/exam-arrange/all-subjects
     * @param Request $request
     * @author Zhoufuxiang 2016-04-06
     * @return string
     */
    public function getAllSubjects(Request $request)
    {
        try {
            $data = Subject::where('archived', '<>', 1)->select(['id', 'title'])->get();

            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        } catch (\Exception $ex) {
            return $this->fail($ex);
        }
    }


    /**
     * 考官安排着陆页
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
     *
     *
     */
    public function getInvigilateArrange(Request $request)
    {
        //验证
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        //获得exam_id
        $exam_id = $request->input('id');
        $exam    = Exam::where('id','=',$exam_id)->first();
        if (is_null($exam)){
            return redirect()->back()->withErrors('没有找到对应的考试！');
        }
        //判断考官安排是考场还是考站安排
//        if(){
//
//        }
        $ExamDraft     = new ExamDraft();
        $datas = $ExamDraft->getDraftFlowData($exam_id);

        return view('osce::admin.examManage.examiner_manage', ['id' => $exam_id, 'data' => $datas]);
    }

    
    
    

    //回显数据ajax请求
     public function getExamTeacherArrange(Request $request){
         $this->validate($request, [
             'exam_id' => 'required|integer'
         ]);
         //获得exam_id
         $exam_id = $request->input('exam_id');
         $exam    = Exam::where('id','=',$exam_id)->first();
         if (is_null($exam)){
             return redirect()->back()->withErrors('没有找到对应的考试！');
         }
         //判断考官安排是考场还是考站安排
//        if(){
//
//        }
         $ExamDraft     = new ExamDraft();
         $datas = $ExamDraft->getDraftFlowData($exam_id);


         
         foreach ($datas as &$teacherData){


             //查询出考站下对应的老师
             $stationteaxherModel = new StationTeacher();
             
             $teacherDatas= $stationteaxherModel->getTeacherData($teacherData,$exam_id);

             foreach ($teacherDatas as $value){

                 if($value ->teacher_type ==2){

                     $teacherData ->sp_teacher = [$value];
                     $teacherData ->teacher = [];
                     
                 }else{
                     $teacherData ->teacher = [$value];
                     $teacherData ->sp_teacher = [];
                 }
             }
         }


         return response()->json(
             $this->success_data($datas, 1, 'success')
         );
     }




//    private function getTeacherList(){
//
//    }
//
//    private function groupTeacherList($teatherList){
//        return  [];
//    }
//
//    private function groupSpTeacherList($teatherList){
//        return  [];
//    }

    /**
     * 保存考官安排数据
     * @param Request $request
     * @return mixed
     */
    public function postInvigilateArrange(Request $request)
    {

        try {
            //验证
            $this->validate($request, [
                'exam_id' => 'required|integer'
            ]);
            //获得exam_id
            $exam_id = $request->input('exam_id');
            $teacherData = $request->input('data');
          
  
            //保存老师的数据
            $stationteaxherModel = new StationTeacher();

            if(!$stationteaxherModel->getsaveteacher($teacherData,$exam_id)){

                        throw new \Exception('保存老师数据失败，请重试！！');
                    
                }else{
                return response()->json(
                    $this->success_data([], 1, 'success')
                );

            }
            
//            return redirect()->route('osce.admin.exam-arrange.getInvigilateArrange', ['id' => $exam_id]);

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据考试项目 获取对应下的考官、SP（接口）
     * @url GET /osce/admin/exam-arrange/invigilates-by-subject
     * @param Request $request
     * @author Zhoufuxiang 2016-04-06
     * @return json
     */
    public function getInvigilatesBySubject(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'subject_id' => 'required|integer',
                'type' => 'required|integer'
            ]);
            $subject_id = intval($request->get('subject_id'));
            $type = intval($request->get('type'));
            $teacherSubject = new TeacherSubject();
            //根据考试项目 获取对应的考官
            $invigilates = $teacherSubject->getTeachers($subject_id, $type);

            return response()->json(
                $this->success_data($invigilates, 1, 'success')
            );

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }


    /**
     * 判定是否是同一个用户
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */
    private function getUserProve($exam, $user)
    {
        //根据考试编号id去查找计划表是否有操作人信息
//        $userId =

    }


    private function timeIndex($data,$time){
        if(array_key_exists(strtotime($time),$data))
        {
            $time   =   strtotime($time)+1;
            return $this->timeIndex($data,date('Y-m-d H:i:s',$time));
        }
        else
        {
            return  $time;
        }
    }
    /**
     * 考场安排，提交保存
     * @param Request $request
     * @author Zhoufuxiang 2016-4-7
     * @throws \Exception
     */
    public function postArrangeSave(Request $request)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try{
            $this->validate($request, [
                'exam_id' => 'required',
            ]);

            $exam_id       = $request->get('exam_id');
            $ExamDraftFlow = new ExamDraftFlow();
            $ExamDraft     = new ExamDraft();
            //获取所有临时数据
            $draftFlows = ExamDraftFlowTemp::where('exam_id', '=', $exam_id)->orderBy('created_at')->get();
            $drafts     = ExamDraftTemp::where('exam_id', '=', $exam_id)->orderBy('created_at')->get();

            //所有临时数据 组合
            $datas = [];
            foreach ($draftFlows as $draftFlow) {
                $datas[strtotime($draftFlow->created_at->format('Y-m-d H:i:s'))] = [
                    'item' => $draftFlow,
                    'is_draft_flow' => 1
                ];
            }

            foreach ($drafts as $draft) {

                $time   =   strtotime($this->timeIndex($datas,$draft->add_time));
                $datas[$time] = [
                    'item' => $draft,
                    'is_draft_flow' => 0
                ];
            }

            ksort($datas);     //数组按时间（键）进行排序

            foreach ($datas as $data) {
                //操作大表
                if ($data['is_draft_flow'] == 1) {
                    if(!$ExamDraftFlow->handleBigData($data)){
                        throw new \Exception('保存失败，请重试！');
                    }

                //操作小表
                } else {
                    if(!$ExamDraft->handleSmallData($data)){
                        throw new \Exception('保存失败，请重试！');
                    }
                }
            }

            //处理 待删除 数据（如：清空临时表数据，删除正式表待删除数据）
            $ExamDraftTempModel= new ExamDraftTemp();
            if (!$ExamDraftTempModel->handleDelDatas($exam_id)){
                throw new \Exception('处理待删除数据失败');
            }

            $connection->commit();
            //返回结果
            return response()->json(
                $this->success_data([], 1, '保存成功！')
            );

        } catch (\Exception $ex){
            $connection->rollBack();
            return response()->json($this->fail($ex));
        }
    }

    /**
     *考试安排数据的回显
     * url:
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */
    public function getExamArrangeData(Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer',
        ]);
        $id = $request->get('exam_id');
        try {
            // 清空临时表数据
            $ExamDraftTempModel= new ExamDraftTemp();
            
            $tempData = $ExamDraftTempModel -> getTempData($id);
            if(!$tempData){
                throw new \Exception('清空数据失败');
            }
           
            //拿到大的考站的数据
            $ExamDraftFlowModel = new ExamDraftFlow();

            $ExamDraftFlowRequest = $ExamDraftFlowModel->getExamDraftFlowData($id);
            //拿到大站的id
            $ExamDraftFlowId = $ExamDraftFlowRequest->pluck('id');
            
            $ExamDraftFlowRequest= $ExamDraftFlowRequest->toArray();

            //拿到小站数据
            $ExamDraftModel = new ExamDraft();
            $ExamDraftRequest = $ExamDraftModel->getExamDraftData($ExamDraftFlowId);
            //将小站数据放到大站下
            foreach ($ExamDraftFlowRequest as &$item){

                foreach ($ExamDraftRequest as $value){

                    if($item['id'] == $value['exam_draft_flow_id']){

                        $item['item'][] = $value;
                    }
                }
            }
            return response()->json(
                $this->success_data($ExamDraftFlowRequest, 1, 'success')
            );

        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }

    }

}