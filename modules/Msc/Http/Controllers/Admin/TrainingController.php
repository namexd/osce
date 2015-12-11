<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/11/28
 * Time: 13:59
 */

namespace Modules\Msc\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\ResourcesClassroomPlanGroup;
use Modules\Msc\Entities\ResourcesClassroomPlanTeacher;
use Modules\Msc\Entities\StdGroup;
use Modules\Msc\Entities\Teacher;
use Modules\Msc\Entities\TrainingGroup;
use Modules\Msc\Http\Controllers\MscController;
use Modules\Msc\Repositories\Common as MscCommon;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Msc\Entities\Training;
use Modules\Msc\Entities\Courses;
use Modules\Msc\Entities\ResourcesClassroom;
use Modules\Msc\Entities\TrainingCourse;
use Modules\Msc\Entities\Groups;
use Modules\Msc\Entities\ResourcesClassroomPlan;
use Modules\Msc\Entities\ResourcesClassroomCourses;
use App\Entities\User;
use Modules\Msc\Http\Controllers\MscWeChatController;

class TrainingController extends MscController
{
    public function getTest(){
        return view('msc::admin.trainarrange.train-preview');
    }
    /**
     * 新建培训-表单
     * @method GET /msc/admin/training/add-training
     * @access public
     *
     * @param Request $request get请求<br><br>
     *
     * @return View
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-30 18:16
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddTraining (Request $request)
    {
        return view('msc::admin.trainarrange.add-train');
    }

    /**
     * 新建培训-处理
     * @method POST /msc/admin/training/add-training
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post 参数</b>
     * * string        $name        培训名称
     * * string        $begindate   培训开始时间
     * * string        $endtime     培训结束时间
     *
     * @return json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-28 14:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddTraining (Request $request)
    {
        $this->validate($request,[
            'name'            => 'required|unique:msc_mis.training,name|max:50|min:0', // 唯一
            'begindate'       => 'required|date_format:Y/m/d H:i:s',
            'enddate'         => 'required|date_format:Y/m/d H:i:s',
        ]);
		
        $data = [
            'name'        => urldecode($request->input('name')),
            'total'       => 0,
            'begindate'   => $request->input('begindate'),
            'enddate'     => $request->input('enddate'),
            'description' => '',
        ];

        $training = Training::create($data);
        if (!$training)
        {
            return back()->with('error', '培训创建失败');
        }

        return redirect()->route('msc.training.addTrainingGroup', [$training->id]);
    }

    /**
     * 检查培训名称是否唯一
     * @method GET /msc/admin/training/ajax-checkname/{name}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $name        被检查的培训名称(必须的)
     *
     * @return bool
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-2 16:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getUniqueName ($name)
    {
        $filterName = urldecode(e($name));

        $names = Training::where('name', $filterName)->get();

        return response()->json(
            $names ? false : true
        );
    }

    /**
     * 新建培训分组-表单
     * @method GET /msc/admin/training/add-training-group
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        培训id(必须的)
     *
     * @return response
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-25 18:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddTrainingGroup ($id)
    {
        // 培训基本信息
        $training = Training::findOrFail($id);

        return view('msc::admin.trainarrange.add-group', ['training'=>$training]);
    }

    /**
     * 导入培训人员分组excel
     * @method POST /msc/admin/training/import-training-group
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $training        分组excel文件名(必须的)
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-30 18:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postImportTrainingGroup (Request $request)
    {
        try
        {
            $data = Common::getExclData($request, 'training');

            $groupInfo = array_shift($data);
            $groupInfo = Common::arrayChTOEn($groupInfo, 'msc.importForCnToEn.training_group');			 

            $trainingStuffNum = count($groupInfo); // 培训人数
            $trainingGroupNum = 0; // 培训组数
            $temp = '';
            foreach ($groupInfo as $item)
            {
                if ($temp != $item['group'])
                {
                    $trainingGroupNum++;
                    $temp = $item['group'];
                }
                else
                {
                    continue;
                }
            }

            die(json_encode(['group' => $groupInfo, 'stuNum' => $trainingStuffNum, 'grpNum' => $trainingGroupNum]));
        }
        catch (\Exception $e)
        {
            die($e);
        }
    }

    /**
     * 新建培训分组-处理
     * @method POST /msc/admin/training/add-training-group
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           $training_id                    培训id
     * * int           $training_group_num             培训分组数目
     * * array         $training_group_student         培训人员分组信息
     *
     * @return response
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-11-30 19:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddTrainingGroup (Request $request)
    {
        $this->validate($request,[
            'training_id'            => 'required|integer',
            'training_group_num'     => 'required|integer',
            'training_group_student' => 'required|array',
        ]);

        DB::beginTransaction();
        try
        {
            $trainingId  = $request->input('training_id');
            $groupNum    = $request->input('training_group_num');

            // group id和实际分组编号映射关系 eg:一组的分组id为41，二组的分组id为42
            $stuToGrpMap = [];

            // 添加培训分组
            for ($i=0; $i<$groupNum; $i++)
            {
                $data = [
                    'name'             => '',
                    'detail'           => '',
                    'student_class_id' => 0, // 分组所属班级默认为0
                ];

                $groups = Groups::create($data);
                if (!$groups)
                {
                    throw new \Exception('创建培训分组失败');
                }

                // 建立分组和培训的关联信息
                $_data = [
                    'training_id' => $trainingId,
                    'group_id'    => $groups->id,
                ];
                $trainingGroup = TrainingGroup::create($_data);
                if (!$trainingGroup)
                {
                    throw new \Exception('创建分组和培训关联信息失败');
                }

                $stuToGrpMap[($i+1)] = $groups->id;
            }

            // 写入缓存，最终提交计划时需写入resources_lab_plan_group表
            Cache::put('trainingGroupIds'.Auth::user()->id, $stuToGrpMap, config('session.lifetime'));

            // 添加学生分组信息
            $trainingGroupStudent = $request->input('training_group_student');
            foreach ($trainingGroupStudent as $item) // item是string "group:一,mobile:18200122145"
            {
                if ('' == $item)
                {
                    continue;
                }
                $itemArray       = explode(',', $item);
                $itemGroupArray  = explode(':', $itemArray['0']);
                $itemMobileArray = explode(':', $itemArray['2']);
                $itemGroup       = $itemGroupArray['1'];
                $itemMobile      = $itemMobileArray['1'];

                // 根据学员手机找到其id
                $user = User::where('mobile', $itemMobile)->firstOrFail();

                $data = [
                    'group_id'          => $stuToGrpMap[MscCommon::hanzi2num($itemGroup)],
                    'student_id'        => $user->id,
                ];

                $studentGroup = StdGroup::create($data);
                if (!$studentGroup)
                {
                    throw new Exception('创建学生分组失败');
                }
            }

            // 把实际的培训人数写入培训记录
            $training        = Training::findOrFail($trainingId);
            $training->total = count($trainingGroupStudent);

            $result = $training->save();
            if (!$result)
            {
                throw new \Exception('写入实际培训人数失败');
            }

            // 写入缓存 方便修改时候读取
            Cache::put('trainingGroup'.Auth::user()->id, $trainingGroupStudent, config('session.lifetime'));

            DB::commit();
						

            return redirect()->route('msc.training.addTrainingPlan', [$trainingId]);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return back()->with('error', '提交分组信息失败');
        }
    }

    /**
     * 新建培训安排-表单
     * @method GET /msc/admin/training/add-training-plan
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        培训id(必须的)
     *
     * @return response
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddTrainingPlan ($id)
    {
        // 培训基本信息
        $training = Training::findOrFail($id);		

        return view('msc::admin.trainarrange.add-arrange', ['training'=>$training]);
    }

    /**
     * 导入培训安排excel
     * @method POST /msc/admin/training/import-training-plan
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $training        培训安排excel文件名(必须的)
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 10:27
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postImportTrainingPlan (Request $request)
    {
        try
        {
            $data = Common::getExclData($request, 'training');

            $planInfo = array_shift($data);

            $planInfo = Common::arrayChTOEn($planInfo, 'msc.importForCnToEn.training_plan');

            // 把培训安排信息写入缓存 预览时使用
            Cache::put('trainingPlan'.Auth::user()->id, $planInfo, config('session.lifetime'));
			            
			die(json_encode(['plan' => $planInfo]));
        }
        catch (\Exception $e)
        {        	
            die($e);
        }
    }

    /**
     * 培训预览-表单
     * @method GET /msc/admin/training/add-training-preview
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        培训id(必须的)
     *
     * @return response
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 11:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddTrainingPreview ($id)
    {
        // 培训基本信息
        $training = Training::findOrFail($id);
        // 培训安排信息
        $planInfo = Cache::get('trainingPlan'.Auth::user()->id);
		
		foreach ($planInfo as $key => $item)
		{
				
			$beginTime = $item['begin_dt']->format('Y-m-d H:i');
			$endTime   = $item['end_dt']->format('Y-m-d H:i');
			
			$beginTimeArray = explode(' ', $beginTime);
			$endTimeArray   = explode(' ', $endTime);
			
			$planInfo[$key]['time'] = date('Y-m-d', strtotime($beginTimeArray['0'])).' '.$beginTimeArray['1'].'-'.$endTimeArray['1'];
		}
		
        return view('msc::admin.trainarrange.train-preview',['training'=>$training,'planinfo'=>$planInfo]);
    }
	
	/**
     * 修改分组-表单
     * @method GET /msc/admin/training/edit-training-group
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * 
     *
     * @return View
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-2 11:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditTrainingGroup ($id)
    {        
        return view('msc::admin.trainarrange.look-group', ['id'=>$id]);
    }

    /**
     * 修改分组-表单
     * @method GET /msc/admin/training/edit-training-group-data
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * 
     *
     * @return View
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-2 11:34
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditTrainingGroupData ()
    {
        $groupInfo = Cache::get('trainingGroup'.Auth::user()->id);

        $data = [];
        foreach ($groupInfo as $key => $item)
        {
            $temp = [];
            $itemArray   = explode(',', $item);
            $groupArray  = explode(':', $itemArray['0']);
            $mobileArray = explode(':', $itemArray['2']);
			$nameArray   = explode(':', $itemArray['1']);

            $temp['group']  = $groupArray['1'];
            $temp['mobile'] = $mobileArray['1'];
			$temp['name']   = $nameArray['1'];
            $data[] = $temp;
        }
		
		die(json_encode(['groupinfo'=>$data]));
    }

    /**
     * 修改分组-处理-前提是组数没改变
     * @method POST /msc/admin/training/edit-training-group
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            $id             培训id
     * * array          $list           修改后的分组
     * @return View
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-2 11:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditTrainingGroup (Request $request)
    {
        $this->validate($request,[
            'id'      => 'required|integer',
            'list'    => 'required|array',
        ]);

        $trainingId = $request->input('id');
        $list       = $request->input('list');		

        $trainingGroups = TrainingGroup::where('training_id', $trainingId)->get();
        if (!$trainingGroups)
        {
            throw new \Exception('未找到分组信息');
        }

        $groupIdArray = [];
        foreach ($trainingGroups as $trainingGroup)
        {
            $group = Groups::findOrFail($trainingGroup->group_id);
            $groupIdArray[] = $group->id;
        }
        sort($groupIdArray); // 升序排列


        $totalNum = 0; // 该培训总人数
        DB::beginTransaction();
        foreach ($groupIdArray as $key => $groupId)
        {
            $students = Groups::findOrFail($groupId)->students;
            $totalNum += count($students);

            $studentIds = []; // 该组已经有的学生
            $_studentIds = []; // 该组提交的学生

            foreach ($students as $item)
            {
                $studentIds[] = $item->id;
            }

            //$newList = $list[$key+1]; //该组新提交的数据

            // 新增            			     
			foreach ($list as $stu)
            {
            	$infoArray   = explode(',', $stu);
				//$groupArray  = explode(':', $infoArray['0']);
				//$nameArray   = explode(':', $infoArray['1']);
				$mobileArray = explode(':', $infoArray['2']);
				
                $user = User::where('mobile', $mobileArray['1'])->firstOrFail();
                if (!$user)
                {
                    DB::rollback();
                    throw new \Exception('找不到该学生信息');
                }
                $_studentIds[] = $user->id;

                if (!in_array($user->id, $studentIds))
                {
                    $data = [
                        'group_id'   => $groupId,
                        'student_id' => $user->id,
                    ];
                    $result = StdGroup::create($data);
                    if (!$result)
                    {
                        DB::rollback();
                        throw new \Exception('小组新增学员失败');
                    }
                    $totalNum++;
                }
            }				          

            // 删除
            foreach ($students as $item)
            {
                if (!in_array($item->id, $_studentIds))
                {
                    $result = $item->delete();
                    if (!$result)
                    {
                        DB::rollback();
                        throw new \Exception('小组删除学员失败');
                    }
                    $totalNum--;
                }
            }
        }

        // 更新该培训人数
        $training = Training::findOrFail($trainingId);
        $training->total = $totalNum;
        $result = $training->save();
        if (!$result)
        {
            DB::rollback();
            throw new \Exception('更新培训总人数失败');
        }

        DB::commit();
				
		return redirect()->route('msc.training.addTrainingPreview', [$trainingId]);		
    }

    /**
     * 培训预览-处理
     * @method POST /msc/admin/training/add-training-preview
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $id                      培训id
     * * string        $trainingCourses         培训安排
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 10:31
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddTrainingPreview (Request $request)
    {    	

        $this->validate($request,[
            'id'              => 'required|integer',
            'trainingCourses' => 'required|array',
        ]);

        DB::beginTransaction();
        try
        {
            // 写入课程培训记录表
            $trainingCourses = $request->input('trainingCourse');

            // 发生冲突列表-已经计划好的
            $conflictArray = [];
            // 发生冲突的课程-培训课程
            $_conflictArray = [];
            foreach ($trainingCourses as $trainingCourse)
            {
            	// 字符串分割成数组
            	$itemArray      = explode(',', $trainingCourse);
				$courseArray    = explode(':', $itemArray['1']);
				$addressArray   = explode(':', $itemArray['2']);
				$groupArray     = explode(':', $itemArray['0']);
				$beginTimeArray = explode(':', $itemArray['3']);
				$endTimeArray   = explode(':', $itemArray['4']);
				
				
                // 课程信息
                $course = Courses::where('code', $courseArray['1'])->firstOrFail();
                // 教室信息
                $classroom = ResourcesClassroom::where('code', $addressArray['1'])->firstOrFail();
                // 课程、教室关联关系
                $classroomCourseModel = new ResourcesClassroomCourses();
                $classroomCourse = $classroomCourseModel->where('resources_lab_id', $classroom->id)->where('course_id', $course->id)->firstOrFail();

                // 检查教室计划情况

                $currentDate = date('Y-m-d', strtotime($beginTimeArray['1']));
                $begintime   = date('H:i:s', strtotime($beginTimeArray['1']));
                $endtime     = date('H:i:s', strtotime($endTimeArray['1']));
				/*
                $classroomPlanBuilder = ResourcesClassroomPlan::where('currentdate', $currentDate); // 时间
                $classroomPlanBuilder = $classroomPlanBuilder->where('begintime', '<=', $endtime);
                $classroomPlanBuilder = $classroomPlanBuilder->orWhere('endtime', '>=', $begintime);
                $classroomPlanBuilder = $classroomPlanBuilder->where('resources_lab_course_id', $classroomCourse->id); //教室
                $classroomPlans = $classroomPlanBuilder->whereIn('status', [0, 1])->get(); // 计划状态
				 */
				$classroomPlan  = new ResourcesClassroomPlan();
				$classroomPlans = $classroomPlan->getConflicts($classroomCourse->id, $currentDate, $begintime, $endtime);
                $occupationFlag = $classroomPlans ? true : false;

                // 写入发生冲突的课程安排-已经计划好的
                if ($occupationFlag)
                {
                    foreach ($classroomPlans as $classroomPlan)
                    {
                        // 课程信息
                        $planCourse = Courses::findOrFail($classroomPlan->course_id);
                        // 教室-课程关联表
                        $planClassroomCourse = ResourcesClassroomCourses::findOrFail($classroomPlan->resources_lab_course_id);
                        // 教室信息
                        $planClassroom = ResourcesClassroom::findOrFail($planClassroomCourse->resources_lab_id);

                        $data = [
                            'id'           => $classroomPlan->id,
                            'courseId'     => $planCourse->id,
                            'course'       => $planCourse->name,
                            'address'      => $planClassroom->name,
                            'addressId'    => $planClassroom->id,
                            'date'         => $classroomPlan->currentdate,
                            'begintime'    => $classroomPlan->begintime,
                            'endtime'      => $classroomPlan->endtime,
                        ];

                        $conflictArray[] = $data;
                    }
                }

                // 写入发生冲突的课程安排-培训课程
                $_data = [
                    'group'     => MscCommon::hanzi2num($groupArray['1']),
                    'courseId'  => $course->id,
                    'course'    => $course->name,
                    'address'   => $classroom->name,
                    'date'      => date('Y-m-d', strtotime($beginTimeArray['1'])),
                    'begintime' => date('H:i:s', strtotime($beginTimeArray['1'])),
                    'endtime'   => date('H:i:s', strtotime($endTimeArray['1'])),
                ];
                $_conflictArray[] = $_data;

                $trainingId = $request->input('id');
                $data = [
                    'course_id'              => $course->id,
                    'training_id'            => $trainingId,
                    'resources_lab_id'       => $classroom->id,
                    'begin_dt'               => date('Y-m-d H:i:s', strtotime($beginTimeArray['1'])),
                    'end_dt'                 => date('Y-m-d H:i:s', strtotime($endTimeArray['1'])),
                    'validation_pass'        => $occupationFlag ? 0 : 1,
                ];

                $trainingCourseModel = TrainingCourse::create($data);
                if (!$trainingCourseModel)
                {
                    throw new \Exception('创建课程培训记录信息失败');
                }
            }

            // 冲突的课程写入缓存
            Cache::put('conflictCourseArray'.Auth::user()->id, $conflictArray, config('session.lifetime'));
            Cache::put('conflictTrainingArray'.Auth::user()->id, $_conflictArray, config('session.lifetime'));

            DB::commit();

            if ($conflictArray)
            {
                // 有课程安排冲突
                return response()->json(
                    $this->success_data($conflictArray, 0)
                );
            }
            else
            {
                return response()->json(
                    $this->success_data()
                );
            }
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return response()->json(
                $this->fail($e)
            );
        }
    }

    /**
     * ajax获取和课程相关的教室
     * @method GET /msc/admin/training/ajax-course-classroom
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $id        课程id(必须的)
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 14:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getClassrooms ($id)
    {
        $courseId = intval($id);

        $course = Courses::findOrFail($courseId);
        $classrooms = $course->classrooms;

        $data = [];
        if ($classrooms)
        {
            foreach ($classrooms as $classroom)
            {
                $temp = [];
                $temp['id']   = $classroom->id;
                $temp['name'] = $classroom->name;
                $data[] = $temp;
            }
        }

        return response()->json(
            $this->success_data($data)
        );
    }

    /**
     * ajax获取教室空闲时间
     * @method GET /msc/admin/ajax-classroom-freetime
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $courseId        课程id(必须的)
     * * string        $classroomId     教室id(必须的)
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 15:15
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFreetime ($courseId, $classroomId)
    {
        $courseId    = intval($courseId);
        $classroomId = intval($classroomId);

        $course = Courses::findOrFail($courseId);
        $time = ($course->time_length) * 60; // 单位 s

        $emptyTimeArray = Common::classroomEmptyTime($classroomId, $time);

        return response()->json(
            $this->success_data($emptyTimeArray)
        );
    }

    /**
     * ajax获取冲突的课程
     * @method GET /msc/admin/training/ajax-conflictcourses
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        $type        类型(必须的)-1获取冲突的培训课程，2-获取冲突的已有课程
     *
     * @return Json
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 15:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getConflictCourses ($type)
    {
        if (!in_array($type, [1, 2]))
        {
            throw new \Exception('请求类型不正确');
        }

        if (1 == $type)
        {
            return response()->json(
                Cache::get('conflictTrainingArray'.Auth::user()->id)
            );
        }
        else
        {
            return response()->json(
                Cache::get('conflictCourseArray'.Auth::user()->id)
            );
        }
    }

    /**
     * 最终提交培训安排
     * @method GET /msc/admin/training/add-final-plan
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        $flag_conflict        是否有冲突标志(必须的)1-有冲突 2-无冲突
     * * string        $flag_edit_type       修改培训安排还是修改已有课程安排(必须的) 1-修改培训安排 2-修改已有课程安排
     * * string        $edit_array           课程安排修改数组(必须的)
     * * int           $id                   培训id
     * @return response
     *
     * @version 0.4
     * @author wangjiang <wangjiang@misrobot.com>
     * @date 2015-12-1 16:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddFinalPlan (Request $request)
    {
        $this->validate($request,[
            'flag_conflict'  => 'required|in:1,2',
            'flag_edit_type' => 'required|in:1,2',
            'edit_array'     => 'required|array',
            'id'             => 'required|integer',
        ]);

        $flag_conflict  = $request->input('flag_conflict');
        $flag_edit_type = $request->input('flag_edit_type');
        $trainingId     = intval($request->input('id'));
        DB::beginTransaction();

        if (2 == $flag_conflict)
        {
            // 无冲突
            $planInfo = Cache::get('trainingPlan'.Auth::user()->id);

            foreach ($planInfo as $item)
            {
                $this->insertSingleExcelData($item);
            }

            // 给学生发送微信通知
            $this->sendStuMsg($trainingId);

            DB::commit();
            return back(); // todo 跳转地址
        }
        else
        {
            // 有冲突
            $editArray = $request->input('edit_array');
            if (2 == $flag_edit_type)
            {
                // 修改已有课程安排
                foreach ($editArray as $editItem)
                {
                    // 检查是否有冲突(两个人同时处理冲突时)

                    // 课程、教室关联关系
                    $classroomCourseModel = new ResourcesClassroomCourses();
                    $classroomCourse = $classroomCourseModel->where('resources_lab_id', $editItem['classroomId'])->where('course_id', $editItem['courseId'])->firstOrFail();

                    // 检查教室计划情况
                    $currentDate = $editItem['begin_dt']->format('Y-m-d');
                    $begintime   = $editItem['begin_dt']->format('H:i');
                    $endtime     = $editItem['end_dt']->format('H:i');

                    $classroomPlanBuilder = ResourcesClassroomPlan::where('currentdate', $currentDate); // 时间
                    $classroomPlanBuilder = $classroomPlanBuilder->where('begintime', '<=', $endtime);
                    $classroomPlanBuilder = $classroomPlanBuilder->orWhere('endtime', '>=', $begintime);
                    $classroomPlanBuilder = $classroomPlanBuilder->where('resources_lab_course_id', $classroomCourse->id); //教室
                    $classroomPlans       = $classroomPlanBuilder->whereIn('status', [0, 1])->get(); // 计划状态

                    if ($classroomPlans)
                    {
                        // 两个人同时处理冲突时发生冲突
                        //todo 返回调整页面重新调整
                    }

                    // 无冲突
                    $theClassroomPlan = ResourcesClassroomPlan::findOrFail($editItem['id']);
                    $theClassroomPlan->resources_lab_course_id = $classroomCourse->id;
                    $theClassroomPlan->currentdate             = $currentDate;
                    $theClassroomPlan->begintime               = $begintime;
                    $theClassroomPlan->$endtime                = $endtime;

                    $result = $theClassroomPlan->save();
                    if (!$result)
                    {
                        DB::rollback();
                        throw new \Exception('修改课程安排失败');
                    }

                }

                // excel培训安排写入-没有修改
                $planInfo = Cache::get('trainingPlan'.Auth::user()->id);

                foreach ($planInfo as $item)
                {
                    $this->insertSingleExcelData($item);
                }

                // 给学生发送微信通知
                $this->sendStuMsg($trainingId);

                DB::commit();
                return back(); // todo 修改成功页面跳转
            }
            else
            {
                // 修改培训安排
                $planInfo = Cache::get('trainingPlan'.Auth::user()->id);

                foreach ($planInfo as $key => $item)
                {
                    $_course = Courses::where('code', $item['course_code'])->firstOrFail();
                    foreach ($editArray as $editArrayItem)
                    {
                        // 第几组 哪门课程
                        if ($editArrayItem['courseId'] == $_course['id'] && $editArrayItem['group'] == MscCommon::hanzi2num($item['group']))
                        {
                            $course_ = Courses::findOrFail($editArrayItem['courseId']);
                            // 执行修改
                            $planInfo[$key]['course_code'] = $course_->code;
                            $planInfo[$key]['begin_dt']    = $editArrayItem['begin_dt'];
                            $planInfo[$key]['end_dt']      = $editArrayItem['end_dt'];
                        }
                        else
                        {
                            continue;
                        }
                    }

                    // excel培训安排写入-修改后
                    $this->insertSingleExcelData($item);
                }

                // 给学生发送微信通知
                $this->sendStuMsg($trainingId);

                DB::commit();
                return back(); // todo 修改成功页面跳转
            }
        }
    }
    private function insertSingleExcelData ($item)
    {
        // 课程信息
        $course = Courses::where('code', $item['course_code'])->firstOrFail();
        // 教室信息
        $classroom = ResourcesClassroom::where('code', $item['address_code'])->firstOrFail();
        // 教室 课程关联信息
        $classroomCourseModel = new ResourcesClassroomCourses();
        $classroomCourse = $classroomCourseModel->where('resources_lab_id', $classroom->id)->where('course_id', $course->id)->firstOrFail();

        // 写入resources_lab_plan表
        $data = [
            'resources_lab_course_id' => $classroomCourse->id,
            'course_id'                     => $course->id,
            'currentdate'                   => $item['begin_dt']->format('Y-m-d'),
            'begintime'                     => $item['begin_dt']->format('H:i'),
            'endtime'                       => $item['end_dt']->format('H:i'),
            'type'                          => 2, // 培训
            'status'                        => 0, // 已计划未使用
        ];
        $classroomPlan = ResourcesClassroomPlan::create($data);
        if (!$classroomPlan)
        {
            throw new \Exception('创建教室使用计划失败');
        }

        // 写入resources_lab_plan_group表
        $stuToGrpMap = Cache::get('trainingGroupIds'.Auth::user()->id);
        $data = [
            'resources_lab_plan_id' => $classroomPlan->id,
            'student_class_id'            => 0,
            'student_group_id'            => $stuToGrpMap[MscCommon::hanzi2num($item['group'])],
        ];
        $classroomPlanGroup = ResourcesClassroomPlanGroup::create($data);
        if (!$classroomPlanGroup)
        {
            DB::rollback();
            throw new \Exception('创建教室使用计划分组失败');
        }

        // 上课老师信息
        $teacher = Teacher::where('code', $item['teacher_code'])->firstOrFail();
        // 写入resources_lab_plan_teacher表
        $data = [
            'resources_lab_plan_id' => $classroomPlan->id,
            'teacher_id' => $teacher->id,
        ];
        $classroomPlanTeacher = ResourcesClassroomPlanTeacher::create($data);
        if (!$classroomPlanTeacher)
        {
            DB::rollback();
            throw new \Exception('创建教室使用计划教课老师失败');
        }

        // 给老师发送微信上课通知
        $mscWeChatController = new MscWeChatController();

        $user = User::findOrFail($teacher->id);
        $address = ResourcesClassroom::where('code', $item['address_code'])->firstOrFail();
        $course  = Courses::where('code', $item['course_code'])->firstOrFail();

        $msg = $user->name.'老师您好！您于'.$item['begin_dt'].'到'.$item['end_dt'].'期间在'.$address->name.'有一堂'.$course->name.'课。';
        $mscWeChatController->sendMsg($msg, $user->openid);
    }
    private function sendStuMsg ($trainingId)
    {
        // 该培训有哪些分组
        $trainingGroups = TrainingGroup::where('training_id', $trainingId)->get();
        if (!$trainingGroups)
        {
            DB::rollback();
            throw new \Exception('未查询到该培训的分组信息');
        }

        $mscWeChatController = new MscWeChatController();
        foreach ($trainingGroups as $trainingGroup)
        {
            $group = Groups::findOrFail($trainingGroup->group_id);

            // 该分组对应的安排
            $classroomPlanGroups = ResourcesClassroomPlanGroup::where('student_group_id', $group->id)->get();
            if (!$classroomPlanGroups)
            {
                DB::rollback();
                throw new \Exception('未查到和该分组相关的安排');
            }

            /*
            foreach ($classroomPlanGroups as $classroomPlanGroup)
            {
                $classroomPlan = ResourcesClassroomPlan::findOrFail($classroomPlanGroup->resources_lab_plan_id);
                // 分别给学生发微信通知
                foreach ($group->students as $student)
                {
                    $openId     = $student->userInfo->openid;
                    $classroom = $classroomPlan->classroomCourses->classroom;
                    $course    = $classroomPlan->course;

                    $msg = '你于'.$classroomPlan->currentdate.' '.$classroomPlan->begintime.'至'.$classroomPlan->currentdate.' '.$classroomPlan->endtime.'期间在'.$classroom->name.'有一堂'.$course->name.'课。';
                    $mscWeChatController->sendMsg($msg, $openId); // 对于一个学生来讲，有多个安排则发送多条信息
                }
            }
            */

            foreach ($group->students as $student)
            {
                $msg = ''; // 要发送的微信通知消息
                foreach ($classroomPlanGroups as $classroomPlanGroup)
                {
                    $classroomPlan = ResourcesClassroomPlan::findOrFail($classroomPlanGroup->resources_lab_plan_id);

                    $openId     = $student->userInfo->openid;
                    $classroom = $classroomPlan->classroomCourses->classroom;
                    $course    = $classroomPlan->course;

                    $msg .= '你于'.$classroomPlan->currentdate.' '.$classroomPlan->begintime.'至'.$classroomPlan->currentdate.' '.$classroomPlan->endtime.'期间在'.$classroom->name.'有一堂'.$course->name.'课。';
                }
                $mscWeChatController->sendMsg($msg, $openId); // 对于一个学生来讲，有多个安排则发送多条信息
            }

        }
    }

}