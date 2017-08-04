<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;


use Illuminate\Support\Facades\DB;
use Auth;

class Cexam extends CommonModel
{
    protected $connection = 'osce_mis';
    public $timestamps=false;
    public $logdata;

    //查询科室考试的列表
    public function searchscorelist($data)
    {
        if($data['usertype']==2){

            $search = DB::table('g_test_log')
                ->where('g_test_log.teacher',$data['id'])
                ->join('g_department','g_department.id','=','g_test_log.department_id')
                ->join('g_user','g_user.id','=','g_test_log.teacher')
                ->join('g_test','g_test.id','=','g_test_log.tid')
                ->where('g_test_log.ifshow',0)
                ->select('g_test_log.*','g_department.name as departname','g_user.name as teachername','g_test.name as examname')
                ->get();
        } else{
            $search = DB::table('g_test_log')
                ->join('g_department','g_department.id','=','g_test_log.department_id')
                ->where('g_test_log.department_id',$data['department_id'])
                ->join('g_user','g_user.id','=','g_test_log.teacher')
                ->join('g_test','g_test.id','=','g_test_log.tid')
                ->where('g_test_log.type',$data['type'])
                ->select('g_test_log.*','g_department.name as departname','g_user.name as teachername','g_test.name as examname')
                ->orderBy('g_test_log.id');
            if($data['type']!=2){
                $search=$search ->where('g_test_log.status',0);
            }

            $search=$search ->get();

        }

        return $search;

    }

    //查询科室考试的列表
    public function searchdepartexamlist($data)
    {
        $search = DB::table('g_test_log')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->join('g_classroom','g_classroom.id','=','g_test_log.classroom')
            ->join('g_user','g_user.id','=','g_test_log.teacher')
            ->join('g_test','g_test.id','=','g_test_log.tid')
            ->where('g_department.teacher_id',$data['id'])
            ->orderBy('g_test_log.end','desc')
            ->select('g_test_log.*','g_department.name as departname','g_user.name as teachername','g_test.name as examname','g_classroom.name as address')
            ->get();


        return $search;

    }


    //查询当前的考试信息
    public function searchModelExamNews($data)
    {

        $builder = DB::table('g_sdcycle')
            ->join('g_user','g_user.id','=','g_sdcycle.stuid')
            ->where('g_sdcycle.stuid',$data['userid'])
            ->where('g_sdcycle.plan_starttime','<',time())
            ->where('g_sdcycle.endtime',0)
            ->select('g_sdcycle.plan_starttime','g_sdcycle.department_id')
            ->select('g_sdcycle.department_id','g_sdcycle.stuid')
            ->first();

        if(!$builder){
            $result['code']=2;
            $result['msg']='当前时间此用户暂无轮转的科室';
            $result['data']=[];
            return $result;
        }
        $this->logdata = $data;


        $search = DB::table('g_test_log')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->join('g_user','g_user.id','=','g_test_log.teacher')
            ->join('g_classroom','g_classroom.id','=','g_test_log.classroom')
            ->join('g_test','g_test.id','=','g_test_log.tid')
            ->leftjoin('g_test_statistics', function ($leftjoin) {
                $leftjoin->on('g_test_statistics.logid', '=', 'g_test_log.id')
                    ->where('g_test_statistics.stuid', '=',$this->logdata['userid']);
            })
            ->where('g_test_log.department_id',$builder->department_id)
            ->where('g_test_log.end','>',time())
            ->select('g_test_log.*','g_department.name as departname','g_test_statistics.ifexam','g_user.name as teachername','g_test.name as examname','g_classroom.name as address')
            ->orderBy('g_test_log.start')
            ->first();



        if($search){

            $search->nowtime = time();

            if($search->ifexam==2){

                $result['code']=5;
                $result['msg']='此学生正在考试';
                $result['data']=$search;
                return $result;

            }elseif($search->ifexam==3){

                $result['code']=6;
                $result['msg']='您已经参加过考试';
                $result['data']=[];
                return $result;
            }elseif(!is_null($search->ifexam)){

                $result['code']=3;
                $result['msg']='老师未确认';
                $result['data']=[];
            }


            $result['code']=1;
            $result['msg']='查询成功';
            $result['data']=$search;

            return $result;
        }

        $result['code']=4;
        $result['msg']='此学生暂无可以参加的考试';
        $result['data']=[];

        return $result;


    }

    //查询科室考试的内容
    public function searchscorequestion($data)
    {

        $builder = DB::table('test_content')
            ->join('g_test','g_test.id','=','test_content.tid')
            ->join('g_test_log','g_test_log.tid','=','g_test.id')
            ->join('g_test_content','g_test_content.id','=','test_content.cid')
            ->where('g_test_log.id',$data['id'])
            ->select('g_test_content.id','g_test_content.type','g_test_content.poins as score','g_test_content.question','g_test_content.content','g_test_content.pbase','g_test_content.source','g_test_content.times','g_test_content.base','g_test_content.degree','g_test_content.separate','g_test_log.id as logid')
            ->orderBy('g_test_content.type')
            ->get();

        return $builder;
    }

    //查询科室考试的内容
    public function searchscoreinfos($test_id)
    {

        $builder = DB::table('g_test_content')
            ->join('g_test','g_test.id','=','test_content.tid')
            ->join('g_test_log','g_test_log.tid','=','g_test.id')
            ->join('g_test_content','g_test_content.id','=','g_test_content.cid')
            ->where('g_test_log.id',$test_id)
            ->select('g_test_content.*','g_test_log.id as logid')
            ->orderBy('g_test_content.type')
            ->get();

        return $builder;
    }

    //更新学生的考试状态
    public function modifyuserexamstatus($data)
    {

        $builder=  DB::table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['logid'])
            ->where('g_test_statistics.stuid',$data['id'])
            ->first();


        if($builder){

            $result['code']=0;
            $result['msg']='已经确认过了';
            return $result;

        }

        DB::table('g_test_statistics')
            ->insertGetId([
                'logid' =>  $data['logid'],
                'stuid' =>  $data['id'],
                'ifexam' =>  1,
            ]);

        $result['code']=1;
        $result['msg']='确认成功';

        return $result;
    }


    //更新学生的答题状态
    public function stunowexam($data)
    {
        $connection = DB::connection($this->connection);
        $connection->table('g_test_statistics')->insert([
        'logid' => $data['id'],
        'stuid' => $data['userid'],
    ]);
    }

    //查询学生的考试状态
    public function searchuserexamstatus($data)
    {
        $connection = DB::connection($this->connection);
        $builder = $connection->table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['id'])
            ->where('g_test_statistics.stuid',$data['userid'])
            ->where('g_test_statistics.ifexam','>',0)
            ->get();

        return $builder;

    }

    //更新学生的考试状态
    public function addexamresult($data)
    {
        $connection = DB::connection($this->connection);
        $id=  $connection->table('g_test_record')
            ->insertGetId([
                'logid' =>  $data['logid'],
                'stuid' =>  $data['stuid'],
                'cid' =>  $data['cid'],
                'answer' =>   $data['answer'],
                'type' =>   $data['type'],
            ]);
        if($id){
            $result['code']=1;
            $result['msg']='提交成功';
            $result['id'] = $id;
            return $result;

        }
        $result['code']=0;
        $result['msg']='提交失败';

        return $result;

    }

    //判断学生是否已提交试卷
    public function ifadd($dataArray){
        $connection = DB::connection($this->connection);
        $result = $connection->table('g_test_statistics')
            ->where('g_test_statistics.logid',$dataArray['logid'])
            ->where('g_test_statistics.stuid',$dataArray['stuid'])
            ->where('g_test_statistics.ifexam',3)
            ->first();
        return $result;
    }


    //查询科室考试的列表
    public function searchResultlist($logid)
    {
        $search = DB::table('g_test_log')
            ->join('g_test_record','g_test_log.id','=','g_test_record.logid')
            ->join('g_user','g_user.id','=','g_test_record.stuid')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->select('g_department.name as departname','g_user.name as stuname','g_user.stu_nu','g_test_log.type','g_test_log.start','g_test_log.end','g_test_record.logid','g_test_record.stuid','g_test_log.teacher','g_test_log.classroom','g_test_log.ifshow')
            ->where('g_test_log.id',$logid)
            ->orderBy('g_test_log.start','desc')
            ->groupBy('g_test_record.stuid')
            ->get();


        for($i=0;$i<count($search);$i++){
            $scorearray = DB::table('g_test_statistics')
                ->where('g_test_statistics.stuid',$search[$i]->stuid)
                ->where('g_test_statistics.logid',$logid)
                ->get();

            if($scorearray){
                $search[$i]->status = $scorearray[0]->status;
                $search[$i]->objective = $scorearray[0]->objective;
                $search[$i]->subjective = $scorearray[0]->subjective;
            }

        }
        return $search;

    }


    //查询试卷的信息
    public function searchExamDetail($logid,$stuid)
    {
        $search = DB::table('g_test_record')
            ->join('g_test_log','g_test_log.id','=','g_test_record.logid')
            ->join('g_user','g_user.id','=','g_test_record.stuid')
            ->join('g_test_content','g_test_content.id','=','g_test_record.cid')
            ->join('g_test_statistics','g_test_statistics.logid','=','g_test_log.id')
            ->join('g_test','g_test.id','=','g_test_log.tid')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->select('g_test_record.*','g_department.name as departname','g_user.name as stuname','g_test_content.question','g_test_content.answer as rightanswer','g_test_statistics.time as alltime','g_test_statistics.objective','g_test_content.content','g_test_content.poins as score','g_test.name as examname')
            ->where('g_test_log.id',$logid)
            ->where('g_test_record.stuid',$stuid)
            ->orderBy('g_test_log.id')
            ->groupby('g_test_record.id')
            ->get();

        return $search;

    }


    //修改试卷的信息
    public function updateExamDetail($data)
    {
        $connection = DB::connection($this->connection);
        $sbuder = $connection->table('g_test_record')
            ->where('g_test_record.logid',$data['logid'])
            ->where('g_test_record.cid',$data['id'])
            ->get();

        if($sbuder){

            $search = DB::table('g_test_record')
                ->where('g_test_record.logid',$data['logid'])
                ->where('g_test_record.cid',$data['id'])
                ->update([
                    'isright' => $data['isright'],
                    'poins' => $data['poins'],
                ]);

            return $search;
        }


    }

    //修改试卷的信息
    public function updateExamCanShow($data)
    {
        $search = DB::table('g_test_log')
            ->where('g_test_log.id',$data['id'])

            ->update([
                'ifshow' => 1,

            ]);

        if($search){
            $result['code']=1;
            $result['msg']='确认成功';
            return $result;

        }
        $result['code']=0;
        $result['msg']='确认失败';
        return $result;

    }

    //自动判断客观题成绩
    public function objectResult($data)
    {
        $connection = DB::connection($this->connection);

        $record = $connection->table('g_test_record')
            ->join('g_test_content','g_test_content.id','=','g_test_record.cid')
            ->where('g_test_record.type','<',4)
            ->where('g_test_record.id',$data['id'])
            ->select('g_test_content.answer as rightresult','g_test_content.poins','g_test_record.answer')
            ->get();

        $score=0;
        if($record){
            if($record[0]->answer==$record[0]->rightresult){
                $connection->table('g_test_record')
                    ->where('g_test_record.id',$data['id'])
                    ->update([
                        'isright' => 1,
                        'poins' => $record[0]->poins,
                    ]);

                $score+=$record[0]->poins;
            }else{
                $connection->table('g_test_record')
                    ->where('g_test_record.id',$data['id'])
                    ->update([
                        'isright' => 2,
                        'poins' => 0,
                    ]);
            }

        }


        return $score;
    }


    //查询科室考试的内容
    public function searchDepartStudents($data)
    {
        $this->logdata=$data;

        $builder = DB::table('g_sdcycle')
            ->join('g_user','g_user.id','=','g_sdcycle.stuid')
            ->leftjoin('g_test_statistics', function ($leftjoin) {
                $leftjoin->on('g_test_statistics.stuid', '=', 'g_user.id')
                    ->where('g_test_statistics.logid', '=',$this->logdata['logid']);
            })
            ->where('g_sdcycle.department_id',$data['department_id'])
            ->where('g_sdcycle.plan_starttime','<',time())
            ->where('g_sdcycle.endtime',0)
            ->select('g_user.name','g_sdcycle.stuid','g_test_statistics.ifexam')
            ->get();

        return $builder;

    }

    //新增统计
    public function addstatics($data)
    {
        $builder = DB::table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['logid'])
            ->where('g_test_statistics.stuid',$data['stuid'])
            ->update([
                'logid' =>  $data['logid'],
                'stuid' =>  $data['stuid'],
                'time' =>  $data['time'],
                'objective' =>   $data['objective']

            ]);

        if($builder){
            $result['code']=1;
            $result['msg']='新增成功';
            return $result;

        }
        $result['code']=0;
        $result['msg']='新增失败';
        return $result;

    }

    //新增平均分
    public function addaveragescore($data)
    {

        $search = DB::table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['id'])
            ->get();

        $objaver=0;
        $subaver=0;

        if($search){
            for($i=0;$i<count($search);$i++){
                $objaver+=$search[$i]->objective;
                $subaver+=$search[$i]->subjective;

            }
            $objaver=$objaver/count($search);

            $subaver=$subaver/count($search);
            $builder = DB::table('g_test_average')
                ->insertGetId([
                    'logid' =>  $data['id'],
                    'objaverage' =>   $objaver,
                    'subaverage' =>   $subaver,

                ]);

            if($builder){
                $result['code']=1;
                $result['msg']='新增成功';
                return $result;

            }
            $result['code']=0;
            $result['msg']='新增失败';
            return $result;

        }

    }

    //更新统计
    public function updatestatics($data)
    {
        $builder = DB::table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['logid'])
            ->where('g_test_statistics.stuid',$data['stuid'])
            ->update([
                'subjective' =>   $data['subjective'],
                'status' => 1
            ]);

        if($builder){
            $result['code']=1;
            $result['msg']='新增成功';
            return $result;

        }
        $result['code']=0;
        $result['msg']='新增失败';
        return $result;

    }

    //查看科室里的老师
    public function seachuserdeparts($data)
    {

        $builder= DB::select("SELECT id FROM g_department WHERE teacher_ids LIKE '%$data,%' OR teacher_ids LIKE '%$data' OR teacher_id = '$data'");

        return $builder;
    }

    //查询一个学生在一个科室的考试
    public function searchstudent($data)
    {

        $builder = DB::table('g_test_log')
            ->join('g_test_statistics','g_test_statistics.logid','=','g_test_log.id')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->join('g_user','g_user.id','=','g_department.teacher_id')
            ->where('g_test_statistics.stuid',$data['userid'])
            ->where('g_test_log.type',$data['type'])
            ->where('g_test_log.ifshow',1)
            ->select('g_test_statistics.logid','g_department.name as departname','g_test_log.start as starttime','g_test_statistics.objective','g_test_statistics.subjective','g_user.name as teachername');

        if($data['department_id']!=0){
            $builder=$builder->where('g_test_log.department_id',$data['department_id']);
        }
        $builder=$builder->get();

        return $builder;

    }

    //查询教师在一个科室的考试成绩
    public function departscorelist($data)
    {
        $builder = DB::table('g_test_log')
            ->join('g_test_statistics','g_test_statistics.logid','=','g_test_log.id')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->join('g_user','g_user.id','=','g_department.teacher_id')
            ->where('g_test_log.department_id',$data['department_id'])
            ->where('g_test_statistics.type',$data['type'])
            ->select('g_test_statistics.stuid','g_test_statistics.logid','g_department.name as departname','g_test_log.start as starttime','g_test_statistics.objective','g_test_statistics.subjective','g_user.name as teachername');

        if($data['starttime']!=0){

            $builder=$builder ->where('g_test_log.start','>',$data['starttime']);

        }elseif($data['endtime']!=0){

            $builder=$builder  ->where('g_test_log.end','<',$data['endtime']);
        }

        $builder=$builder->get();

        return $builder;

    }



    //管理员教师在一个科室的考试成绩
    public function glyscorelist($data)
    {
        $builder = DB::table('g_test_average')
            ->join('g_test_log','g_test_log.id','=','g_test_average.logid')
            ->join('g_department','g_department.id','=','g_test_log.department_id')
            ->join('g_user','g_user.id','=','g_department.teacher_id')
            ->where('g_test_log.start','>',$data['starttime'])
            ->where('g_test_log.ifshow',1)
            ->where('g_test_log.type',$data['type']);
        if($data['department_id']!=0){
            $builder=$builder  ->where('g_test_log.department_id',$data['department_id']);
        }

        if($data['endtime']!=0){
            $builder=$builder  ->where('g_test_log.end','<',$data['endtime']);
        }


        $builder=$builder
            ->select('g_department.name as departname','g_test_log.start as starttime','g_test_average.subaverage','g_test_average.objaverage','g_user.name as teachername')
            ->get();

        return $builder;

    }




}


