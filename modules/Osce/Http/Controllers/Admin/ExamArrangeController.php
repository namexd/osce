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
use Modules\Osce\Entities\ExamDraftFlowTemp;
use Modules\Osce\Entities\ExamDraftTemp;
use Modules\Osce\Entities\ExamGradation;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;

class ExamArrangeController extends CommonController
{

    /**
     * 新增考试安排的站
     * @url GET /osce/admin/exam-arrange/add-exam-flow
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */
    
    
    
    public function postAddExamFlow(Request $request){
        try{
            $this->validate($request,[
                'exam_id'=>'required',
                'name'=>'required',
                'order'=>'required',
                'exam_gradation_id'=>'sometime'  //阶段
            ]);
            $examId = $request->get('exam_id');
            $name = $request->get('name');
            $order = $request->get('order');
            $examGradationId = $request->get('exam_gradation_id');
            //判断操作人是否一致

            //获取当前操作信息
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('未找到当前操作人信息');
            }
            $data =[
                'exam_id'=>$examId,
                'name'=>$name,
                'order'=>$order,
                'exam_gradation_id'=>$examGradationId,
                'ctrl_type'=>1,
            ];
            //先保存到临时表
            $result = ExamDraftFlowTemp::create($data);
            if($result){
                //新增一条空的考站的子站数据
                $DraftData=[
                    'exam_id'=>$examId,
                    'old_draft_flow_id'=>$result->id,
                    'ctrl_type'=>1,
                    'used'=>1,
                    'user_id'=>$user->id,
                ];
                $DraftResult = ExamDraftTemp::create($DraftData);

                if($DraftResult){
                    return response()->json(
                        $this->success_data(['id'=>$result->id,'draft_id'=>$DraftResult->id], 1, 'success')
                    );
                }else{
                    throw new \Exception('保存临时考站失败');
                }

            }else{
                throw new \Exception('保存临时考站失败');

            }
        }catch (\Exception $ex){
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


    public function postAddExamDraft(Request $request){
        $this->validate($request,[
            'exam_id'=>'required',
            'type'=>'required',

            'subject'   => 'sometime',      //考试项目
            'station'   => 'sometime',      //考站
            'room'      => 'sometime',      //考场
            'chioce'    => 'sometime',      //选考
        ]);

        $type = $request->get('type');
        $examId = $request->get('exam_id');
        $subjectId =$request->get('subject');
        $stationId =$request->get('station');
        $roomId =$request->get('room');

        try{
            //获取当前操作信息
            $user = Auth::user();
            if (empty($user)) {
                throw new \Exception('未找到当前操作人信息');
            }
            $data=[
                'exam_id'=>$examId,
                'old_draft_flow_id'=>$request->get('old_draft_flow_id'),
                'old_draft_id'=>$request->get('old_draft_id'),
                'user_id'=>'',
                'subject_id'=>'',
                'station_id'=>'',
                'room_id'=>'',
                'used'=>0,
                'ctrl_type'=>'',
            ];
            if(!is_null($subjectId)){
                $data['subject_id'] = $subjectId;
            }

            if(!is_null($stationId)){
                $data['station'] = $subjectId;
            }

            if(!is_null($roomId)){
                $data['room'] = $roomId;
            }

            if($type==2){
                $data['ctrl_type'] = $type;
            }

            if($type==3){
                $data['ctrl_type'] = $type;
            }
            if($type==4){
                $data['ctrl_type'] = $type;
            }

            $result = ExamDraftTemp::create($data);
            if(!$result){
                throw new \Exception('保存临时考站数据失败');
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







    //删除站接口
    public function getDelExamFlow(Request $request){
        $this->validate($request,[
            'exam_id'=>'required',
            'id'=>'required',
            'type'=>'required',
        ]);
        $id = $request->get('id');
        $exam_id = $request->get('exam_id');
        $type = $request->get('type');
        try{
            if($type==2){
                //是删除真实表数据就在临时表中记录下该操作
                $data =[
                    'exam_id'=>$exam_id,
                    'type'=>$type,
                    'old_draft_flow_id'=>$id,
                ];
                $result = ExamDraftFlowTemp::create($data);
                if($result){
                    return response()->json(
                        $this->success_data($result->id, 1, '删除成功')
                    );
                }

            }else{
                $result = ExamDraftFlowTemp::find($id);
                $result->old_draft_flow_id = $id;
                $result->type =$type;

                if($result->save()){
                    return response()->json(
                        $this->success_data($result->id, 1, '删除成功')
                    );
                }
            }

        }catch (\Exception $ex){

            return response()->json(
                $this->fail($ex)
            );

        }


    }



    //删除子站
    public function getDelExamDraft(Request $request){
        $this->validate($request,[
            'exam_id'=>'required',
            'id'=>'required',
            'type'=>'required',
        ]);
        $id = $request->get('id');
        $exam_id = $request->get('exam_id');
        $type = $request->get('type');
        try{
            if($type==2){
                //是删除真实表数据就在临时表中记录下该操作
                $data =[
                    'exam_id'=>$exam_id,
                    'type'=>$type,
                    'old_draft_id'=>$id,
                ];
                
                $DraftResult = ExamDraftTemp::create($data);
                if($DraftResult){

                    return response()->json(
                        $this->success_data($DraftResult->id, 1, '删除成功')
                    );
                }

            }else{
                $DraftResult = ExamDraftTemp::find($id);

                $DraftResult->old_draft_id =  $id;

                $DraftResult->type = $type;
                if($DraftResult->save()){
                    return response()->json(
                        $this->success_data($DraftResult->id, 1, '删除成功')
                    );
                }
            }

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
            'id'=>'required',
//            'type'=>'required',
//            'draft_id'=>'required'
        ]);
        $name = $request->get('station_name');
        $id = $request->get('id');

        $roomIdArray = ExamDraftTemp::where('old_draft_flow_id','=',$id)->get()->pluck('room_id')->toArray();
        $roomModel = new Room();
//        $roomData = $roomModel -> showRoomList($keyword = '', $type = '0', $id = '');

        $roomData = $roomModel -> getRoomList($roomIdArray,$name);
        return response()->json(
            $this->success_data($roomData, 1, 'success')
        );
        

    }


    //获取考站接口
    public function getStationList(Request $request){
        $this->validate($request,[
            'station_name'=>'sometimes',
            'id'=>'required',
//            'type'=>'required',
//            'draft_id'=>'required'
        ]);

        $name = $request->get('station_name');
        $id = $request->get('id');


        //查询出已用过的考站
        $stationIdArray = ExamDraftTemp::where('old_draft_flow_id','=',$id)->get()->pluck('station_id')->toArray();
        $stationModel = new Station();
        $stationData = $stationModel -> showList($stationIdArray,$ajax = true,$name);

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
    public function getAllGradations(Request $request){
        try{
            //验证
            $this->validate($request, [
                'exam_id' => 'required|integer',
            ]);
            $exam_id = intval($request->get('exam_id'));
            $data    = ExamGradation::where('exam_id','=',$exam_id)->get();
            return response()->json(
                $this->success_data($data, 1, 'success')
            );

        }catch (\Exception $ex){
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
     * @url GET /osce/admin/exam-arrange/invigilates-by-subject
     * @param Request $request
     * @author Zhoufuxiang 2016-04-06
     * @return json
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


    /**
     * 判定是否是同一个用户
     * @param Request $request
     * @author zhouqiang 2016-04-06
     * @return string
     */
    private  function getUserProve($exam,$user){
        //根据考试编号id去查找计划表是否有操作人信息
//        $userId =

    }


    public function postHandleExamDraft(Request $request){
        $this->validate($request,[
            'id'        => 'sometime',
            'stage'     => 'sometime',      //阶段
            'subject'   => 'sometime',      //考试项目
            'station'   => 'sometime',      //考站
            'room'      => 'sometime',      //考场
            'chioce'    => 'sometime',      //选考
        ]);

        $param = $request->only(['id','stage','subject','station','room','chioce']);



    }





}