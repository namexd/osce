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




    //更新学生的答题状态
    public function stunowexam($data)
    {
        $connection = DB::connection($this->connection);
        $connection->table('g_test_statistics')->insert([
        'logid' => $data['id'],
        'stuid' => $data['userid'],
         'time' => $data['time']
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
            ->first();
        return $result;
    }


    //查询考试的列表
    public function searchResultlist($logid)
    {
        $connection = DB::connection($this->connection);
        $search = $connection->table('g_test_log')
            ->join('g_test_record','g_test_log.id','=','g_test_record.logid')
            ->join('student','student.user_id','=','g_test_record.stuid')
            ->select('studnet.name as stuname','g_test_log.type','g_test_log.start','g_test_log.end','g_test_record.logid','g_test_record.stuid','g_test_log.teacher','g_test_log.classroom','g_test_log.ifshow')
            ->where('g_test_log.id',$logid)
            ->orderBy('g_test_log.start','desc')
            ->groupBy('g_test_record.stuid')
            ->get();


        for($i=0;$i<count($search);$i++){
            $scorearray = $connection->table('g_test_statistics')
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
        $connection = DB::connection($this->connection);

        $search = $connection->table('g_test_record')
            ->join('g_test_log','g_test_log.id','=','g_test_record.logid')
            ->join('student','student.user_id','=','g_test_record.stuid')
            ->join('g_test_content','g_test_content.id','=','g_test_record.cid')
            ->join('g_test_statistics','g_test_statistics.logid','=','g_test_log.id')
            ->join('g_test','g_test.id','=','g_test_log.tid')
            ->select('g_test_record.*','g_test_record.poins as score','student.name as stuname','g_test_content.question','g_test_content.answer as rightanswer','g_test_statistics.time as alltime','g_test_statistics.status as zt','g_test_statistics.objective','g_test_content.content','g_test_content.poins','g_test_content.category','g_test_log.name as examname','g_test.score as examscore','g_test_log.times')
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

            $search = $connection->table('g_test_record')
                ->where('g_test_record.logid',$data['logid'])
                ->where('g_test_record.cid',$data['id'])
                ->update([
                    'isright' => $data['isright'],
                    'poins' => $data['poins'],
                ]);

            return $search;
        }


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



    //新增统计
    public function addstatics($data)
    {
        $connection = DB::connection($this->connection);
 /*       $arrobj = $connection->table('g_test_log')->where('id',$data['logid'])->first();
        $tid = $arrobj->tid;
        $arrobj1 = $connection->table('g_test_content')->where('test_id',$tid)->where('type','>',3)->first();
        if($arrobj1){
            $status = 2;
        }else{
            $status = 0;
        }*/
        $builder = $connection->table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['logid'])
            ->where('g_test_statistics.stuid',$data['stuid'])
            ->update([
                'logid' =>  $data['logid'],
                'stuid' =>  $data['stuid'],
                'time' =>  $data['time'],
                //'status'=> $status,
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
        $connection = DB::connection($this->connection);
/*        $arrobj = $connection->table('g_test_log')->where('id',$data['logid'])->first();
        $tid = $arrobj->tid;
        $arrobj1 = $connection->table('g_test_content')->where('test_id',$tid)->where('type','>',3)->first();
        if($arrobj1){
            $status = 2;
        }else{
            $status = 1;
        }*/

        $status = 1;
        $builder = $connection->table('g_test_statistics')
            ->where('g_test_statistics.logid',$data['logid'])
            ->where('g_test_statistics.stuid',$data['stuid'])
            ->update([
                'subjective' =>   $data['subjective'],
                'status' => $status
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


