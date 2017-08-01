<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Excel;
use Modules\Osce\Entities\Test;
class TestController extends CommonController
{

    public $result;
    //get  文件导入
    public function import(Request $request)
    {
        header("Content-Type: text/html; charset=utf-8");


        $file = Input::file('file');
        //获取后缀
        $entension = $file->getClientOriginalExtension();
        //修改为时间戳格式的名字
        $time = time();
        $name = $time.'.'.$entension;
        //移动到资源目录
        $file->move(storage_path().'/exports',$name);
        $filePath = '../storage/exports/'.iconv('UTF-8', 'GBK', $time).'.'.$entension;


        Excel::load($filePath, function($reader) {

            //获取excel的第1张表
            $reader = $reader->getSheet(0);

            //获取试卷名称
            $name=[];
            //获取表中的数据
            $results = $reader->toArray();
            $arr = array();
            for($i=0;$i<count($results);$i++){
                if($i>1){
                    $arr[] = $results[$i];
                }elseif($i==0){
                    $name= $results[$i];
                }
            }

            $zname =str_replace(' ', '', $name[1]);

            if($zname=="请在此行填写名称"){

                $this->result =1;

                return;
            }

            $test = new Test();

            $tdata = array(
                'name'    => $name[1]
            );
            $tid = $test->addTest($tdata);

            $return = [];

            foreach($arr as $result){
                //题目类型
                if($result[0]=='单选题'){
                    $type = 1;
                }
                if($result[0]=='多选题'){
                    $type = 2;
                }
                if($result[0]=='判断题'){
                    $type = 3;
                }
                if($result[0]=='填空题'){
                    $type = 4;
                }
                if($result[0]=='名词解释题'){
                    $type = 5;
                }
                if($result[0]=='论述题'){
                    $type = 6;
                }
                if($result[0]=='简答题'){
                    $type = 7;
                }

                //认知
                if($result[6]=='解释'){
                    $cognition = 1;
                }
                if($result[6]=='记忆'){
                    $cognition = 2;
                }
                if($result[6]=='应用'){
                    $cognition = 3;
                }

                //题源
                if($result[7]=='自编'){
                    $source = 1;
                }
                if($result[7]=='国内'){
                    $source = 2;
                }
                if($result[7]=='国外'){
                    $source = 3;
                }

                //适用层次
                if($result[8]=='专科生'){
                    $lv = 1;
                }
                if($result[8]=='本科生'){
                    $lv = 2;
                }
                if($result[8]=='研究生'){
                    $lv = 3;
                }
                if($result[8]=='研究生'){
                    $lv = 4;
                }

                //要求度
                if($result[9]=='熟悉'){
                    $require = 1;
                }
                if($result[9]=='了解'){
                    $require = 2;
                }
                if($result[9]=='掌握'){
                    $require = 3;
                }

                //难度
                if($result[11]=='简单'){
                    $degree = 1;
                }
                if($result[11]=='中等'){
                    $degree = 2;
                }
                if($result[11]=='较难'){
                    $degree = 3;
                }

                $contents = array(
                    'tid'         =>  $tid,
                    'type'        =>  $type,
                    'question'   =>  $result[1],
                    'content'   =>  $result[2],
                    'answer'      =>   $result[3],
                    'pbase'       =>  $result[4],
                    'base'        =>   $result[5],
                    'cognition'  =>  $cognition,
                    'source'     =>   $source,
                    'lv'          =>  $lv,
                    'require'    =>  $require,
                    'times'      =>  $result[10],
                    'degree'     =>  $degree,
                    'separate'   =>  $result[12],
                    'poins'      =>  $result[13]
                );

                $id = $test->addContent($contents);
                $return[] = $id;

            }

        });

        if($this->result==1){

            $back = $this->rmsg(0,'导入失败,请填写试卷名称');

            return $back;

        }

        unlink($filePath);

        $back = $this->rmsg(1,'导入成功');

        return $back;

    }

    //get  文件导出
    public function export(){
        $cellData = [
            ['试卷名称','请在此行填写名称'],
            ['题目类型','问题内容','问题选项（单选、多选)','答案','考察知识模块','知识要点','认知','题源','适用层次','要求度','答题时间','难度','区分度','分值'],
            ['填空题','什么情况下行妇科三合诊检查？(1)__(2)__','','了解宫颈癌分期 了解后盆腔病变如子宫内膜异位症，盆腔包块','诊断基础','检查方法','解释','自编','本科生','熟悉','2','中等','0.00','2'],
            ['单选题','关于输卵管，下列哪项是错误的？','A.分为间质部、峡部、壶腹部和漏斗部（伞部） B.内侧与子宫角相连通，外端游离  C.与卵巢一起共称子宫附件 D.受性激素的影响，粘膜有周期性的组织学变化 E.输卵管上皮为单层鳞状上皮上皮','E','解剖与生理','解剖','解释','自编','本科生','熟悉','2','中等','0.00','1'],
            ['名词解释题','子宫韧带','','由圆韧带、阔韧带、主韧带及宫骶韧带组成。','解剖与生理','解剖','记忆','自编','本科生','熟悉','2','简单','0.00','3'],
            ['填空题','子宫体壁由三层组织构成，分别为__、__和__。','','粘膜层 肌层 浆膜层','解剖与生理','解剖','记忆','自编','本科生','熟悉','1','简单','0.00','2'],
            ['多选题','下述左右语句所示内容，哪几项是正确的。','A.染色体检查--原发性闭经  B.测血中hCG量--水泡状胎块 C.结核杆菌培养--子宫性闭经  D.盆腔淋巴结造影--绒毛膜上皮癌 E.宫颈刮片--子宫内膜癌','A B C','诊断基础','检查方法','应用','自编','本科生','熟悉','2','中等','0.00','3'],
            ['判断题','宫颈活组织检查目的是为了诊断子宫内膜癌。','','错误','诊断基础','检查方法','解释','自编','本科生','熟悉','2','中等','0.00','1'],
            ['判断题','宫颈刮片报告为巴氏Ⅰ级，提示正常，未见癌细胞。','','正确','诊断基础','检查方法','解释','自编','本科生','熟悉','2','简单','0.00','1']
        ];
        Excel::create('demo',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    //删除试题
    public function del(Request $request){

        $data = $request->all();

        $test = new Test();

        $result = $test->del($data);
        if(isset($result['log'])){
            $back = $this->rmsg(2,'试题已使用，无法删除');
        }else{
            $back = $this->rmsg(1,'删除成功');
        }

        return $back;
    }


    //选择试题
    public function choose(Request $request){


        $data = $request->all();

        $test = new Test();

        $result = $test->getChoose($data);

        if(isset($data['page'])){
            $count = $data['pagecount'];
            $page  = $data['page'];

            $arr = [];
            for($i=0;$i<count($result);$i++){
                if($i>=($page-1)*$count&&$i<=$page*$count){
                    $arr[] = $result[$i];
                }
            }

            $back = array(
                'allcount' => count($result),
                'data'     => $arr
            );
            return $back;
        }else{
            return $result;
        }


    }

    //选择考试

    public function chooseexam(Request $request){

        $data = $request->all();

        $test = new Test();

        $result = $test->getChooseExam($data);

        if(isset($data['page'])){
            $count = $data['pagecount'];
            $page  = $data['page'];

            $arr = [];
            for($i=0;$i<count($result);$i++){
                if($i>=($page-1)*$count&&$i<=$page*$count){
                    $arr[] = $result[$i];
                }
            }

            $back = array(
                'allcount' => count($result),
                'data'     => $arr
            );
            return $back;
        }else{
            return $result;
        }


    }

    public function chooseteacher(){
        $test = new Test();
        $result = $test->getChooseTeacher();
        return $result;
    }




}
