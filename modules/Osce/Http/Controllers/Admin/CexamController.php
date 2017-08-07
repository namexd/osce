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
use Modules\Osce\Entities\TestLog;
use Modules\Osce\Entities\TestStatistics;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\Cexam;
class CexamController extends CommonController
{

    /** 新增考试
     * @method GET
     */

    public function addaExame(Request $request)
    {
        $dataArray=$request->only('exam_id','tid','start','end','teacher','times');

        $isHas = TestLog::where('start','<',$dataArray['start'])->where('end','>',$dataArray['start'])->orWhere(function ($query)use ( $dataArray )  {
            $query->where('start', '<', $dataArray['end'])
                ->where('end', '>', $dataArray['end']);
        })->first();
        if(empty($isHas)){
            $addArray = [
                'exam_id'=>$dataArray['exam_id'],
                'tid' =>$dataArray['tid'],
                'start' =>$dataArray['start'],
                'end' =>$dataArray['end'],
                'teacher' =>$dataArray['teacher'],
                'times' =>$dataArray['times'],
                'status' =>0
            ];
            TestLog::create($addArray);
            return redirect()->route('osce.theory.index')->withErrors('1新增成功');
        }else{
            return redirect()->route('osce.theory.index')->withErrors('新增失败，当前考试时间与其他考试时间冲突');
        }
    }
    /** 登入考试
     * @method GET
     */
    public function cexamQuestion(Request $request)
    {

    }





    /** 查询考试
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchExameInfo(Request $request)
    {
        $log_id=$request->get('testlog_id');
        $result = TestLog::find($log_id);
        if($result){
            //dd($result->times*60);
            $endTime =strtotime(session('enterTime'))+($result->times*60)-time();
            if($endTime<0){
                \Auth::logout();
                return redirect()->back()->withErrors('考试时间已经结束！');
            }
            return view('osce::theory.exam_online', ['data' =>$result,'endtime'=>$endTime]);
        }else{
            return redirect()->back()->withErrors('参数有误！');
        }
    }



    /** 提交答案
     * @method GET
     */

    public function addExameResult(Request $request)
    {
        $dataArray=$request->only('logid','cid','answer','type');
        $dataArray['stuid'] = \Auth::user()->id;
        $dataArray['time'] = time();
        $exam = new Cexam();


        $score = 0;

        $cids    = $dataArray['cid'];
        $answers = $dataArray['answer'];


        $ifadd = $exam->ifadd($dataArray);

        if($ifadd){
            return view('osce::theory.theory_login')->withErrors('您已提交过了 请勿重复提交');
        }

        //增加到表g_test_statistics
        $dataArray['userid'] =$dataArray['stuid'];
        $dataArray['id'] =$dataArray['logid'];
        $exam-> stunowexam($dataArray);

        //写答案
        for($i=0;$i<count($cids);$i++){
            $addArray = [
                'logid'  =>$dataArray['logid'],
                'stuid'  =>$dataArray['stuid'],
                'cid'  =>$cids[$i],
                'answer'  =>$answers[$i],
                'type'   =>$dataArray['type'][$i]

            ];

            $result= $exam->addexamresult($addArray);


            if($result['code']==1){
                $score+= $exam->objectResult($result);
            }

        }

        $dataArray['objective']=$score;

        //数据统计
        $exam->addstatics($dataArray);


        //$info = $this->rmsg($result['code'],$result['msg']);

        \Auth::logout();
        return view('osce::theory.theory_login')->withErrors('1答卷成功');

    }




    /** 查询某个学生试卷的具体信息
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchExamREesult(Request $request)
    {
        $dataArray=$request->only('logid','userid');

        $exam = new Cexam();

        $result= $exam->searchExamDetail($dataArray['logid'],$dataArray['userid']);


        return view('osce::theory.searchexamdetail',['data'=>$result]);


    }


    /** 批改试卷提交
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function modifyExamREesult(Request $request)
    {
        $dataArray=$request->only('id','isright','poins','logid','stuid');


        $exam = new Cexam();

        $score=0;
        for($i=0;$i<count($dataArray['id']);$i++){

            $score+=$dataArray['poins'][$i];
            $addArray = [
                'id' =>     $dataArray['id'][$i],
                'isright' =>     $dataArray['isright'][$i],
                'poins' =>     $dataArray['poins'][$i],
                'logid' =>     $dataArray['logid'],
            ];
            $result= $exam->updateExamDetail($addArray);

        }

        $sysarray['subjective']=$score;
        $sysarray['logid']=$dataArray['logid'];
        $sysarray['stuid']=$dataArray['stuid'];
        //更新统计表里的分数
        $exam ->updatestatics($sysarray);
        $info = $this->rmsg(1,'成功');

        return $info;
    }






    /**查询学生成绩
     * @method GET
     * @url   fatherdepart
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * @return view
     *
     * @version 1.0
     * @author zhochong <zouyuchao@misrobot.com>
     * @date 2016-5-12 14:05
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     * @
     */

    public function searchResultList(Request $request)
    {
        $dataArray=$request->only('userid','department_id','type','usertype','starttime','endtime','page','pagecount');

        $this->gyinput($dataArray);

        $page = $dataArray['page'];

        $pagecount = $dataArray['pagecount'];

        $exam = new Cexam();

        $user =new User();

        $result=[];

        if($dataArray['usertype']==1){

            $result=$exam->searchstudent($dataArray);


        }else if($dataArray['usertype']==2){

            $departArray = $exam->seachuserdeparts($dataArray['userid']);

            $searchnews['starttime']=$dataArray['starttime'];
            $searchnews['endtime']=$dataArray['endtime'];
            $searchnews['type']=$dataArray['type'];

            for($i=0;$i<count($departArray);$i++){

                $searchnews['department_id']=$departArray[$i]->id;

                $searchinfo=$exam-> departscorelist($searchnews);

                for($j=0;$j<count($searchinfo);$j++){

                    $stuname = $user->searchUserName($searchinfo[0]->stuid);

                    $searchinfo[$j]->stuname= $stuname[0]->name;

                    $result[]=$searchinfo;

                }

            }

        }else{


            $result=  $exam->glyscorelist($dataArray);

        }

        $info = $this->paginationWay($result,$page,$pagecount);

        return $info;
    }


// 成绩列表



}
