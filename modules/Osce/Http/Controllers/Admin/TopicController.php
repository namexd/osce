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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList(Request $request)
    {
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddTopic()
    {
        return view('osce::admin.resourceManage.course_manage_add');
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
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 21:35
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddTopic(Request $request)
    {
        $this->validate($request, [
            'title'     => 'required|unique:osce_mis.subject,title',    //名称
            'cases'     => 'required',    //病例
            'total'     => 'required',    //总分
            'desc'      => 'required',    //描述
            'goods'     => 'sometimes',    //所需用物
//            'stem'      => 'required',    //题干
//            'equipments'=> 'required',    //所需设备
            'content'   => 'required',    //评分标准
            'score'     => 'required',    //考核点、考核项分数
            'description'=>'required',    //考核项下的评分标准
        ], [
            'title.required'    => '名称必填',
            'title.unique'      => '该科目已存在',
            'cases.required'    => '请选择病例',
            'total.required'    => '总分必填',
            'desc.required'     => '必须填写描述',
            'content.required'  => '必须新增评分点',
            'score.required'    => '分数必填',
            'description.required'   => '请添加考核项',
        ]);

        $content = $request->get('content');        //评分标准（所有内容）
        $score   = $request->get('score');          //考核点、考核项对应的分数
        $answer  = $request->get('description');    //考核项下面的评分标准

        try {
            $formData = SubjectItem::builderItemData($content, $score, $answer);
            $totalData = 0;
            foreach ($score as $index => $socrdata) {
                foreach ($socrdata as $key => $socre) {
                    if ($key == 'total') {
                        continue;
                    }
                    $totalData += $socre;
                }
            }

            $cases= $request->input('cases');           //病例
            $goods= $request->input('goods');           //用物

            $data = [
                'title'      => e($request->get('title')),
                'score'      => intval($request->get('total')),     //总分
                'description'=> e($request->get('desc')),           //描述
                'stem'       => e($request->input('stem')),         //题干
                'goods'      => '',                                 //所需物品
                'equipments' => e($request->input('equipments')),   //所需设备
            ];

            //判断总分与考核项分数是否正确
            if($totalData != $data['score']){
                throw new \Exception('考核项分数和 没有对应总分！');
            }

            $subjectModel = new Subject();
            if ($subjectModel->addSubject($data, $formData, $cases, $goods)) {

                return redirect()->route('osce.admin.topic.getList');

            } else {

                throw new \Exception('新增失败！');
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditTopic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'title'     => 'required',    //名称
            'cases'     => 'required',    //病例
            'total'     => 'required',    //总分
            'desc'      => 'required',    //描述
            'goods'     => 'sometimes',   //所需用物
//            'stem'      => 'required',    //题干
//            'equipments'=> 'required',    //所需设备
            'content'   => 'required',    //评分标准
            'score'     => 'required',    //考核点、考核项分数
            'description'=>'required',    //考核项下的评分标准
        ], [
            'id.required' => '课题ID必须填写',
            'title.required'    => '名称必填',
            'cases.required'    => '请选择病例',
            'total.required'    => '总分必填',
            'desc.required'     => '必须填写描述',
            'content.required'  => '必须新增评分点',
            'score.required'    => '分数必填',
            'description.required'   => '请添加考核项',
        ]);

        //考试项目基础数据
        $data = [
            'title'       => e($request->get('title')),
            'description' => $request->get('desc'),
            'stem'        => $request->input('stem'),
            'equipments'  => $request->input('equipments'),
            'goods'       => '',
            'score'       => $request->input('total')
        ];
        $id      = intval($request->get('id'));
        $content = $request->get('content');        //评分标准（所有内容）
        $score   = $request->get('score');          //考核点、考核项对应的分数
        $answer  = $request->get('description');    //考核项下面的评分标准
        $cases   = $request->input('cases');        //病例
        $goods   = $request->input('goods');        //用物

        $subjectModel = new Subject();
        try {
            $formData = SubjectItem::builderItemData($content, $score, $answer);
            $totalData = 0;
            foreach ($score as $index => $socrdata) {
                foreach ($socrdata as $key => $socre) {
                    if ($key == 'total') {
                        continue;
                    }
                    $totalData += $socre;
                }
            }
            //判断总分与考核项分数是否正确
            if($totalData != $data['score']){
                throw new \Exception('考核项分数之和与总分不相等！');
            }

            if ($subjectModel->editTopic($id, $data, $formData, $cases, $goods)) {

                return redirect()->route('osce.admin.topic.getList');
            } else {

                throw new \Exception('编辑失败');
            }

        } catch (\Exception $ex) {
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
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditTopic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $id = $request->get('id');
        $subject = Subject::find($id);
        OsceCommon::valueIsNull($subject, -1000, '没有找到对应的科目');

        $items = $subject->items;
        $items = SubjectItem::builderItemTable($items);
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

        //获取考试项目——病例关系数据
        $subjectCases = SubjectCases::where('subject_id','=',$id)->get();
        //获取考试项目——用物关系数据
        $subjectSupplys = SubjectSupply::where('subject_id','=',$id)->get();

        return view('osce::admin.resourceManage.course_manage_edit',
            ['item' => $subject, 'list' => $items, 'prointNum' => $prointNum, 'optionNum' => $optionNum,
             'subjectCases' => $subjectCases, 'subjectSupplys' => $subjectSupplys,
            ]);
    }

    /**
     *
     * @url /osce/admin/topic/getDelTopic
     * @access public
     *
     *
     * <b>get 请求字段：</b>
     * * string        id        考核标准ID(必须的)
     *
     * @return
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
            $SubjectModel->delSubject($subject);
            return \Response::json(array('code' => 1));
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
            //return redirect()->back()->withErrors($ex->getMessage());
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
     * @author jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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

                /*判断分数 TODO: Zhoufuxiang 2016-2-26*/
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
     * 判断科目模板表头及列数 TODO: zhoufuxiang 2016-2-27
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>     *
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
       $paginate = $request->get('paginate');
       try{
          
           $caseModel = new CaseModel();
          
           if(empty($caseName)){

               //查询出所有的病例
               $casesList = $caseModel->getCasesList($caseName);
           }else{
               $casesList = $caseModel->getCasesList($caseName);
           }
           
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
    public  function getSubjectSupply(){
        try{

            $caseModel = new Supply();
            
                //查询出所有的病例
                $supplyList = $caseModel->getSupplyList();

            return response()->json(
                $this->success_data($supplyList, 1, '病例获取成功')
            );
        }catch (\Exception $ex){
            return response()->json($this->fail($ex));

        }

    }



}