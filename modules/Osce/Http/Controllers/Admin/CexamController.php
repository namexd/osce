<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Entities\User;
use DB;
use Auth;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestLog;
use Modules\Osce\Entities\TestRecord;
use Modules\Osce\Entities\TestStatistics;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\Cexam;
use App\Repositories\Common as importUser;
use Modules\Osce\Entities\AddAllExaminee\AddAllExaminee;

class CexamController extends CommonController
{

    /** 新增考试
     * @method GET
     */

    public function addExame(Request $request)
    {
        $dataArray=$request->only('exam_id','name','tid','start','end','teacher','times','convert');
        $isExam = TestLog::where('exam_id',$dataArray['exam_id'])->first();
        if($isExam){
            return redirect()->back()->withErrors('本场技能考试已存在一场理论考试！');
        }
        $start=$request->get('start');
        $end=$request->get('end');
        $isHas = TestLog::where(function ($query)use ($start ,$end){
            $query->where('start', '<=', $start)
                ->where('end', '>=', $end);
            })
            ->orWhere(function ($query)use ($start,$end){
                $query->where('start', '>=', $start)
                    ->where('end', '<=', $end);
            })
            ->orWhere(function ($query)use ($end){
                $query->where('start', '<=', $end)
                    ->where('end', '>=', $end);
            })
            ->orWhere(function ($query)use ($start){
                $query->where('start', '<=', $start)
                    ->where('end', '>=', $start);
            })->first();
        if(empty($isHas)){
            if($dataArray['convert'] < 1 || $dataArray['convert'] > 100){
                return redirect()->back()->withErrors('参数有误！');
            }
            $addArray = [
                'exam_id'=>$dataArray['exam_id'],
                'name'=>$dataArray['name'],
                'tid' =>$dataArray['tid'],
                'start' =>$dataArray['start'],
                'end' =>$dataArray['end'],
                'teacher' =>$dataArray['teacher'],
                'times' =>$dataArray['times'],
                'convert' =>($dataArray['convert']==100)?1:round($dataArray['convert']/100,2),
                'status' =>0
            ];
            TestLog::create($addArray);
            return redirect()->route('osce.theory.index')->withErrors('1新增成功');
        }else{
            return redirect()->route('osce.theory.index')->withErrors('新增失败，当前考试时间与其他考试时间冲突');
        }
    }
    /** 修改考试时间
     * @method GET
     */
    public function editExam(Request $request)
    {
        $id=$request->get('id');
        $start=$request->get('start');
        $end=$request->get('end');
        //dd($request->all());
        try {
            $isHas = TestLog::where('id','!=',$id)
            ->where(function ($query)use ($start ,$end){
                $query->where(function ($query)use ($start ,$end){
                    $query->where('start', '<=', $start)
                        ->where('end', '>=', $end);
                });
                $query->orWhere(function ($query)use ($start,$end){
                    $query->where('start', '>=', $start)
                        ->where('end', '<=', $end);
                });
                $query->orWhere(function ($query)use ($end){
                    $query->where('start', '<=', $end)
                        ->where('end', '>=', $end);
                });
                $query->orWhere(function ($query)use ($start){
                    $query->where('start', '<=', $start)
                        ->where('end', '>=', $start);
                });
            })->first();
            if(empty($isHas)){
                TestLog::where('id',$id)->update(['start'=>$start,'end'=>$end]);
                return $this->success_data([],1,'修改成功');
                //return redirect()->route('osce.theory.index')->withErrors('1修改成功');
            }else{
                return $this->success_data([],0,'修改失败，当前修改的考试时间与其他考试时间冲突');
                //return redirect()->route('osce.theory.index')->withErrors('修改失败，当前修改的考试时间与其他考试时间冲突');
            }
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }
    }

    /** 查询考试
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchExameInfo(Request $request)
    {
        $log_id=$request->get('testlog_id');
        $result = TestLog::find($log_id);
        if($result){
            //dd($result->times*60);
            $endTime =strtotime(session('enterTime'))+($result->times*60)-time();
            if($endTime<0){
                \Auth::logout();
                return redirect()->back()->withErrors('考试时间已经结束！');
            }
            return view('osce::theory.exam_online', ['data' =>$result,'endtime'=>$endTime]);
        }else{
            return redirect()->back()->withErrors('参数有误！');
        }
    }



    /** 提交答案
     * @method GET
     */

    public function addExameResult(Request $request)
    {
        $dataArray=$request->only('logid','cid','answer','type');
        $dataArray['stuid'] = \Auth::user()->id;
        $dataArray['time'] = time();
        $exam = new Cexam();


        $score = 0;

        $cids    = $dataArray['cid'];
        $answers = $dataArray['answer'];


        $ifadd = $exam->ifadd($dataArray);
        if($ifadd){
            \Auth::logout();
            return view('osce::theory.theory_login')->withErrors('您已提交过了 请勿重复提交');
        }

        //增加到表g_test_statistics
        $dataArray['userid'] =$dataArray['stuid'];
        $dataArray['id'] =$dataArray['logid'];
        $exam-> stunowexam($dataArray);

        //写答案
        for($i=0;$i<count($cids);$i++){
            $addArray = [
                'logid'  =>$dataArray['logid'],
                'stuid'  =>$dataArray['stuid'],
                'cid'  =>$cids[$i],
                'answer'  =>$answers[$i],
                'type'   =>$dataArray['type'][$i]

            ];

            $result= $exam->addexamresult($addArray);


            if($result['code']==1){
                $score+= $exam->objectResult($result);
            }

        }

        //增加折合率
        $testarrs = TestLog::where('id',$dataArray['logid'])->first();
        $convert = $testarrs->convert;
        if(!empty($convert)){
            $score = round($convert*$score,2);
        }

        $dataArray['objective']=$score;

        //数据统计
        $exam->addstatics($dataArray);


        //$info = $this->rmsg($result['code'],$result['msg']);

        \Auth::logout();
        return redirect()->route('osce.theory.login.getIndex')->withErrors('1答卷成功');

    }




    /** 查询某个学生试卷的具体信息
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchExamResult(Request $request)
    {
        $dataArray=$request->only('logid','userid');

        $exam = new Cexam();

        $result= $exam->searchExamDetail($dataArray['logid'],$dataArray['userid']);

        $test = TestLog::find($dataArray['logid']);

        return view('osce::theory.searchexamdetail',['data'=>$result,'test'=>$test]);


    }

    public function MarkingExamResult(Request $request)
    {
        $dataArray=$request->only('logid','userid');

        $exam = new Cexam();

        $result= $exam->searchExamDetail($dataArray['logid'],$dataArray['userid']);
        $test = TestLog::find($dataArray['logid']);

        return view('osce::theory.modifystudentexam',['data'=>$result,'test'=>$test]);


    }


    /** 批改试卷提交
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function modifyExamResult(Request $request)
    {
        $dataArray=$request->only('id','isright','poins','logid','stuid','page');

        $exam = new Cexam();

        $score=0;
        for($i=0;$i<count($dataArray['id']);$i++){

            $score+=$dataArray['poins'][$i];
            $addArray = [
                'id' =>     $dataArray['id'][$i],
                'isright' =>     $dataArray['isright'][$i],
                'poins' =>     $dataArray['poins'][$i],
                'logid' =>     $dataArray['logid'],
            ];
            $result= $exam->updateExamDetail($addArray);

        }

        //增加折合率
        $testarrs = TestLog::where('id',$dataArray['logid'])->first();
        $convert = $testarrs->convert;
        if(!empty($convert)){
            $score = round($convert*$score,2);
        }

        $sysarray['subjective']=$score;
        $sysarray['logid']=$dataArray['logid'];
        $sysarray['stuid']=$dataArray['stuid'];
        //更新统计表里的分数
        $exam ->updatestatics($sysarray);
        return redirect()->route('osce.theory.studentmarking',array('id'=>$dataArray['logid'],'page'=>$dataArray['page']))->withErrors('1批巻成功');

    }


    /**查询学生成绩
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchResultList(Request $request)
    {
        $dataArray=$request->only('userid','department_id','type','usertype','starttime','endtime','page','pagecount');

        $this->gyinput($dataArray);

        $page = $dataArray['page'];

        $pagecount = $dataArray['pagecount'];

        $exam = new Cexam();

        $user =new User();

        $result=[];

        if($dataArray['usertype']==1){

            $result=$exam->searchstudent($dataArray);


        }else if($dataArray['usertype']==2){

            $departArray = $exam->seachuserdeparts($dataArray['userid']);

            $searchnews['starttime']=$dataArray['starttime'];
            $searchnews['endtime']=$dataArray['endtime'];
            $searchnews['type']=$dataArray['type'];

            for($i=0;$i<count($departArray);$i++){

                $searchnews['department_id']=$departArray[$i]->id;

                $searchinfo=$exam-> departscorelist($searchnews);

                for($j=0;$j<count($searchinfo);$j++){

                    $stuname = $user->searchUserName($searchinfo[0]->stuid);

                    $searchinfo[$j]->stuname= $stuname[0]->name;

                    $result[]=$searchinfo;

                }

            }

        }else{


            $result=  $exam->glyscorelist($dataArray);

        }

        $info = $this->paginationWay($result,$page,$pagecount);

        return $info;
    }
    //考生管理
    public function studentList(Request $request){

        try {
            $testId = $request->get('test_id');
            $test = TestLog::find($testId);
            $students = Student::where('test_id',$testId);
            if($request->has('keywords')){
                $keywords = '%'.$request->get('keywords').'%';
                $students->where(function ($query) use($keywords) {
                    $query->orWhere('name', 'like',$keywords)
                        ->orWhere('mobile','like', $keywords)
                        ->orWhere('idcard','like', $keywords)
                        ->orWhere('code', 'like',$keywords);
                });
            }
            $list = $students->paginate(10);
            return view('osce::theory.studentList',['data'=>$list,'test'=>$test]);
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }

    }
    //新增考生
    public function addStudent(Request $request){

        try {
            $testId = $request->get('test_id');
            return view('osce::theory.addstudent',['test_id'=>$testId]);
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }

    }
    //考生新增提交
    public function postAddStudent(Request $request){

        $this   ->  validate($request,[
            'test_id'       =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'mobile'        =>  'required',
            'code'          =>  'required',
            //'images_path'   =>  'required',
            'exam_sequence' =>  'required',
            'grade_class'   =>  'required',
            'teacher_name'  =>  'required'
        ],[
            'name.required'         =>  '姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'code.required'         =>  '学号必填',
            //'images_path.required'  =>  '请上传照片',
            'exam_sequence.required'=>  '准考证号必填',
            'grade_class.required'  =>  '班级必填',
            'teacher_name.required' =>  '班主任姓名必填'
        ]);

        $images  = $request->get('images_path')?$request->get('images_path'):'/images/head.png';  //照片
        //用户数据(姓名,性别,身份证号,手机号,学号,邮箱,照片)
        $userData = $request->only('name','gender','idcard','mobile','code','email');
        $userData['avatar'] = $images[0];      //照片
        //考生数据(姓名,性别,身份证号,手机号,学号,邮箱,备注,准考证号,班级,班主任姓名)
        $examineeData = $request->only('name','idcard','mobile','code','description','exam_sequence','grade_class','teacher_name');
        $examineeData['avator'] = $images[0];  //照片

        try{
            //$connection->beginTransaction();
            //考试id
            $testId = $request->get('test_id');
            DB::transaction(function () use($userData,$examineeData,$testId){

                $operator = Auth::user();
                if (empty($operator)) {
                    throw new \Exception('未找到当前操作人信息');
                }

                //身份证号验证
                Common::checkIdCard($testId, $userData,false);

                //处理考生用户信息（基本信息、角色分配）
                //考生角色ID
                $role_id = config('osce.studentRoleId', 2);
                $user = Common::handleUser($userData, $role_id);
                //$user = $this->handleUser($userData);

                //查询学号是否存在
                $code = Student::where('code', $examineeData['code'])->where('user_id', '<>', $user->id)->first();

                if (!empty($code)) {
                    throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该学号已经有别人使用！');
                }
                //根据用户ID和考试号查找考生
                $student = Student::where('user_id', $user->id)->where('test_id', $testId)->first();

                //存在考生信息,则提示已添加, 否则新增
                if ($student) {
                    throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该考生已经存在，不能再次添加！');

                } else {

                    $examineeData['test_id'] = $testId;
                    $examineeData['user_id'] = $user->id;
                    $examineeData['create_user_id'] = $operator->id;
                    //新增考试对应的考生
                    $student = Student::create($examineeData);
                    if (!$student) {
                        throw new \Exception('新增考生失败！');
                    }
                }
            });
            //$connection->commit();
            return redirect()->route('osce.theory.studentList', ['test_id' => $testId]);
        }catch(\Exception $ex)
        {
            dd($ex);
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
    /*public function getEidtStudent(Request $request){
        $this   ->  validate($request,[
            'id'  =>  'required',
        ]);

        $id =   $request    ->  get('id');
        $student    =   Student::findOrFail($id);

        return view('osce::admin.examManage.examinee_manage_edit', ['item' => $student]);
    }
    public function postEditStudent(Request $request){
        $this   ->  validate($request,[
            'id'            =>  'required',
            'name'          =>  'required',
            'idcard'        =>  'required',
            'code'          =>  'sometimes',
            'gender'        =>  'required',
            'mobile'        =>  'required',
            'description'   =>  'sometimes',
            'images_path'   =>  'required',
            'exam_sequence' =>  'required',
            'grade_class'   =>  'required',
            'teacher_name'  =>  'required'
        ],[
            'name.required'         =>  '姓名必填',
            'idcard.required'       =>  '身份证号必填',
            'mobile.required'       =>  '手机号必填',
            'images_path.required'  =>  '请上传照片',
            'exam_sequence.required'=>  '准考证号必填',
            'grade_class.required'  =>  '班级必填',
            'teacher_name.required' =>  '班主任姓名必填'
        ]);
        $id         =   $request->get('id');
        $student    =   Student::find($id);
        $images     =   $request->get('images_path');   //照片
        //考生数据(姓名,性别,身份证号,手机号,学号,邮箱,备注,准考证号,班级,班主任姓名)
        $data = $request->only('name','idcard','mobile','code','description','exam_sequence','grade_class','teacher_name');
        $data['avator'] = $images[0];  //照片
        $examId = $request->get('exam_id');

        try{
            if($student) {
                //查询学号是否存在
//                $code = Student::where('code', $data['code'])->where('exam_id', $examId)->where('user_id','<>',$student->user_id)->first();
//                if(!empty($code)){
//                    throw new \Exception('该学号已经有别人使用！');
//                }
                //查询手机号码是否已经被别人使用
                $mobile = User::where(['mobile' => $data['mobile']])->where('id','<>',$student->user_id)->first();
                if(!empty($mobile)){
                    throw new \Exception('手机号已经存在，请输入新的手机号');
                }
                //查询身份证是否已经被别人使用
                $f = User::where(['idcard' => $data['idcard']])->where('id','<>',$student->user_id)->first();
                if(!empty($f)){
                    throw new \Exception('身份证已经存在，请输入新的身份证');
                }
                foreach($data as $field => $value) {
                    if(!empty($value)){
                        $student->$field = $value;
                    }
                }

                if($student->save()) {
                    $user   =   $student->userInfo;
                    $user->email  = $request->get('email');
                    $user->gender = $request->get('gender');
                    $user->avatar = $data['avator'];
                    $user->mobile = $data['mobile'];
                    $user->idcard = $data['idcard'];
                    if(!$user->save()) {
                        throw new \Exception('用户信息修改失败');
                    }
                    /*if ($request->get('flag') == 1) {
                        return redirect()->route('osce.admin.exam.getStudentQuery');
                    }/
                    return redirect()->route('osce.theory.studentList',['test_id'=>$student->test_id]);
                } else {
                    throw new \Exception('考生信息修改失败');
                }

            } else {
                throw new \Exception('没有找到该考生');
            }

        } catch(\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }*/
    //删除考生
    public function getDelStudent(Request $request){
        $this   ->  validate($request,[
            'id'  =>  'required',
        ]);
        try {
            $id =   $request    ->  get('id');
            $student    =   Student::find($id);
            if($student){
                $record = TestRecord::where(['stuid'=>$student->user_id,'logid'=>$student->test_id])->first();
                if(empty($record)){
                    $student->delete();
                    return $this->success_data([],1,'删除成功!');
                }else{
                    return $this->success_data([],0,'考生已参与考生，删除失败!');
                }
            }else{
                return $this->success_data([],0,'参数有误!');
            }
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }

    }
    //导入考生
    public function importStudents(Request $request){

        try {
            //
            $testId = $request->get('test_id');
            $sucNum = 0;$exiNum=0;
            $studentArrays=[];
            $data   =  importUser::getExclData($request, 'student');
            $data = array_shift($data);
            $checkUser = new AddAllExaminee;
            $data = $checkUser->judgeTemplate($data);
            $data = $checkUser->fieldsChTOEn($data);
            //dd($data);
            foreach ($data as $key => $studentData){
                //1、数据验证
                //姓名不能为空，为空的跳过
                if (empty(trim($studentData['name']))) {
                    if (!empty($studentData['idcard']) && !empty($studentData['mobile'])) {
                        throw new \Exception('第' .($key+2). '行的姓名不能为空！');
                    }
                    continue;
                }
                //性别处理
                $studentData['gender'] = $studentData['gender']=='男'?1:2;
                //证件类型处理（将中文转换为对应的数字）
                $studentData['idcard_type'] = $checkUser->handleIdcardType($studentData['idcard_type'], $key+2);
                //去掉证件号码中的空格 //小x强转大X
                $studentData['idcard'] = strtoupper(str_replace(' ', '', $studentData['idcard']));
                //查询用户是否已经参加考试
                $user = User::where('username', $studentData['mobile'])->select('id')->first();
                //dd($user->id);
                if ($user) {
                    $student = Student::where('user_id', $user->id)->where('test_id', $testId)->first();
                } else {
                    $student = null;
                }
                //考生存在就跳过
                if (!is_null($student)) {
                    $exiNum++;
                    continue;
                }
                //用户数据
                $userData = [
                    'name'          => $studentData['name'],
                    'gender'        => $studentData['gender'],
                    'idcard_type'   => intval($studentData['idcard_type']),
                    'idcard'        => trim($studentData['idcard']),
                    'mobile'        => trim($studentData['mobile']),
                    'code'          => trim($studentData['code']),
                    'avatar'        => $studentData['avator'],
                    'email'         => $studentData['email']
                ];
                //处理用户数据 TODO：fandian 2016-06-03 18:06
                $role_id  = config('osce.studentRoleId');
                $userData = Common::handleUser($userData, $role_id);

                //考生数据
                $studentArray = [
                    'name'          => $studentData['name'],
                    'idcard'        => trim($studentData['idcard']),
                    'mobile'        => trim($studentData['mobile']),
                    'code'          => trim($studentData['code']),
                    'avator'        => $studentData['avator'],
                    'description'   => $studentData['description'],
                    'exam_sequence' => $studentData['exam_sequence'],
                    'grade_class'   => $studentData['grade_class'],
                    'teacher_name'  => $studentData['teacher_name']
                ];

                $studentArray['test_id'] = $testId;
                $studentArray['user_id'] = $userData->id;
                $studentArray['create_user_id'] = \Auth::id();

                //拼装一个二维数组
                $studentArrays[] = $studentArray;

                //成功的考生数加1
                $sucNum++;
            }//循环结束
            //更新考试的人数
            $total = Student::where('test_id',$testId)->count();
            //$exam->total = $sucNum + $exam->total;
            $total = $sucNum + $total;

            //将拼装好的$studentArrays一次性插入student表
            if (count($studentArrays) != 0) {
                if (!Student::insert($studentArrays)) {
                    throw new \Exception('保存学生时出错！');
                }
            }

            //返回信息
            $message = "成功导入{$sucNum}个学生";
            if ($exiNum) {
                $message .= "，有{$exiNum}个学生已存在";
            }
            if ($exiNum) {
                //throw new \Exception(trim($message, '，'));
                return $this->success_data([],0,trim($message, '，'));
            }
            unset($userData);
//            $connection->commit();
            return $this->success_data([],1,$message);//返回导入成功的个数

        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                //throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该手机号码已经使用，请输入新的手机号');
                return $this->success_data([],0,(empty($key) ? '' : ('第' . $key . '行')) . '该手机号码已经使用，请输入新的手机号');
            }
            throw $ex;
        }
    }



}
