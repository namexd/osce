<?php
/**
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 21:01
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Repositories\Common;
use DB;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\SubjectCases;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Entities\SubjectSupply;
use Modules\Osce\Entities\Supply;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common as OsceCommon;
class TopicController extends CommonController
{
    /**
     * 获取课题列表
     * @url /osce/admin/topic/list
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE}${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getList(Request $request)
    {
        $this->validate($request,[
            'name'     => 'sometimes',    //名称
        ]);
        $name = e($request->get('name'));
        $Subject = new Subject;
        $list = $Subject->getList($name);
        return view('osce::admin.resourceManage.course_manage', ['list' => $list, 'name' => $name]);
    }

    /**
     * 新增课题表单
     * @url /osce/admin/topic/add-topic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE}${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getAddTopic()
    {
        //获得上次的时间限制
        $time = session('time');
        //模板下载路径
        $tempUrl = '/download/topic.xlsx';
        return view('osce::admin.resourceManage.course_manage_add', ['time'=>$time, 'tempUrl'=>$tempUrl]);
    }

    /**
     * 新增课题（考核点的盒子）
     * @url /osce/admin/topic/add-topic
     * @access public
     *
     * * @param Request $request
     * <b>post 请求字段：</b>
     * * string         title        课题名称(必须的)
     * * int            score        课题总分(必须的)
     * * int            order        课题排序(必须的)
     * * int            status       课题状态(必须的)
     *
     * @return redirect
     *
     * @version 3.4
     * @author fandian <fandian@sulida.com>
     * @date 2016-03-30 21:35
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function postAddTopic(Request $request)
    {
        $this->validate($request, [
            'title'         => 'required|unique:osce_mis.subject,title',    //名称
            'mins'          => 'required',      //时间限制
            'cases'         => 'required',      //病例
            'total'         => 'required',      //总分
            'desc'          => 'sometimes',     //描述
            'special_score' => 'sometimes',     //特殊评分项
            'goods'         => 'sometimes',     //所需用物
            'content'       => 'required',      //评分标准
            'score'         => 'required',      //考核点、考核项分数
            'description'   => 'required',      //考核项下的评分标准
            'rate_choose'   => 'required',      //折算方式选择
            'rate_score'    => 'required',      //折算分
        ], [
            'title.required'        => '名称必填',
            'title.unique'          => '该科目已存在',
            'cases.required'        => '请选择病例',
            'total.required'        => '总分必填',
            'mins.required'         => '必须填写时间限制',
            'content.required'      => '必须新增评分点',
            'score.required'        => '分数必填',
            'description.required'  => '请添加考核项',
            'rate_choose.required'  => '折算方式未选择',
            'rate_score.required'   => '折算分是必须的',
        ]);

        $content = $request->get('content');        //评分标准（所有内容）
        $score   = $request->get('score');          //考核点、考核项对应的分数
        $answer  = $request->get('description');    //考核项下面的评分标准
        $cases   = $request->input('cases');        //病例
        $goods   = $request->input('goods');        //用物
        $speScore= $request->get('special_score');  //特殊评分项
        $speflag = $request->input('special_score_flag');  //特殊评分项标记
        if($speflag == 0){
            $speScore = [];
        }

        try {
            $user = \Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }

            //处理评分标准数据（数据组合）
            $formData  = SubjectItem::builderItemData($content, $score, $answer);
            $totalData = 0;
            //获取评分标准中考核点分数总和
            foreach ($score as $index => $socrdata) {
                foreach ($socrdata as $key => $socre) {
                    if ($key == 'total' || $key == 'rate') {
                        continue;
                    }
                    $totalData += $socre;
                }
            }
            $data = [
                'title'             => e($request->get('title')),               //考试项目名称
                'mins'              => $request->get('mins'),                   //时间限制
                'score'             => intval($request->get('total')),          //总分
                'rate_score'        => floatval($request->get('rate_score')),     //折算分
                'description'       => e($request->get('desc')),                //描述
                'stem'              => e($request->input('stem')),              //题干
                'created_user_id'   => $user->id,
                'rate_choose'       => intval($request->get('rate_choose')),    //折算方式标记
            ];

            //判断总分与考核项分数是否正确
            if($totalData != $data['score']){
                throw new \Exception('考核项分数和 没有对应总分！');
            }

            //将当前时间限定的值放入session
            $time = $request->input('mins');
            $request->session()->put('time', $time);
            if (!$request->session()->has('time')) {
                throw new \Exception('未能将时间保存！');
            }

            $subjectModel = new Subject();
            //添加考试项目
            $result = $subjectModel->addSubject($data, $formData, $cases, $speScore, $goods, $user->id);
            if (!$result) {
                throw new \Exception('新增失败！');
            }

            //todo 调用弹窗时新增的跳转 周强 2016-4-13
            $Redirect = OsceCommon::handleRedirect($request,$result);
            if($Redirect == false){
                return redirect()->route('osce.admin.topic.getList');
            }else{
                return $Redirect;
            }

        } catch (\Exception $ex) {

            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }

    }

    /**
     * 编辑课题
     * @url /osce/admin/topic/edit-topic
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * int            id           课题ID(必须的)
     * * string         title        课题名称(必须的)
     * * int            score        课题总分(必须的)
     * * int            order        课题排序(必须的)
     * * int            status       课题状态(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE}${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function postEditTopic(Request $request)
    {
        $this->validate($request, [
            'id'            => 'required',
            'title'         => 'required',      //名称
            'mins'          => 'required',      //时间限制
            'cases'         => 'required',      //病例
            'total'         => 'required',      //总分
            'desc'          => 'sometimes',     //描述
            'special_score' => 'sometimes',     //特殊评分项
            'goods'         => 'sometimes',     //所需用物
            'content'       => 'required',      //评分标准
            'score'         => 'required',      //考核点、考核项分数
            'description'   => 'required',      //考核项下的评分标准
            'rate_choose'   => 'required',      //折算方式选择
            'rate_score'    => 'required',      //折算分
        ], [
            'id.required'           => '课题ID必须填写',
            'title.required'        => '名称必填',
            'mins.required'         => '必须填写时间限制',
            'cases.required'        => '请选择病例',
            'total.required'        => '总分必填',
            'content.required'      => '必须新增评分点',
            'score.required'        => '分数必填',
            'description.required'  => '请添加考核项',
            'rate_choose.required'  => '折算方式未选择',
            'rate_score.required'   => '折算分是必须的',
        ]);

        //考试项目基础数据
        $data = [
            'title'       => e($request->get('title')),
            'mins'        => $request->get('mins'),
            'description' => $request->get('desc'),
            'score'       => $request->input('total'),
            'rate_score'  => floatval($request->get('rate_score')),
            'rate_choose' => intval($request->get('rate_choose')),
        ];
        $id      = intval($request->get('id'));
        $content = $request->get('content');        //评分标准（所有内容）
        $score   = $request->get('score');          //考核点、考核项对应的分数
        $answer  = $request->get('description');    //考核项下面的评分标准
        $cases   = $request->input('cases');        //病例
        $goods   = $request->input('goods');        //用物
        $speScore= $request->get('special_score');  //特殊评分项
        $speflag = $request->input('special_score_flag');  //特殊评分项标记
        if($speflag == 0){
            $speScore = [];
        }
        
        $subjectModel = new Subject();
        try {
            //处理评分标准数据（数据组合）
            $formData = SubjectItem::builderItemData($content, $score, $answer);
            $totalData = 0;
            foreach ($score as $index => $socrdata) {
                foreach ($socrdata as $key => $socre) {
                    if ($key == 'total' || $key == 'rate') {
                        continue;
                    }
                    $totalData += $socre;
                }
            }
            //判断总分与考核项分数是否正确
            if($totalData != $data['score']){
                throw new \Exception('考核项分数之和与总分不相等！');
            }

            //编辑考试项目
            if (!$subjectModel->editTopic($id, $data, $formData, $cases, $speScore, $goods)) {
                throw new \Exception('编辑失败');
            }

            return redirect()->route('osce.admin.topic.getList');

        } catch (\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 编辑考核标准表单
     * @url /osce/admin/topic/edit-topic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        id        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE}${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getEditTopic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $id = $request->get('id');
        $subject = Subject::where('id','=',$id)->with('cases')->with('supplys')->with('specialScores')
                    ->with(['standards'=>function($q){
                        $q->with('standardItem');
                    }])->first();
        OsceCommon::valueIsNull($subject, -1000, '没有找到对应的科目');

        $standards = $subject->standards->first();
        if (is_null($standards) || is_null($standards->standardItem)){
            $items = [];
        }else{
            //处理 评分标准 数据
            $items = StandardItem::builderItemTable($standards->standardItem);
        }
        $prointNum = 1;
        $optionNum = [0 => 0];

        foreach ($items as $item) {
            if ($item->pid == 0) {
                $prointNum++;
            } else {
                if (array_key_exists($item->pid, $optionNum)) {
                    $optionNum[$item->pid]++;
                } else {
                    $optionNum[$item->pid] = 1;
                }
            }
        }

        //获取考试项目——用物关系数据
        $subjectSupplys = SubjectSupply::where('subject_id','=',$id)->with('supply')->get();
        //模板下载路径
        $tempUrl = '/download/topic.xlsx';

        return view('osce::admin.resourceManage.course_manage_edit',
        [
            'item' => $subject, 'list' => $items, 'prointNum' => $prointNum, 'optionNum' => $optionNum,
            'subjectSupplys' => $subjectSupplys, 'tempUrl' => $tempUrl
        ]);
    }

    /**
     *
     * @url GET /osce/admin/topic/del-topic
     * @access public
     *
     * <b>get 请求字段：</b>
     * * string        id        考核标准ID(必须的)
     *
     * @return
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date ${DATE}${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getDelTopic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $id = $request->get('id');
        $SubjectModel = new Subject();
        $subject = $SubjectModel->find($id);
        try {
            //删除考试项目
            $SubjectModel->delSubject($subject);
            return response()->json(
                $this->success_data([],1,'删除成功！')
            );

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     *
     * @url /osce/admin/topic/import-excel
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     *
     *
     * @param Request $request
     * @version 1.0
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function postImportExcel(Request $request)
    {
        try {
            $data = Common::getExclData($request, 'topic');
            $topicList = array_shift($data);

            //判断模板表头是否有误
            $this->judgeTemplet($topicList);

            //将中文表头，按配置翻译成英文的字段名
            $data = Common::arrayChTOEn($topicList, 'osce.importForCnToEn.standard');
            $totalScore = -1;
            foreach ($data as $key => &$items) {
                foreach ($items as &$item) {
                    $item = e($item);
                }

                /*判断分数 TODO: fandian 2016-2-26*/
                if (!strpos($items['sort'], '-')) {
                    if ($key != 0 && $totalScore != 0) {
                        throw new \Exception('分数有误，请修改后重试');
                    }
                    $sort = intval($items['sort']);
                    $totalScore = intval($items['score']);
                } else {
                    if (!isset($sort)) {
                        throw new \Exception('模板有误，请修改后重试');
                    }
                    $sonSort = intval(substr($items['sort'], 0, strpos($items['sort'], '-')));

                    if ($sort == $sonSort) {
                        $totalScore = $totalScore - intval($items['score']);
                        if ($key + 1 == count($data) && $totalScore != 0) {
                            throw new \Exception('分数有误，请修改后重试');
                        }
                    }
                }
            }

            return json_encode($this->success_data($data));
        } catch (\Exception $ex) {
            return json_encode($this->fail($ex));
        }
    }

    /**
     * 判断科目模板表头及列数 TODO: fandian 2016-2-27
     */
    public function judgeTemplet($topicList)
    {
        try {
            $standard = ['序号', '考核点', '考核项', '评分标准', '分数'];
            foreach ($topicList as $key => $value) {
                //模板列数
                if (count($value) != 5) {
                    throw new \Exception('模板列数有误');
                }
                foreach ($value as $index => $item) {
                    if (!in_array($index, $standard)) {
                        throw new \Exception('模板表头有误');
                    }
                }
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 下载考核点导入模板
     * @url GET /osce/admin/topic/toppic-tpl
     * @access public
     *
     *
     * @return void
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2015-12-29 17:09
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getToppicTpl()
    {
        $this->downloadfile('topic.xlsx', public_path('download') . '/topic.xlsx');
    }

    private function downloadfile($filename, $filepath)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }


    /**
     * 判断名称是否已经存在
     * @url POST /osce/admin/resources-manager/postNameUnique
     * @author fandian <fandian@sulida.com>     *
     */
    public function postNameUnique(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $id = $request->get('id');
        $name = $request->get('title');

        //实例化模型
        $model = new Subject();
        //查询 该名字 是否存在
        if (empty($id)) {
            $result = $model->where('title', $name)->first();
        } else {
            $result = $model->where('title', $name)->where('id', '<>', $id)->first();
        }
        if ($result) {
            return json_encode(['valid' => false]);
        } else {
            return json_encode(['valid' => true]);
        }
    }




    // 考试项目获取病例数据
   public  function getSubjectCases(Request $request){
       $this->validate($request,[
           'cases_name'=>'sometimes',

       ]);
       $caseName = $request->get('cases_name');
//       $paginate = $request->get('paginate');
       try{
          
           $caseModel = new CaseModel();

           //查询出所有的病例
           $casesList = $caseModel->getCasesList($caseName);
           
//           return response()->json(
//               $this->success_rows(1,'success',$pagination->total(),$pagesize=config('msc.page_size'),$pagination->currentPage(),$data)
//           );
           return response()->json(
               $this->success_data($casesList, 1, '病例获取成功')
           );
       }catch (\Exception $ex){
           return response()->json($this->fail($ex));

       }
}



    //获取用物接口
    public  function getSubjectSupply(Request $request){
            $this->validate($request,[
               'name'=>'sometimes'
            ]);

        $name = e($request->get('q'));

        try{
            $caseModel = new Supply();
            
                //查询出所有的病例
                $supplyList = $caseModel->getSupplyList($name);

            return response()->json(
                $this->success_data($supplyList, 1, '病例获取成功')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));

        }

    }



}