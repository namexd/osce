<?php
/**
 * Created by PhpStorm.
 * User: fandian
 * Date: 2016/1/28 0028
 * Time: 10:32
 */
namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamSpecialScore;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\StudentScoreExport;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\TestLog;

class ExamResultController extends CommonController{


    /**
     *
     * @method GET
     * @url /exam/exam-result-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id          考试id(必须的)
     * * int        station_id       考站id  (必须的)
     * * string     name             学生姓名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function geExamResultList(Request $request)
    {
        $this->validate($request,[
            'exam_id'     => 'sometimes|integer',
            'station_id'  => 'sometimes|integer',
            'name'        => 'sometimes|string',
        ]);

        $examId    = $request->get('exam_id');
        $stationId = $request->get('station_id');
        $name      = $request->get('name');

        $ExamDraft = new ExamDraft();
        //查询考站，(存在考试ID, 根据考试ID查询)
        $stations  = $ExamDraft->getExamAllStations($examId);
        $exams     = Exam::all();

        $ExamResult  = new ExamResult();
        $examResults = $ExamResult->getResultList($examId, $stationId, $name);
        //修改时间显示（时分秒：00:00:00）
        if(!$examResults->isEmpty()){
            foreach($examResults as &$item){
                $item->time = Common::handleTime($item->time);
                $item->invalidSign = $ExamResult->getInvalidSign($item->id);
            }
        }
        //渲染视图
        return view('osce::admin.examManage.score_query')->with(
            [
                'examResults'=> $examResults, 'exams'    => $exams,
                'stations'   => $stations,    'exam_id'  => $examId,
                'station_id' => $stationId,   'name'     => $name
            ]);
    }

    /**
     *获取考试成绩详情页
     * @method GET
     * @url /exam/exam-result-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        成绩id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamResultDetail(Request $request)
    {
       $this->validate($request,[
           'exam_result_id'   => 'required|integer',
           'flag'             => 'sometimes|integer'
       ]);
        $id = intval($request->get('exam_result_id'));      //获取成绩结果ID
        $ExamResult = new ExamResult();
        $result     = $ExamResult->getResultDetail($id);    //获取考试结果详细信息
//        $result     = $examResult->first()->toArray();      //考生成绩明细详情
//        dump($result);
//        foreach($examResult as $item){
//            $result = [
//                'id'            =>$item->id,
//                'exam_name'     =>$item->exam_name,
//                'student'       =>$item->student,
//                'teacher'       =>$item->teacher,
//                'begin_dt'      =>$item->begin_dt,
//                'time'          =>$item->time,
//                'score'         =>$item->score,
//                'evaluate'      =>$item->evaluate,
//                'patient'       =>$item->patient,
//                'affinity'      =>$item->affinity,
//                'station_id'    =>$item->station_id,
//                'subject_title' =>$item->subject_title,
//                'subject_id'    =>$item->subject_id,
//                'original_score' =>$item->original_score,
//            ];
//        }
//        dd($result);
        //查询成绩详情
        $score = ExamScore::where('exam_result_id', '=', $id)->where('subject_id', '=', $result['subject_id'])->get();
        //查询特殊评分项成绩详情
        $ExamSpecialScore = new ExamSpecialScore();
        $specialScore = $ExamSpecialScore->getSpecialScoreBySubject($id, $result['subject_id']);

        //TODO: fandian
        $scores     = [];   //用于保存返回到页面的成绩
        $itemScore  = [];   //用于保存该考试项目下，每个考核点的总分
        foreach($score as $itm)
        {
            $pid = $itm->standardItem->pid;     //获取每个考核项的pid(对应每个考核点的ID)

            $scores[$pid]['items'][] = [
                'standard'  => $itm->standardItem,
                'score'     => $itm->original_score,
                'image'     => TestAttach::where('test_result_id',$result['id'])->where('standard_item_id',$itm->standardItem->id)->get(),
            ];
            //将每个考核点下对应的考核项分数，加起来，求总和（考核点分数总和），（$itm->score 为每个考核项的分数）
            $itemScore[$pid]['totalScore'] = (isset($itemScore[$pid]['totalScore'])? $itemScore[$pid]['totalScore']:0) + $itm->original_score;
        }

        $standard = [];     //考核点成绩曲线
        $avg      = [];     //平均成绩曲线数据
        $standardItem =new StandardItem();
        foreach ($scores as $index => $item) {
            //获取考核点信息
            $standardM = StandardItem::where('id', '=', $index)->first();
            $scores[$index]['sort']     = $standardM->sort;
            $scores[$index]['content']  = $standardM->content;
            $scores[$index]['tScore']   = $standardM->score;
            $scores[$index]['score']    = $itemScore[$index]['totalScore'];
            $scores[$index]['image']    = TestAttach::where('test_result_id',$result['id'])->where('standard_item_id',$index)->get();

            $standard[$index] = $itemScore[$index]['totalScore'];
            $avg[$index]      = $standardItem->getCheckPointAvg($index, $result['subject_id']);
        }

        return view('osce::admin.examManage.score_query_detail')->with(
            [
                'flag'   => $request->get('flag'),
                'result' => $result,    'standard'      => $standard,   'avg'   => $avg,
                'scores' => $scores,    'specialScore'  => $specialScore
            ]);
    }

    /**
     *下载图片
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        考试结果附件id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getDownloadImage(Request $request){
        $this->validate($request,[
            'id'            =>'required|integer',
        ]);
        $id     =   $request->get('id');
        $info  =   TestAttach::find($id);
        $attchments =  $info->url;
        $fileNameArray   =  explode('/',$attchments);
        $this->downloadfile(array_pop($fileNameArray),public_path().'/'.$attchments);
    }
    private function downloadfile($filename,$filepath){
        $file=explode('.',$filename);
        $tFile=array_pop($file);
        $filename=md5($filename).'.'.$tFile;
//        $filepath   =   iconv('utf-8', 'gbk', $filepath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);

    }

    /**
     * 视频页面的着陆页
     * @param Request $request
     * @author ZouYuChao
     * @return \Illuminate\View\View
     */
    public function getResultVideo(Request $request)
    {
        try {
            $this->validate($request,[
                'exam_id' => 'required|integer',
                'student_id' => 'required|integer',
                'station_id' => 'required|integer'
            ]);
            //获取数据
            $examId = $request->input('exam_id');
            $studentId = $request->input('student_id');
            $stationId = $request->input('station_id');
            //根据考试id拿到场次id临时修改
            $examScreeningId = ExamScreening::where('exam_id','=',$examId)->select('id')->get()->pluck('id');

            //更据考站id查询到
            $stationVcr = StationVcr::where('station_id','=',$stationId)->first();
            if(is_null($stationVcr)){
                throw new \Exception('没有找到相关联的摄像机');
            }
            $stationVcrId = $stationVcr->id;
            //查询到页面需要的数据
            $data = StationVideo::label($examId,$studentId,$stationId,$examScreeningId);
            //查询出时间锚点追加到数组中
            $anchor = StationVideo:: getTationVideo($examId, $studentId, $stationVcrId);
            return view('osce::admin.statisticalAnalysis.exam_video',['data'=>$data,'anchor'=>$anchor]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    //下载安装包
    public function getDownloadComponents(){
        $this->downloadComponents('WebComponents.exe',public_path('download').'/WebComponents.exe');
    }

    private function downloadComponents($filename,$filepath){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }


    /**
     *ajax请求获取当前考试下的考站
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_id        考试id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamStationList(Request $request){
        $this->validate($request,[
           'exam_id'  =>'sometimes',
        ]);
        $data=[];
        $exam_id=$request->get('exam_id');
        if($exam_id){
            //拿到考试下所有的考站
            $stationIds = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->select(
                'exam_draft.station_id as station_id'
            )
            ->get();
            foreach($stationIds as $station){
                $data[]=$station->station;
            }
        }else{
            $stationIds = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->select(
                    'exam_draft.station_id as station_id'
                )
                ->get();
            foreach($stationIds as $station){
                $data[]=$station->station;
            }
        }
        return response()->json(
            $this->success_data($data,1,'success')
        );
    }

    /**
     * 导出成绩Excel
     * @url   /osce/admin/exam/export-score
     * @param Request $request
     * @param StudentScoreExport $export
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-25 10:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExportScore(Request $request, StudentScoreExport $export)
    {
        try{
            $this->validate($request, [
                'exam_id'   => 'required',
            ]);

            //获取考试ID
            $exam_id = intval($request->get('exam_id'))? : null;
            $exam    = Exam::doingExam($exam_id);

            $ExamResult = new ExamResult();
            $datas   = $ExamResult->getAllResult($exam_id);     //获取成绩数据

            //《一》转为数组
            list($data, $grade_class)    = $export->objToArray($datas, $exam_id);
            //按学年制分组
            $ExlData = [];
            foreach ($data as $item){
                $ExlData[$item[0]][0]  = $data[0];      //取表头数据
                $ExlData[$item[0]][]   = $item;
            }

            //《二》
//        $data2   = $export->objToArray2($datas, $exam_id);
//        $ExlData = [];
//        foreach ($data2 as $item){
//            $ExlData[$item[11]][0]  = $data2[0];
//            $ExlData[$item[11]][]   = $item;
//        }

            Excel::create($exam->name, function($excel) use ($ExlData, $grade_class)
            {
                foreach ($grade_class as $grade_clas)
                {
                    $excel->sheet($grade_clas, function($sheet) use ($ExlData, $grade_clas){
                        $sheet->setWidth(config('osce.student_score.width'));
                        $sheet->rows($ExlData[$grade_clas]);
                    });
                }
            })->export('xls');

//            Excel::create($exam->name, function($excel) use ($ExlData){
//                $excel->sheet('2011级五年制', function($sheet) use ($ExlData){
//                    $sheet->setWidth(config('osce.student_score.width'));
//                    $sheet->rows($ExlData['2011级五年制']);
//                });
//                $excel->sheet('2009级八年制', function($sheet) use ($ExlData){
//                    $sheet->setWidth(config('osce.student_score.width'));
//                    $sheet->rows($ExlData['2009级八年制']);
//
//                });
//                $excel->sheet('2009级创新班', function($sheet) use ($ExlData){
//                    $sheet->setWidth(config('osce.student_score.width'));
//                    $sheet->rows($ExlData['2009级创新班']);
//
//                });
//
//            })->export('xls');

//        return  $export->sheet('StudentScore', function ($sheet) use ($data2)
//                {
//                    $sheet->setWidth(config('osce.student_score.width'));
//                    $sheet->rows($data2);
//                })
//                ->export('xlsx');

        }catch (\Exception $ex)
        {
            return redirect()->back()->withErrors(['code'=>$ex->getCode(), 'msg'=>$ex->getMessage()]);
        }
    }


    //成绩汇总导出
    public function getExportAllScore(Request $request){

        $this->validate($request, [
            'exam_id' => 'integer'
        ]);
        $examId = '';
        $message = '';
        $examDownlist = Exam::select('id', 'name')->where('exam.status', '<>', 0)->where('pid','=',0)->orderBy('begin_dt', 'desc')->get();
        //获得最近的考试的id
        $lastExam = Exam::orderBy('begin_dt', 'desc')->where('exam.status', '<>', 0)->where('pid','=',0)->first();

        if (is_null($lastExam)) {
            $list = [];
        } else {
            $lastExamId = $lastExam->id;
            //获得参数
            $examId = $request->input('exam_id', $lastExamId);
            list($screening_ids, $elderExam_ids) = ExamScreening::getAllScreeningByExam($examId);
            //获得学生的列表在该考试的列表
            $list = Student::getStudentScoreList($screening_ids,"");
        }
        //查询一下有没有理伦考试
        $testlogs = TestLog::where('exam_id',$examId)->first();
        if(!empty($testlogs)){
            $lgid = $testlogs->id;
            $TestStatistics = TestStatistics::where('logid',$lgid)->get();
        }
        $arr = [];
        $newlist = [];
        if(!empty($TestStatistics)){
            foreach($TestStatistics as $k=>$v){
                $arr[$v->stuid] = $v->objective."#".$v->subjective;
            }
        }

        foreach($list as $k=>$v){
            $newlist[$k]["student_name"] = $v->student_name;
            $newlist[$k]["student_code"] = $v->student_code;
            $newlist[$k]["exam_name"] = $v->exam_name;
            $newlist[$k]["score_total"] = $v->score_total;
            $newlist[$k]["student_id"] = $v->student_id;
            if(!empty($arr[$v->student_id])) {
                $theory = explode("#", $arr[$v->student_id]);
                $newlist[$k]["station_total"] = $v->station_total+1;
            }else{
                $theory = [0,0];
                $newlist[$k]["station_total"] = $v->station_total;
            }
            $newlist[$k]["objective"] = $theory[0];
            $newlist[$k]["subjective"] = $theory[1];
            if(!empty($testlogs)) {
                $newlist[$k]["score_theory"] = ($newlist[$k]["objective"] + $newlist[$k]["subjective"]);
            }else{
                $newlist[$k]["score_theory"] = "无";
            }
            $newlist[$k]["score_all"] = $newlist[$k]["objective"]+$newlist[$k]["subjective"]+$v->score_total;
        }
        $hello =[['姓名','学号','考试名称','考站数','技能考试总成绩','理伦考试总成绩','总分']];
        $i = 1;
        foreach($newlist as $v){
           $hello[$i] =[$v['student_name'],$v['student_code'],$v['exam_name'],$v['station_total'],$v['score_total'],$v['score_theory'],$v['score_all']];
            $i++;
        }
        Excel::create(iconv('UTF-8', 'GBK//ignore', '成绩汇总'),function($excel) use ($hello){
            $excel->sheet('score', function($sheet) use ($hello){
                $sheet->rows($hello);
            });
        })->export('xls');
    }






    //科目成绩的导出
    //url  /osce/admin/exam/subject-score
    public function  getSubjectScore(StudentScoreExport $export){
        $examId = 3;
        $exam = Exam::find($examId);
       //拿到科目下的数据
        $StudentModel = new ExamResult();
        $subjectScore =$StudentModel-> getAllResult($examId);
        //组装表头
        $header= [];
        //组装表头下的数据

        foreach ($subjectScore as $key=> $value){
            if(empty($value->subject_name)){
                $value->subject_name = '理论考试';
            }
            $header[$value->subject_name][$value->student_id][0]= $value->grade_class;
            $header[$value->subject_name][$value->student_id][1]= $value->code;
            $header[$value->subject_name][$value->student_id][2]= $value->student_name;
            $header[$value->subject_name][$value->student_id][3]= $value->subject_name;
            $header[$value->subject_name][$value->student_id][4]= $value->score;
            $header[$value->subject_name][0]= [
                '学年制',
                '学号',
                '学生姓名',
                '科目',
                '成绩',
            ];
            ksort($header[$value->subject_name]);
        }
            Excel::create($exam->name, function($excel) use ($header){
                foreach ($header as $key =>$item){
                $excel->sheet($key, function($sheet) use ($item){
                    $sheet->setWidth(config('osce.student_score.width'));
                    $sheet->rows($item);
                });
                }

            })->export('xls');
    }





}