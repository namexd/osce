<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

class BasicDataTest extends TestCase
{
    private function getRandStr($lenth){
        $str    =   '';
        for($i=1;$i<=$lenth;$i++)
        {
            $str.=rand(0,9);
        }
        return $str;
    }
    //获取随机老师
    private function getRandTeacher(){
        $list   =   \Modules\Msc\Entities\Teacher::where('id','>',48)->get();
        return $this->getRandItem($list);
    }
    private function getTimeList(){
        return  [
            30,45,60,120
        ];
    }
    public function getGrade(){
        $config =   [
            '2015',
            '2014',
            '2013',
            '2012',
        ];
        return $this->getRandItem($config);
    }
    private function getTimeToTime(){
        return  [
            '08:00-09:00',
            '10:00-11:00',
            '12:00-13:00',
            '14:00-17:00',
            '17:00-18:00',
        ];
    }
    //获取随机学生
    private function getRandStudent(){
        $list   =   \Modules\Msc\Entities\Student::where('id','>',48)->get();
        return $this->getRandItem($list);
    }
	public function testFullUserData(){
		$studentList    =   \App\Entities\User::where('name','like','%学生%')->get();
		$teacherList    =   \App\Entities\User::where('name','like','%教师%')->get();
//        foreach($studentList as $student)
//        {
//            $codeInfo   =   explode('测试学生',$student->name);
//            $data   =   [
//                'id'          =>  $student->id,
//                'name'          =>  $student->name,
//                'code'          =>  $codeInfo[1],
//                'grade'         =>  $this->getGrade(),
//                'professional'  =>  1,
//                'student_type'  =>  1,
//                'validated'  =>  1,
//            ];
//            \Modules\Msc\Entities\Student::firstOrCreate($data);
//        }
//        foreach($teacherList as $teacher)
//        {
//            $codeInfo   =   explode('测试教师',$teacher->name);
//            $data   =   [
//                'id'          =>  $teacher->id,
//                'name'          =>  $teacher->name,
//                'code'          =>  $codeInfo[1],
//                'teacher_dept'  =>  1,
//                'validated'  =>  1,
//            ];
//            \Modules\Msc\Entities\Teacher::firstOrCreate($data);
//        }
    }
    //新增课程
    public function testAddCourses(){
        $code   =   $this->getRandStr(6);
        $data   =   [
            'name'  =>  '测试课程'.$this->getRandStr(6),
            'code'  =>  $code,
            'detail'=>  '描述测试'.$this->getRandStr(6),
            'max_persons'=>rand(30,50),
            'time_length'=>$this->getRandItem($this->getTimeList())
        ];
        $has    =   \Modules\Msc\Entities\Courses::where('code','=',$code)->count();
        if($has==0)
        {
            \Modules\Msc\Entities\Courses::create($data);
        }
    }
    //新增教室
    public function testAddLab(){
        $code   =   $this->getRandStr(6);
        $teacher    =   $this   ->  getRandTeacher();
        $teacherUserInfo   =   \App\Entities\User::find($teacher  ->  id);
        $data   =   [
            'name'  => '测试教室'.$this->getRandStr(6),
            'code'  =>  $code,
            'location'  => '测试地址'.$code,
            'begintime'  =>  '08:00',
            'endtime'  =>  '22:00',
            'opened'  =>  rand(0,2),
            'manager_id'  =>  $teacher  ->  id,
            'manager_name'  =>  $teacher  ->  id,
            'manager_mobile'  =>  $teacherUserInfo  ->mobile,
            'detail'  =>    '测试地址'.$this->getRandStr(6),
            'status'  =>  1,
        ];
        $ResourcesClassroom =   new \Modules\Msc\Entities\ResourcesClassroom();
        $ResourcesClassroom->create($data);
    }
    public function testAddToolsCate(){
        $user   =   $this   ->  getRandTeacher();
        $userOb     =   \App\Entities\User::find($user->id);
        $data   =   [
            'repeat_max'    =>  0,
            'pid'           =>  0,
            'name'          =>  '测试分类'.$this->getRandStr(6),
            'manager_id'    =>  $user   ->  id,
            'manager_name'  =>  $user   ->  name,
            'manager_mobile'=>  $userOb   ->  mobile,
            'location'      => '测试地址'.$this->getRandStr(6),
            'detail'        => '测试描述'.$this->getRandStr(6),
            'loan_days'     =>  0
        ];
        \Modules\Msc\Entities\ResourcesToolsCate::create($data);
    }
    //新增外借设备
    public function testAddTools(){
        $user   =   $this   ->  getRandTeacher();
        $userOb     =   \App\Entities\User::find($user->id);
        $cateList   =   \Modules\Msc\Entities\ResourcesToolsCate::get();
        $cate   =   $this-> getRandItem($cateList);
        $data   =   [
            'repeat_max'    =>  0,
            'name'          =>  '测试外借设备'.$this->getRandStr(6),
            'cate_id'       =>  $cate   ->  id,
            'code'          =>  [
                $this->getRandStr(6),$this->getRandStr(6),
            ],
            'manager_id'    =>  $user   ->  id,
            'manager_name'  =>  $user   ->  name,
            'manager_mobile'=>  $userOb   ->  mobile,
            'location'      =>  '测试地址'.$this->getRandStr(6),
            'detail'        => '测试描述'.$this->getRandStr(6),
            'loan_days'     => 0,
            'resources_type'     => 'TOOLS',
        ];
        $response   =   $this   ->withSession(['openid'=>$userOb->openid])
            ->actingAs($userOb)
            ->action('post','\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddResources','',$data);
    }
    //新增开放实验室
    public function testAddOpenDeviceCate(){
        $name   = '测试开放设备类别'.$this->getRandStr(6);
        $teacher   = $this->getRandTeacher();
        $data   =   [
            'pid'           =>  0,
            'name'          => $name,
            'manager_id'    => $teacher->id,
            'manager_name'  => $teacher->name,
            'manager_mobile'=> $teacher->userInfo->mobile,
            'location'      => '新八教',
            'detail'        => '测试描述'.$this->getRandStr(6),
        ];
        \Modules\Msc\Entities\ResourcesDeviceCate::firstOrCreate($data);
    }

    //新增开放设备
    public function testAddOpenDevice(){
        $ResourcesDeviceCate   =   new \Modules\Msc\Entities\ResourcesDeviceCate();
        $cateList   =   $ResourcesDeviceCate->get();

        $cate       =   $this->getRandItem($cateList);
        $ResourcesClassroom    =   new \Modules\Msc\Entities\ResourcesClassroom();

        $labList   =     $ResourcesClassroom->where('opened','=',2)->get();
        $lab       =   $this->getRandItem($labList);

        $data   =   [
            'resources_lab_id'  =>  $lab    ->  id,
            'name'              =>  '测试开放设备'.$this->getRandStr(6),
            'code'              =>  $this->getRandStr(6),
            'resources_device_cate_id'  =>  $cate->id,
            'max_use_time'      =>  rand(30,120),
            'warning'           =>  '',
            'detail'            =>  '测试描述'.$this->getRandStr(6),
            'status'            =>   1
        ];
        $ResourcesDevice    =   new \Modules\Msc\Entities\ResourcesDevice();
        $ResourcesDevice    ->firstOrCreate($data);
    }

}
