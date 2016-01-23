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
use Maatwebsite\Excel\Facades\Excel;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Http\Controllers\CommonController;

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
    public function getList(Request $request){
        $name   =   e($request->get('name'));
        $Subject    =   new Subject;
        $list       =   $Subject    ->  getList($name);
        return view('osce::admin.examination.topicList',['list'=>$list]);
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
    public function postAddTopic(Request $request){
        $this   ->  validate($request,[
            'title'         =>  'required|unique:osce_mis.subject,title',
            'content'       =>  'required',
            'score'         =>  'required',
            'desc'          =>  'sometimes',
        ],[
            'title.required'    =>  '评分标准名称必须',
            'title.unique'      =>  '评分标准名称必须唯一',
            'content.required'  =>  '评分标准必须',
            'score.required'    =>  '评分必须',
        ]);

        $content        = $request  ->get('content');
        $score          = $request  ->get('score');
        $answer          = $request ->get('description');

        $formData = SubjectItem::builderItemData($content, $score,$answer);
        $totalData   =  0;
        foreach($score as $index=>$socrdata)
        {
            foreach($socrdata as $key=>$socre)
            {
                if($key=='total')
                {
                    continue;
                }
                $totalData  +=  $socre;
            }
        }

        $data   =   [
            'title'         =>  e($request  ->  get('title')),
            'description'   =>  e($request  ->  get('desc')),
            'score'         =>  $totalData,
        ];

        $subjectModel   =   new Subject();
        if($subjectModel->  addSubject($data,$formData)){
            return redirect()->route('osce.admin.topic.getList');
        } else{
            return  redirect()->back()->withErrors(new \Exception('新增失败'));
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
    public function postEditTopic(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
            'title' =>  'required',
            'desc' =>  'sometimes',
        ],[
            'id.required'       =>  '课题ID必须',
            'title.required'    =>  '课题名称必须',
        ]);

        $data   =   [
//            'id'            =>  intval($request     ->  get('id')),
            'title'         =>  e($request          ->  get('title')),
            'description'   =>  $request          ->  get('description'),
        ];
        $id     =   intval($request ->get('id'));

        $subjectModel   =   new Subject();
        try
        {
            $formData   =   SubjectItem::builderItemData($request->get('content'),$request->get('score'),$request->get('description'));

            if($subjectModel   ->  editTopic($id,$data,$formData))
            {
                return redirect()->route('osce.admin.topic.getList');
            }
            else
            {
                throw new \Exception('编辑失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
        }
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
    public function getAddTopic(){
        return view('osce::admin.resourcemanage.categories');
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
    public function getEditTopic(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required'
        ]);

        $id         =   $request->get('id');
        $subject    =   Subject::find($id);

        $items      =   $subject->items;
        $items      =   SubjectItem::builderItemTable($items);
        $prointNum  =   1;
        $optionNum  =   [
            0=>0
        ];
        foreach($items as $item)
        {
            if($item->pid==0)
            {
                $prointNum++;
            }
            else
            {
                if(array_key_exists($item->pid,$optionNum))
                {
                    $optionNum[$item->pid]++;
                }
                else
                {
                    $optionNum[$item->pid]=0;
                }
            }
        }
        return view('osce::admin.resourcemanage.edittopic',['item'=>$subject,'list'=>$items,'prointNum'=>$prointNum,'optionNum'=>$optionNum]);
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
    public function getDelTopic(Request $request){
        $this->validate($request,[
            'id'=>'required'
        ]);
        $id =   $request->get('id');
        $SubjectModel   =   new Subject();
        $subject =  $SubjectModel->find($id);
        try{
            $SubjectModel   ->  delSubject($subject);
            return redirect()->route('osce.admin.topic.getList');
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
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
            //将中文表头，按配置翻译成英文的字段名
            $data = Common::arrayChTOEn($topicList, 'osce.importForCnToEn.standard');
            foreach ($data as &$items) {
                foreach ($items as &$item) {
                    $item = e($item);
                }
            }
            echo json_encode($this->success_data($data));
        } catch (\Exception $ex) {
            echo json_encode($this->fail($ex));
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
    public function getToppicTpl(){
        $this->downloadfile('topic.xlsx',public_path('download').'/topic.xlsx');
    }

    private function downloadfile($filename,$filepath){
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
}