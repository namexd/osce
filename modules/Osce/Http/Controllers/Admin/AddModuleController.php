<?php
/**
 * Created by PhpStorm.
 * User: hsiaowei <phper.tang@qq.com>
 * Date: 2017/8/8
 * Time: 14:06
 */
namespace Modules\Osce\Http\Controllers\Admin;


use Illuminate\Http\Request;
use DB;
use Modules\Osce\Entities\TestContentModule;
use Pingpong\Modules\Routing\Controller;
use Excel;

class AddModuleController extends Controller
{
    public function a(Request $request){
        try {

            $ids = TestContentModule::get()->pluck('id')->all();//多的部分
            $idss = collect(DB::connection('osce_mis')->table('g_test_content_module_bak')->get())->pluck('id')->all();
            $id_diff = array_diff ($ids,$idss);
            dd($id_diff);
            $value = $request->get('number');
            dd($value);
            $upper = array('零','一','二','三','四','五','六','七','八','九');
            $units = array('十','百','千','万','十','百','千','亿','十','百','千');

        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }
    }
    public function index(){
        //$file->move(storage_path().'/exports',$name);
        //$filePath = '../storage/exports/'.iconv('UTF-8', 'GBK//ignore', $time).'.'.$entension;
        /*$a='"Deficiency of growth hormone leads to-
        a)Delayed fusion of epiphysis
        b)Proportionate dwarfism
        c)Acromegaly
        d)Mental retardation
        "
        ';
        dd( strtr($a,['a)'=>'A.','b)'=>'B.','c)'=>'C.','d)'=>'D.']) );*/
//DD(strtr('A&nbsp;B&nbsp;C&nbsp;D&nbsp;E',['&nbsp;'=>' ','\''=>'']));
        //dd(strip_tags('Hello <b>World</b>'));
        /*$a = strtr("'A&nbsp;B&nbsp;C&nbsp;D&nbsp;E",['&nbsp;'=>'','\''=>'']);
        $b='';
        for($i=0;$i<strlen($a);$i++){
            $b=$b.substr($a,$i,1).' ';
        }
        dd(rtrim($b));*/
        /*$str = '男，2岁，患室间隔缺损合并心功能不全，6个月前开始用地高辛、卡托普利治疗。近一周内出现恶心、呕吐，食欲不振，胃纳明显减少，并伴有头晕、嗜睡，无发热、咳嗽，无腹泻，心电图示胸前导联ST段鱼钩样压低，首先考虑为:
A．合并急性胃炎
B．地高辛剂量不足，心功能不全未能控制
C．地高辛中毒
D．低钾血症
E．病毒性心肌炎
';
        dd(explode("A.",$str));*/
        /*$result="多&nbsp;&nbsp;10&nbsp;&nbsp;&nbsp;6～8";
        dd(strtr(strip_tags($result),['&nbsp;'=>'','\''=>'']));*/
        //return json_encode(TestContentModule::get());
        return Excel::load('E:/2013724110317.xls', function($reader) {

            //获取excel的第1张表
            $reader = $reader->getSheet(0);

            //获取试卷名称
            $name = [];
            //获取表中的数据
            $results = $reader->toArray();

            /*$arr = array();

             for($i=0;$i<count($results);$i++){
                 if($i>1){
                     $arr[] = $results[$i];
                 }elseif($i==0){
                     $name= $results[$i];
                 }
             }*/

            /* $zname =str_replace(' ', '', $name[1]);

             if($zname=="请在此行填写名称"){

                 $this->result =1;

                 return;
             }
            $return = [];*/
            //dd($results);
            $contents=[];
            foreach ($results as $key => $result) {
                //题目类型
                if ($result[0] == '单选题' || $result[0] == 'A1型题' || $result[0] == 'A2型题A2型题') {
                    $type = 1;
                }elseif ($result[0] == '多选题' || $result[0] == 'X型题') {
                    $type = 2;
                }elseif ($result[0] == '判断题') {
                    $type = 3;
                }elseif ($result[0] == '填空题') {
                    $type = 4;
                }elseif ($result[0] == '名词解释题') {
                    $type = 5;
                }elseif ($result[0] == '论述题') {
                    $type = 6;
                }elseif ($result[0] == '简答题' || $result[0] =='病例分析题') {
                    $type = 7;
                }

                //认知
                if ($result[6] == '解释') {
                    $cognition = 1;
                }elseif ($result[6] == '记忆') {
                    $cognition = 2;
                }elseif ($result[6] == '应用') {
                    $cognition = 3;
                }

                //题源
                if ($result[7] == '自编') {
                    $source = 1;
                }elseif ($result[7] == '国内') {
                    $source = 2;
                }elseif ($result[7] == '国外') {
                    $source = 3;
                }

                //适用层次
                if ($result[8] == '专科生') {
                    $lv = 1;
                }elseif ($result[8] == '本科生') {
                    $lv = 2;
                }elseif ($result[8] == '研究生') {
                    $lv = 3;
                }elseif ($result[8] == '博士生') {
                    $lv = 4;
                }

                //要求度
                if ($result[9] == '熟悉') {
                    $require = 1;
                }elseif ($result[9] == '了解') {
                    $require = 2;
                }elseif ($result[9] == '掌握') {
                    $require = 3;
                }

                //难度
                if ($result[11] == '简单') {
                    $degree = 1;
                }elseif ($result[11] == '中等') {
                    $degree = 2;
                }elseif ($result[11] == '较难') {
                    $degree = 3;
                }
                //矫正答案
                if($type<4){
                    $answer = strtr(strip_tags($result[2]),['&nbsp;'=>' ','\''=>'']);
                    $answerR='';
                    for($i=0;$i<strlen($answer);$i++){
                        $answerR=$answerR.substr($answer,$i,1).' ';
                    }
                }else{
                    $answerR = strtr(strip_tags($result[2]),['&nbsp;'=>' ','\''=>'']);
                }
                $desc = strtr(strip_tags($result[1]),['a)'=>'A.','b)'=>'B.','c)'=>'C.','d)'=>'D.','e)'=>'E.','A．'=>'A.','B．'=>'B.','C．'=>'C.','D．'=>'D.','E．'=>'E.']);

                echo $key.'==='.$result[0].'</br>';

                if($type<3){
                    $ex = explode("A.",$desc);
                    $question = $ex[0];
                    $content = 'A.'.$ex[1];

                }else{
                    $question = $desc;
                    $content = '';
                }

                $contents[] = array(
                    'type' => $type,
                    'category' => $result[0],
                    'question' =>$question,
                    'content' => $content,
                    'answer' => rtrim($answerR),//strtr('A&nbsp;B&nbsp;C&nbsp;D&nbsp;E',['&nbsp;'=>' ','\''=>''])
                    'pbase' => $result[3],//主题一
                    'base' =>empty($result[4] && $result[5])?'':$result[4].','.$result[5],//主题二
                    'cognition' => $cognition,//认知
                    'source' => $source,//题源
                    'lv' => $lv,//适应层次
                    'require' => $require,//要求度
                    'times' => $result[10],//答题时间
                    'degree' => $degree,//难度
                    'separate' => $result[12],//区分度
                    //'poins' => $result[13]//分值
                );
                //$sumScore += $result[13];

            }
            $return[] = TestContentModule::insert($contents);
        });
    }

}