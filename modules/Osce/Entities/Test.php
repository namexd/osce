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

class Test extends CommonModel
{
    protected $table='g_test';
    protected $primaryKey='id';
    public $timestamps=false;

    //新增答卷
    public function addTest($data)
    {
        $id = DB::table('g_test')->insertGetId([
            'name'           =>  $data['name'],
            'ctime'           =>  time()
        ]);

        return $id;
    }

    //新增题目
    public function addContent($data)
    {

        $id = DB::table('g_test_content')->insertGetId([
            'type'           =>  $data['type'],
            'answer'          =>   $data['answer'],
            'poins'           =>  $data['poins'],
            'question'          =>   $data['question'],
            'content'          =>   $data['content'],
            'pbase'           =>  $data['pbase'],
            'base'          =>   $data['base'],
            'cognition'           =>  $data['cognition'],
            'source'          =>   $data['source'],
            'lv'          =>   $data['lv'],
            'require'           =>  $data['require'],
            'times'          =>   $data['times'],
            'degree'           =>  $data['degree'],
            'separate'          =>   $data['separate']
        ]);

/*        DB::table('test_content')->insertGetId([
            'tid'            =>  $data['tid'],
            'cid'            =>  $id
        ]);*/

        return $id;
    }

    //选择试题
    public function getChoose($data)
    {

        $info = DB::table('g_test')
            ->select('id','name')
            ->get();

        return $info;

    }

    //选择考试
    public function getChooseExam()
    {

        $info = DB::table('exam')->where('status',0)->get();

        return $info;

    }


    //选择教室
    public function del($data)
    {

        $base = DB::table('g_test_log')
            ->where('g_test_log.tid',$data['id'])
            ->get();

        $arr = array();
        if($base!=''&&$base!=null){
            $arr['log'] = 1;
            return $arr;
        }
        DB::table('g_test')
            ->where('g_test.id',$data['id'])
            ->delete();

        $info = DB::table('test_content')
            ->where('test_content.tid',$data['id'])
            ->get();

        DB::table('test_content')
            ->where('test_content.tid',$data['id'])
            ->delete();
        foreach($info as $item){
            $count =  DB::table('g_test_content')
                ->where('g_test_content.id',$item->cid)
                ->delete();
        }

        $arr['count'] = $count;
        return $count;

    }



}


