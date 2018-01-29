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
use Modules\Msc\Entities\Student;
use Modules\Osce\Entities\IpLimit;
use Modules\Osce\Entities\TestContent;
use Modules\Osce\Entities\TestContentModule;
use Modules\Osce\Entities\TestStatistics;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Excel;
use Modules\Osce\Entities\Test;
use Modules\Osce\Entities\TestLog;
use Input;

class TestController extends CommonController
{

    public $result;

    //显示最近一个月的考试列表
    public function examList(){
        $list= TestLog::where('start','>',date('Y-m-d H:i:s',strtotime('-1 month')))->get();
        return view('osce::theory.slect_exam_list')->with('list',$list);

    }

    public function add(){
        $choose = $this->choose();
        $chooseexam = $this->chooseexam();
        $chooseteacher = $this->chooseteacher();
        return view('osce::theory.exam_manage')->with('data',['choose'=>$choose,'chooseexam'=>$chooseexam,'chooseteacher'=>$chooseteacher]);
    }

    public function index(){
        $data = TestLog::paginate(10);
        return view('osce::theory.exam_list')->with('data',$data);
        //$data[0]->exam;
        //dd($data);
    }
    public function examquestion(){
        $data = Test::orderBy('id','desc')->paginate(10);
        return view('osce::theory.exam_question')->with('data',$data);
    }
    public function autoquestion(){
        $data = TestContentModule::select('type', DB::raw('count(id) as sum_count'))
            -> groupBy('type')->get();
        return view('osce::theory.exam_autoquestion')->with('data',$data);
    }
    //试卷新增
    public function autoexamadd(Request $request){
        return view('osce::theory.exam_add');
    }
    //试卷修改
    public function autoexamedit(Request $request){
        $this->validate($request, [
            'id'    => 'required'
        ],[
            'id.required'   => '试卷ID必传'
        ]);
        $id =$request->get('id');

        $testlog = TestLog::where('tid',$id)->get();
        if(empty($testlog)){
            $data =Test::find($id);
            return view('osce::theory.exam_edit')->with('data',$data);
        }else{
            return redirect()->back()->withErrors('修改失败，已存在考试引入此试题');
        }
    }
    //试卷预览
    public function autoexampreview(Request $request){
        $this->validate($request, [
            'id'    => 'required'
        ],[
            'id.required'   => '试卷ID必传'
        ]);
        $id =$request->get('id');
        $data =Test::find($id);
        return view('osce::theory.exam_preview')->with('data',$data);
    }
    public function autoexam(Request $request){
        $this->validate($request, [
            'name'    => 'required',
            'type'    => 'required',
            'number'    => 'required',
            'score'    => 'required',
        ],[
            'name.required'   => '试卷名称必传',
            'type.required'   => '类型必传',
            'number.required'   => '数量必传',
            'score.required'   => '分数必传',
        ]);
        $name = $request->get('name');
        $typeArr = $request->get('type');
        $numberArr = $request->get('number');
        $scoreArr = $request->get('score');

        if( count($typeArr) != count($numberArr) && count($typeArr) != count($scoreArr) ){
            return redirect()->back()->withErrors('参数有误！');
        }
        $test_id = Test::insertGetId(['name' =>  $name, 'ctime' => date('Y-m-d H:i:s')]);
        $sum = $this->creatautoexam($test_id,$typeArr,$numberArr,$scoreArr);
        $data =Test::find($test_id);
        $data->update(['score'=>$sum]);
        return $this->success_data(['test_id'=>$test_id]);
    }
    public function onceautoexam(Request $request){
        $this->validate($request, [
            'id'    => 'required'
        ],[
            'id.required'   => '试卷ID必传'
        ]);
        $id =$request->get('id');
        $exam = TestContent::where('test_id',$id)->select(DB::raw('test_id,type,count(1) as number,poins'))->groupBy('type')->get();
        $typeArr=[];$numberArr=[];$scoreArr=[];
        foreach($exam as $key => $val){
            $typeArr[$key]=$val->type;
            $numberArr[$key]=$val->number;
            $scoreArr[$key]=$val->poins;
        }
        //dd($typeArr,$numberArr,$scoreArr);
        TestContent::where('test_id',$id)->delete();
        $this->creatautoexam($id,$typeArr,$numberArr,$scoreArr);
        $data= Test::find($id);
        return $this->success_data();
    }
    protected function creatautoexam($test_id,$typeArr,$numberArr,$scoreArr){
        $sum =0 ;
        foreach($typeArr as $key =>$val){
            if($typeArr[$key] && $numberArr[$key] && $scoreArr[$key]){
                $sum=$sum + $numberArr[$key]*$scoreArr[$key];
                $data = TestContentModule::where('type',$val)->get()->shuffle()->take($numberArr[$key]);
                foreach($data as $k=>$value ){
                    $insert = [
                        'test_id'=>$test_id,
                        'type'=>$value->type,
                        'images'=>$value->images,
                        'answer'=>$value->answer,
                        'poins'=>$scoreArr[$key],
                        'question'=>$value->question,
                        'pbase'=>$value->pbase,
                        'base'=>$value->base,
                        'cognition'=>$value->cognition,
                        'source'=>$value->source,
                        'lv'=>$value->lv,
                        'require'=>$value->require,
                        'times'=>$value->times,
                        'degree'=>$value->degree,
                        'separate'=>$value->separate,
                        'content'=>$value->content
                    ];
                    //dd($data,$insert);
                    TestContent::create($insert);
                }
            }
        }
        return $sum;
    }

    public function delquestion(Request $request){
        $this->validate($request, [
            'id'    => 'required|integer',
        ],[
            'id.required'   => 'ID必传',
        ]);
        $id = $request->get('id');
        $isUser = TestLog::where('tid',$id)->first();
        if(empty($isUser)){
            Test::find($id)->delete();
            TestContent::where('test_id',$id)->delete();
            return redirect()->route('osce.theory.examquestion')->withErrors('1删除成功！');
        }else{
            return redirect()->back()->withErrors('删除失败，已存在考试引入此试题！');
        }
    }
    /*
     * 修改题目
     * */
    public function editQuestionList(Request $request){
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'question' => 'required',
        ], [
            'id.required' => '试卷ID必传',
            'name.required' => '试卷名称必传',
            'question.required' => '试卷考题必传',
        ]);
        $id = $request->get('id');
        $name = $request->get('name');
        $question = $request->get('question');
        try {
            $test =Test::find($id);
            if($test){
                //判断是否关联考试并且考试是否已经开始
                if(empty($test->testLog) || (!empty($test->testLog) && $test->testLog->start > date('Y-m-d H:i:s') )){
                    TestContent::where('test_id',$id)->delete();
                    foreach($question as $key=>$val){
                        /*
                         * cognition 认知: 1解释 2记忆 3应用
                         * source 题源: 1自编 2国内 3国外
                         * lv 适应层次:1专科生 2本科生 3研究生 4博士生
                         * require 要求度:1熟悉 2了解 3掌握
                         * degree 难度: 1简单 2中等 3较难
                         * pbase 考察知识模块
                         * base 知识要点
                         * separate 区分度
                         * times 时长
                         */
                        /*if($val->hasFile('exam_images')){
                            $fileData = $this->uploadFile('exam_images',10,'uploads/exam/');
                            if($fileData['code'] == 1){
                                $questionArr['images'] = $fileData['filepath'];
                            }
                        }*/
                        $questionArr['test_id'] = $id;
                        $questionArr['type'] = $val['type'];
                        $questionArr['question'] = $val['question'];
                        $questionArr['content'] = $val['content'];
                        $questionArr['answer'] = $val['answer'];
                        $questionArr['poins'] = $val['poins'];

                        /*默认*/
                        $questionArr['cognition'] = 1;
                        $questionArr['source'] = 1;
                        $questionArr['lv'] = 1;
                        $questionArr['require'] = 1;
                        $questionArr['degree'] = 1;
                        $questionArr['pbase'] = '基础知识';//考察知识模块默认值
                        $questionArr['base'] = '基础知识';//知识要点默认值
                        $questionArr['separate'] =0;//区分度默认值
                        $questionArr['times'] = 3;
                        TestContent::create($questionArr);
                    }

                    //更新试卷表中的成绩
                    $sumScore = TestContent::where('test_id',$id)->sum('poins');
                    $test->update(['score'=>$sumScore,'name'=>$name]);
                    return $this->success_data();
                }else{
                    return $this->success_data([],0,'当前试卷已关联考试，并且已经开始，不允许编辑题目！');
                }
            }else{
                return $this->success_data([],0,'参数有误！');
            }
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }

    }
    /*
     * 同时新增多题目
     * */
    public function addQuestionList(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'question' => 'required',
        ], [
            'name.required' => '试卷名称必传',
            'question.required' => '试卷考题必传',
        ]);
        try {
            $name = $request->get('name');
            $question = $request->get('question');
            $test =Test::create(['name'=>$name,'ctime'=>date('Y-m-d H:i:s')]);
            if($test){
                foreach($question as $key=>$val){
                    /*
                     * cognition 认知: 1解释 2记忆 3应用
                     * source 题源: 1自编 2国内 3国外
                     * lv 适应层次:1专科生 2本科生 3研究生 4博士生
                     * require 要求度:1熟悉 2了解 3掌握
                     * degree 难度: 1简单 2中等 3较难
                     * pbase 考察知识模块
                     * base 知识要点
                     * separate 区分度
                     * times 时长
                     */
                    /*if($val->hasFile('exam_images')){
                        $fileData = $this->uploadFile('exam_images',10,'uploads/exam/');
                        if($fileData['code'] == 1){
                            $questionArr['images'] = $fileData['filepath'];
                        }
                    }*/
                    $questionArr['test_id'] = $test->id;
                    $questionArr['type'] = $val['type'];
                    $questionArr['question'] = $val['question'];
                    $questionArr['content'] = $val['content'];
                    $questionArr['answer'] = $val['answer'];
                    $questionArr['poins'] = $val['poins'];

                    /*默认*/
                    $questionArr['cognition'] = 1;
                    $questionArr['source'] = 1;
                    $questionArr['lv'] = 1;
                    $questionArr['require'] = 1;
                    $questionArr['degree'] = 1;
                    $questionArr['pbase'] = '基础知识';//考察知识模块默认值
                    $questionArr['base'] = '基础知识';//知识要点默认值
                    $questionArr['separate'] =0;//区分度默认值
                    $questionArr['times'] = 3;
                    TestContent::create($questionArr);
                    //更新试卷表中的成绩
                }
                $sumScore = TestContent::where('test_id',$test->id)->sum('poins');
                $test->update(['score'=>$sumScore]);
                return $this->success_data();
            }else{
                return $this->success_data([],0,'参数有误！');
            }
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }

    }
    //上传图片
    public function toUpload(Request $request){
        //dd($request->hasFile('exam_images'));
        if($request->hasFile('exam_images')){
            return  $this->uploadFile('exam_images',10,'uploads/theory/');
        }else{
            return $this->success_data([],0,'参数有误！');
        }
    }
    //删除上传图片
    public function toDeleteUpload(Request $request){
        $this->validate($request, [
            'image_url' => 'required',
        ], [
            'image_url.required' => '图片地址必传',
        ]);
        $isHas = $request->get('image_url');
        if($isHas && file_exists(public_path($isHas)) ){
            unlink(public_path($isHas));
        }
        return $this->success_data();
    }
    public function examscore(){
        $data = TestLog::where('end','<',date('Y-m-d H:i:s'))->paginate(10);
        //dd($data);
        return view('osce::theory.exam_score',['data'=>$data]);
    }

    public function examcheck(){
        $data = TestLog::where('end','<',date('Y-m-d H:i:s'))->paginate(10);
        //dd($data);
        return view('osce::theory.exam_check',['data'=>$data]);
    }


    public function studentscore(Request $request){
        $this->validate($request, [
            'id'    => 'required|integer',
        ], [
            'id.required' => 'ID必传'
        ]);
        $id = $request->get('id');
        $test = TestLog::find($id);
        $data = TestStatistics::where('logid',$id)->paginate(10);
        return view('osce::theory.student_score',['data'=>$data,'test'=>$test]);
    }
    public function studentscoreexport(Request $request){
        $this->validate($request, [
            'id'    => 'required|integer',
        ], [
            'id.required' => 'ID必传'
        ]);
        $id = $request->get('id');
        $data = TestStatistics::where('logid',$id)->get();
        $exam = TestLog::find($id);
        $examName = $exam->exam->name.'的理论考试';
        $teacherName = $exam->teacherdata->name;
        $scoreList[]=['姓名','学号','考试名称','监考老师','客观题得分','主观题得分','考试总成绩'];
        foreach($data as $k=>$v){
            $scoreList[$k+1] = [
                $v->student->name,
                $v->student->code,
                $examName,$teacherName,
                $v->objective,
                $v->subjective,
                intval($v->objective)+intval($v->subjective),
            ];
        }
        //dd($sheet);
        Excel::create(iconv('UTF-8', 'GBK//ignore', $examName.'的理论成绩汇总'),function($excel) use ($scoreList){
            $excel->sheet('score', function($sheet) use ($scoreList){
                $sheet->rows($scoreList);
            });
        })->export('xls');
        //return view('osce::theory.student_score',['data'=>$data]);
    }

    public function studentmarking(Request $request){
        $this->validate($request, [
            'id'    => 'required|integer',
        ], [
            'id.required' => 'ID必传'
        ]);
        $id = $request->get('id');
        $test = TestLog::find($id);
        $data = TestStatistics::where('logid',$id)->paginate(10);
        return view('osce::theory.student_marking',['data'=>$data,'test'=>$test]);
    }


    public function rankStudent(Request $request){
        $this->validate($request, [
            'log_id'    => 'required|integer',
        ],[
            'log_id.required'   => 'ID必传',
        ]);
        $id = $request->get('log_id');
        $logData =TestLog::find($id);
        if($logData){
           $list =  Student::where('exam_id',$logData->exam_id)->orderBy('id','asc')->get()->chunk(20);
            return view('osce::theory.exam_student_list',['data'=>['test'=>$logData,'student'=>$list]]);
        }else{
            return redirect()->back()->withErrors('参数错误！');
        }
    }

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
        $filePath = '../storage/exports/'.iconv('UTF-8', 'GBK//ignore', $time).'.'.$entension;


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
            $sumScore=0;
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
                if($result[8]=='博士生'){
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
                    'test_id'         =>  $tid,
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
                $sumScore+=$result[13];
                $return[] = TestContent::insertGetId($contents);
            }
            Test::find($tid)->update(['score'=>$sumScore]);

        });
        if($this->result==1){
            return redirect()->route('osce.theory.examquestion')->withErrors('导入失败,请填写试卷名称');
        }
        unlink($filePath);
        return redirect()->route('osce.theory.examquestion')->withErrors('1导入成功');


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
        Excel::create(iconv('UTF-8', 'GBK//ignore', '考题模板'),function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    //删除试题
    public function del(Request $request){

        $this->validate($request, [
            'id'    => 'required|integer',
        ],[
            'id.required'   => 'ID必传',
        ]);
        try{
            $id = $request->get('id');
            TestLog::find($id)->delete();
            return redirect()->route('osce.theory.index');
        }
        catch (\Exception $ex){
            return response()->json($this->fail($ex));
        }

    }


    //选择试题
    public function choose(){
        $test = new Test();
        $result = $test->getChoose();
        return $result;

    }

    //选择考试
    //public function chooseexam(Request $request){
    public function chooseexam(){
        $test = new Test();
        $result = $test->getChooseExam();
        return $result;
    }

    public function chooseteacher(){
        $test = new Test();
        $result = $test->getChooseTeacher();
        return $result;
    }

    /*
     * name：获取硕博士所以老师学生列表
     * date：2018/1/29 10:10
     * author:Hsiaowei(phper.tang@qq.com)
     * param： int *
     * param： string *
     * return： array
     * */
    public function iplimit(){
        try {
            $list = IpLimit::get();
            return view('osce::theory.ip_limit_list',['list'=>$list]);

        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($this->fail($ex));
        }
    }
}
