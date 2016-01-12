<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/11
 * Time: 10:57
 */

namespace Modules\Osce\Entities;

use Illuminate\Database\Eloquent\Collection;
use Modules\Osce\Entities\CommonModel;

class ExamPlan extends CommonModel
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_plan';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=	['exam_id','exam_screening_id','student_id','station_id','room_id','begin_dt','end_dt','status','created_user_id'];

    //考生池
    protected   $allSudent      =   [];
    //考场考生池
    protected   $roomSudent     =   [];
    //学生时间轴
    protected $studentTimeList  =   [];
    //考场队列
    protected $roomQueue        =   [];
    //考场下考站数量
    protected $roomStationNum   =   [];
    //考站队列
    protected $stationQueue     =   [];
    //已结束考生
    protected $overStudent      =   [];

    protected $lastGroup        =   false;

    protected $lastGroupList    =   [];

    protected $stepLastStatus   =   false;

    public function IntelligenceEaxmPlan($exam){
        $studentList   =   $this   ->  getExamStudent($exam);
//        `sequence_cate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '排序方式',//随机还是顺序
//        `sequence_mode` tinyint(2) NOT NULL DEFAULT '1' COMMENT '考试排序模式',//考站还是考场
        $this   ->  initStudentTimeList($studentList);
        $this   ->  initAllSudent($studentList);
        $this   ->  indexArrange($exam);

    }
    /*
     * 获取报考学生
     */
    public function getExamStudent($exam){
        return  $students   =   $exam   ->  students;
    }

    /*
     * 顺序排考
     */
    public function indexArrange($exam){
    //获取考站顺序
        //获取考站流程节点清单
        $flows  =   $this   ->  getExamFlow($exam);
        $flowsIndex         =   $this   ->  groupFlowByRoom($flows);

        $this   ->  setLastFlow($flowsIndex);

        //初始化考场考生队列
        $this   ->  initRoomQueue($exam);
        //初始化房间考生池
        $this   ->  initRoomSudents();

        //节点按照顺序分组
        if($exam->sequence_mode==1)
        {
            $flowsArray =   array_shift($flowsIndex);
            foreach($flowsArray as $flow)
            {
                $this   ->  putStudentToFirstRoomQueue($flow->room,$this->allSudent);
                //$this   ->  delStudentFormPond();
            }
            while(!$this->lastGroup)
            {
                $preStudentsList    =   $this   ->  stepExam($flowsIndex);
                //var_dump($this->lastGroup);
                $this   ->  setOverStudent($preStudentsList);
            }
        }
        else
        {
            $flowsIndex =   $this   ->  groupFlowByStation($flows);
        }
    }

    public function setOverStudent($preStudentsList){
        $overStudents        =   $this   ->  overStudent;
        foreach($preStudentsList as $student)
        {
            $overStudents[$student->id] =   $student;
        }
        $this   ->  overStudent =   $overStudents;
        return $this;
    }
    /*
     * 随机排考
     */
    public function randArrange(){
        //获取空闲教室清单

    }

    /*
     * 获取考试所有流程节点
     */
    public function getExamFlow($exam){
        return $exam    ->  flows;
    }
    /*
     *  根据考场分组流程
     */
    public function groupFlowByRoom($flows){
        $group                      =   [];
        foreach($flows as $flow)
        {
            $examFlowRoomRelation       =   $flow   ->  examFlowRoomRelation;

            if(is_null($examFlowRoomRelation->  serialnumber))
            {
                throw new \Exception('序号数据错误');
            }
            $group[$examFlowRoomRelation->  serialnumber][]=$examFlowRoomRelation;
        }

        ksort($group);
        return $group;
    }
    /*
     * 根据考站分组流程
     */
    public function groupFlowByStation($flows){

    }

    /*
     * 初始化 学生开考相对时间
     */
    protected function initStudentTimeList($studentList){
        $studentTimeList    =   [];
        foreach($studentList as $student)
        {
            $studentTimeList[$student->id]  =   0;
        }
        $this   ->  studentTimeList =   $studentTimeList;
        return $this;
    }
    /*
     *
     *（重要）
     *
     *
     * 为考场 - 随机 方式 初始化 考场 池
     */
    protected  function initRoomQueue($exam){
        $roomQueue              =   [];
        $roomList               =   $this   ->  getExamRoomList($exam->id);
        foreach($roomList as $room)
        {
            //初始化考场下考站数量
            $this   ->initRoomStationNum($room->id,count($room->    room    ->stations));
            //初始化考场下考站考生队列
            $roomQueue[$room->id]    =  [];
        }

        $this   ->  roomQueue   =   $roomQueue;
        return $this;
    }

    /*
     * 初始化考站队列数据
     */
    protected  function initStationQueue($exam){

    }

    protected function initRoomStationNum($roomId,$num){
        $data           =   $this   ->  roomStationNum;
        $data[$roomId]  =   $num;
        $this   ->  roomStationNum  =   $data;
        return $this;
    }

    /*
     * 初始化考试考生池
     */
    protected function initAllSudent($studentList){
        $data   =   [];
        foreach($studentList as $student)
        {
            $data[$student->id] =   $student;
        }
        $this   -> allSudent    =   $data;
        return $this;
    }

    /*
     * 初始化考场考生池
     */
    protected function initRoomSudents($roomList=[],$studentList=[]){
        //学生列表
        if(empty($studentList))
        {
            $data    =   $this   ->  allSudent;
        }
        else
        {
            $data   =   [];
            foreach($studentList as $student)
            {
                $data[$student->id] =   $student;
            }
        }

        //房间列表
        if(empty($roomList))
        {
            $roomList    =   $this   ->  roomQueue;
        }
        $list   =  [];

        foreach($roomList as $roomId=>$item)
        {
            if(!is_array($item))
            {
                $roomId     =   $item->id;
            }
            $list[$roomId]  =   $data;
        }
        $this   -> roomSudent    =   $list;
        return $this;
    }

    protected function getExamRoomList($exam_id){
        return  ExamRoom::where('exam_id','=',$exam_id)->get();
    }

    /*
     * 随机从集合中 获取一个元素
     */
    protected function getRandItem($collect){
        $collect    =   empty($collect)? []:$collect;
        $num    =   count($collect);
        $index  =   rand(1,$num)-1;
        $item   =   null;
        //dd($collect);

        foreach($collect as $key    =>  $item)
        {
            if($key===$index)
            {
                return $item;
            }
        }

        return $item;
    }

    /*
     * 为考站获取查找是需要数量的学生
     */
    public function getStudentsForRoom($room){
        $roomStudentPond    =   $this   ->  roomSudent[$room->id];
        $num            =   $this   ->  roomStationNum[$room->id];
        if($num<0)
        {
            throw new \Exception('考站数量不对');
        }
        //如果候考池人员小于 一次需进场的人数,并且不是最后一组，本次排列0个人，轮下一波；
        if($this->lastGroup||count($roomStudentPond)<$num)
        {
            return  [];
        }
        $thisGroup  =   [];
        for($i=0;$i<$num;$i++)
        {
            $thisGroup[]    =   $this->getRandItem($roomStudentPond);
        }
        return $thisGroup;
    }

    /*
     * 为考场安排一波学生
     */
    public function arrangeStudentForRoom($room){
        //选取学生
        $studentChoosed     =   $this   ->  getStudentsForRoom($room);
        //将考生从该房间被选池中删除
        $this               ->  delStudentFormPond($studentChoosed,$room);
        return  $studentChoosed;
    }

    /*
     * 从房间学生池中删除已选择学生
     */
    public function delStudentFormPond($studentChoosed,$room){
        $idlist =   array_pluck($studentChoosed,'id');
        $roomStudentPondList    =   $this   ->  roomSudent;
        $roomStudentPond    =   $roomStudentPondList[$room->id];
        foreach($idlist as $id)
        {
            unset($roomStudentPond[$id]);
        }
        $roomStudentPondList[$room->id] =   $roomStudentPond;
        return $this;
    }

    /**
     * 把学生放进 考场 队列
     */
    public function putStudentToRoomQueue($student,$room){

    }

    public function stepExam($flows){
        if(!empty($flows))
        {
            //获取当前流程
            $flow  =   array_shift($flows);
            $room       =   $this   ->  getRandItem($flow);
            $chooseList =   $this   ->  roomSudent[$room->id];
            //获取本次考场新增的安排考生
            $preStudentsList        =   $this   ->  arrangeStudentForRoom($room);
            //判断当前流程是否为 当次考试最后一个考场
            if($this  ->  isLastStep($flow))
            {
                //dd($this->roomQueue);
                $this   ->lastGroupCheck($chooseList);
            }
            //dd($this->roomQueue);
            //$this   ->  stepExam($flows);

            return  $preStudentsList;

        }
        else
        {
            return '';
        }
    }

    public function isLastStep($flows){
        $lastGroupList          =   $this   ->  lastGroupList;
        $lastIds                =   array_pluck($lastGroupList,'serialnumber');

        foreach($flows  as $flow)
        {
            if(in_array($flow->serialnumber,$lastIds))
            {
                return true;
            }
        }
        return false;
    }
    /**
     * 给第一个考场添加候考队列
     */
    public function putStudentToFirstRoomQueue($room,$allSudent=[]){
        if(empty($allSudent))
        {
            $allSudent  =   $this->allSudent;
        }
        return  $this->addRoomQueue($room,$allSudent);
    }

    /*
     * 模拟添加候考队列
     */
    public function addRoomQueue($room,$students){
        $roomQueue  =   $this   ->  roomQueue;
        $roomQueue[$room->id]   =   $students;
        $this   ->  roomQueue   =   $roomQueue;
        return $this;
    }

    public function lastGroupCheck($thisGroup){
        $overStudent    =   $this   ->  overStudent;
        $allSudent      =   $this   -> allSudent;
        $overIds        =   array_pluck($overStudent,'id');
        $allIds         =   array_pluck($allSudent,'id');
        $thisIds        =   array_pluck($thisGroup,'id');

        foreach($thisIds as $id)
        {
            $overIds[]    = $id;
        }
        if(sort($allIds)===sort($overIds))
        {
            $this->lastGroup=true;
        }
    }

    public function setLastFlow($flowsIndex){
        $last   =   array_pop($flowsIndex);
        $this   ->  lastGroupList   =   $last;
        return $this;
    }
}