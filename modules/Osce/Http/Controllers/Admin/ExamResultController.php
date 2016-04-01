<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/28 0028
 * Time: 10:32
 */
namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamRoom;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;

class ExamResultController extends CommonController{

    /**
     *
     * @method GET
     * @url /exam/result-exam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
//    public function getResultExam(Request $request){
//
//         $exams=Exam::select()->get();
//         return response()->json(
//           $this->success_data($exams,1,'success')
//         );
//
//    }

    /**
     *
     * @method GET
     * @url /exam/result-station
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
//    public function getResultStation(Request $request){
//         $stations=Station::select()->get();
//         return response()->json(
//            $this->success_data($stations,1,'success')
//        );
//    }

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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function geExamResultList(Request $request)
    {
        $this->validate($request,[
            'exam_id'     => 'sometimes',
            'station_id'  => 'sometimes',
            'name'        => 'sometimes',
        ]);

        $examId    = $request->get('exam_id');
        $stationId = $request->get('station_id');
        $name      = $request->get('name');

        //存在考试ID，根据考试ID查询对应的考站
        if(!empty($examId)){
            $examInfo = Exam::where('id', $examId)->select('sequence_mode')->first();
            if($examInfo->sequence_mode == 1){
                $examRoomIds = ExamRoom::where('exam_id', $examId)->select('room_id')->get()->pluck('room_id');
                $examStationIds = RoomStation::whereIn('room_id', $examRoomIds)->select('station_id')->get()->pluck('station_id');
            }else{
                $examStationIds = ExamStation::where('exam_id', $examId)->select('station_id')->get()->pluck('station_id');
            }
            $stations  = Station::whereIn('id',$examStationIds)->get();
        }else{
            $stations  = Station::select()->get();
        }

        $exams      = Exam::select()->get();
        $examResult = new ExamResult();
        $examResults= $examResult->getResultList($examId,$stationId,$name);
        foreach($examResults as $item){
//            date_default_timezone_set("UTC");
//            $item->time = date('H:i:s',$item->time);
//            date_default_timezone_set("PRC");
            $item->time = Common::handleTime($item->time);
        }
        return view('osce::admin.examManage.score_query')->with(['examResults'=>$examResults,'stations'=>$stations,'exams'=>$exams,'exam_id'=>$examId,'station_id'=>$stationId,'name'=>$name]);
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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamResultDetail(Request $request)
    {
       $this->validate($request,[
           'id'   => 'required|integer'
       ]);

        $id = $request->get('id');
        $examResult = new ExamResult();
        $examResult = $examResult->getResultDetail($id);
        $result = [];
        foreach($examResult as $item){
            $result = [
                'id' =>$item->id,
                'exam_name' =>$item->exam_name,
                'student'   =>$item->student,
                'teacher'   =>$item->teacher,
                'begin_dt'  =>$item->begin_dt,
                'time'      =>$item->time,
                'score'     =>$item->score,
                'evaluate'  =>$item->evaluate,
                'operation' =>$item->operation,
                'skilled'   =>$item->skilled,
                'patient'   =>$item->patient,
                'station_id'   =>$item->station_id,
                'affinity'  =>$item->affinity,
                'subject_title' =>$item->subject_title,
                'subject_id' =>$item->subject_id,
            ];
        }
//        date_default_timezone_set("UTC");
//        $result['time'] = date('H:i:s',$result['time']);
//        date_default_timezone_set("PRC");
        $result['time'] = Common::handleTime($result['time']);
        $score = ExamScore::where('exam_result_id',$id)->where('subject_id',$result['subject_id'])->get();

//        $image=[];
//        foreach($score as $itm){
//            $image[]=[
//                'standard'=>$itm->standard,
//                'score'=>$itm->score,
//                'image'=>'',
//            ];
//        }
//        $scores=[];
//        foreach($image as $img){
//            $scores[]=[
//                'standard'=>$img['standard'],
//                'score'=>$img['score'],
//                'image'=>TestAttach::where('test_result_id',$result['id'])->where('standard_id',$img['standard']->id)->select()->get(),
//            ];
//        }
//        $standard=[];
//        foreach($scores as $standards){
//            if($standards['standard']->pid==0){
//                $standard[]=ExamScore::where('exam_result_id',$id)->where('subject_id',$result['subject_id'])->where('standard_id',$standards['standard']->id)->select()->first()->score;
//            }
//        }
//        $standardModel=new Standard();
//        $totalScore=$standardModel->getScore($result['station_id'],$result['subject_id']);
//        if(is_null($totalScore)){
//            $sort=$totalScore[0]->sort;
//            $avg=[];
//            for($i=1;$i<=$sort;$i++){
//                $avg[]=$standardModel->getAvgScore($i,$result['station_id'],$result['subject_id']);
//            }
//        }else{
//            $avg=[0];
//        }


        //TODO: zhoufuxiang
        $scores = [];
        $itemScore = [];
        foreach($score as $itm){
            $pid = $itm->standard->pid;
            $scores[$pid]['items'][] = [
                'standard'  => $itm->standard,
                'score'     => $itm->score,
                'image'     => TestAttach::where('test_result_id',$result['id'])->where('standard_id',$itm->standard->id)->get(),
            ];
            $itemScore[$pid]['totalScore'] = (isset($itemScore[$pid]['totalScore'])? $itemScore[$pid]['totalScore']:0) + $itm->score;
        }

        $standard = [];
        $avg = [];
        $standardModel=new Standard();
        foreach ($scores as $index => $item) {
            //获取考核点信息
            $standardM = Standard::where('id', $index)->first();
            $scores[$index]['sort']     = $standardM->sort;
            $scores[$index]['content']  = $standardM->content;
            $scores[$index]['tScore']   = $standardM->score;
            $scores[$index]['score']    = $itemScore[$index]['totalScore'];
            $scores[$index]['image']    = TestAttach::where('test_result_id',$result['id'])->where('standard_id',$index)->get();

            $standard[$index] = $itemScore[$index]['totalScore'];
            $avg[$index] = $standardModel->getCheckPointAvg($index, $result['subject_id']);
        }

        return view('osce::admin.examManage.score_query_detail')->with(['result'=>$result,'scores'=>$scores,'standard'=>$standard,'avg'=>$avg]);

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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author Jiangzhiheng
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
            $stationVcrId = StationVcr::where('station_id','=',$stationId)->first()->id;
            if(is_null($stationVcrId)){
                throw new \Exception('没有找到相关联的摄像机');
            }
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
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamStationList(Request $request){
        $this->validate($request,[
           'exam_id'  =>'sometimes',
        ]);
        $data=[];
        $exam_id=$request->get('exam_id');
        if($exam_id){
            $stations=ExamStation::where('exam_id',$exam_id)->select()->get();
            foreach($stations as $station){
                $data[]=$station->station;
            }
        }else{
            $stations=ExamStation::select()->get();
            foreach($stations as $station){
                $data[]=$station->station;
            }
        }

        return response()->json(
            $this->success_data($data,1,'success')
        );
    }
}