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
use Modules\Osce\Entities\Cexam;
class CexamController extends CommonController
{

    /** 新增考试
     * @method GET
     */

    public function addaExame(Request $request)
    {
        $dataArray=$request->only('exam_id','tid','start','end','teacher');


        $exam = new Cexam();

        $result= array();

/*        $zz= $exam-> iftimeyiyang($dataArray);


        if($zz['code']!=1){

            $info = $this->rmsg($zz['code'],$zz['msg']);

            return $info;
        }*/


            $addArray = [
                'exam_id'=>$dataArray['exam_id'],
                'tid' =>$dataArray['tid'],
                'start' =>$dataArray['start'],
                'end' =>$dataArray['end'],
                'teacher' =>$dataArray['teacher']
            ];

            $result = $exam->ifcanaddscore($addArray);



        $info = $this->rmsg($result['code'],$result['msg']);

        return $info;
    }


    /** 查询负责考试的考试信息列表
     * @method GET
     */

    public function searchExameList(Request $request)
    {

        $exam = new Cexam();

        $dataArray=$request->only('id','type','usertype','department_id','page','pagecount');


        $page = $dataArray['page'];

        $pagecount = $dataArray['pagecount'];

        $result = $exam->searchscorelist($dataArray);

        $info = $this->paginationWay($result,$page,$pagecount);

        return $info;
    }


    /** 查询负责考试的考试信息列表
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

    public function searchDepartexamList(Request $request)
    {

        $exam = new Cexam();

        $dataArray=$request->only('id','page','pagecount');

        $page = $dataArray['page'];

        $pagecount = $dataArray['pagecount'];

        $result = $exam->searchdepartexamlist($dataArray);

        $info = $this->paginationWay($result,$page,$pagecount);

        $info['nowtime']=time();

        return $info;
    }

    /** 查询负责考试的考试信息列表
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

    public function searchModelExam(Request $request)
    {

        $exam = new Cexam();

        $dataArray=$request->only('userid');

        $result = $exam->searchModelExamNews($dataArray);

        return $result;
    }



    /** 查询当前轮转科室里的学生信息
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

    public function searchDepartStudents(Request $request)
    {
        $dataArray=$request->only('department_id','logid');

        $exam = new Cexam();

        $result = $exam->searchDepartStudents($dataArray);


        return $result;
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
        $dataArray=$request->only('id');

        $exam = new Cexam();

        $result = $exam->searchscoreinfos($dataArray);

        if(count($result)==0){
            $info = $this->rmsg(1,'考试内容为空');

            return $info;
        }

        return $result;
    }


    /** 确认学生能参加考试
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

    public function sureuserExame(Request $request)
    {
        $dataArray=$request->only('id','logid');

        $exam = new Cexam();

        $result = $exam->modifyuserexamstatus($dataArray);

        $info = $this->rmsg($result['code'],$result['msg']);

        return $info;
    }
    /** 开始考试
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

    public function startExame(Request $request)
    {
        $dataArray=$request->only('id','userid');

        $exam = new Cexam();

        $ifexam= $exam->searchuserexamstatus($dataArray);


        if(count($ifexam)==0) {
            $info['status']= 3;
            $info['msg']= '此学生不能参加考试,请等待老师确认';
            $info['data']=[];
            return $info;
        }else{

            $result = $exam->searchscorequestion($dataArray);

            if(count($result)==0) {
                $info['status']= 2;
                $info['msg']= '考试内容为空';
                $info['data']=[];

                return $info;
            }


            $info['status']= 1;
            $info['msg']= '可以考试';
            $info['datas']=$result;
            $info['nowtime']=time();


            $dataArray['status'] = 2;
            $exam-> stunowexam($dataArray);

            return $info;
        }

    }

    /** 提交答案
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

    public function addExameResult(Request $request)
    {
        $dataArray=$request->only('logid','stuid','cid','answer','time','type');

        $this->gyinput($dataArray);

        $exam = new Cexam();

        $result ='';

        $score = 0;

        $cids    = $dataArray['cid'];
        $answers = $dataArray['answer'];


        $ifadd = DB::table('g_test_statistics')
            ->where('g_test_statistics.logid',$dataArray['logid'])
            ->where('g_test_statistics.stuid',$dataArray['stuid'])
            ->where('g_test_statistics.ifexam',3)
            ->first();

        if($ifadd){

            $info = $this->rmsg(0,'您已提交过了 请勿重复提交');

            return $info;
        }

        //更新学生的答题状态
        $dataArray['status'] =3;
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


        $info = $this->rmsg($result['code'],$result['msg']);

        return $info;

    }

    /** 查询试卷答题人的列表
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

    public function searchUserREesult(Request $request)
    {

        //status、subjective、objective
        $dataArray=$request->only('logid','page','pagecount');

        $page = $dataArray['page'];

        $pagecount = $dataArray['pagecount'];

        $exam = new Cexam();

        $result= $exam->searchResultlist($dataArray['logid']);

        $info = $this->paginationWay($result,$page,$pagecount);

        return $info;

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

        return $result;

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

        $this->gyinput($dataArray);

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


    /**确认某次考试能看了
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

    public function sureScoreShow(Request $request)
    {
        $dataArray=$request->only('id');

        $exam = new Cexam();

        $result=$exam->updateExamCanShow($dataArray);

        if($result){
            $exam->addaveragescore($dataArray);
        }

        $info = $this->rmsg($result['code'],$result['msg']);

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





}
