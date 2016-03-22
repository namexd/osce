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
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\SubjectItem;
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
        return view('osce::admin.resourceManage.subject_manage', ['list' => $list, 'name' => $name]);
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
            'title' => 'required|unique:osce_mis.subject,title',
            'content' => 'required',
            'score' => 'required',
            'desc' => 'required',
            'stem' => 'required',
            'equipments' => 'required',
            'goods' => 'required',
            'description'=>'required'
        ], [
            'title.required' => '名称必填',
            'title.unique' => '该科目已存在',
            'content.required' => '必须新增评分点',
            'score.required' => '分数必填',
            'desc.required' => '必须新增描述',
            'description.required' => '请添加考核项',
        ]);

        $content = $request->get('content');
        $score = $request->get('score');
        $answer = $request->get('description');

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

            $data = [
                'title' => e($request->get('title')),
                'description' => e($request->get('desc')),
                'stem' => e($request->input('stem')),
                'equipments' => e($request->input('equipments')),
                'goods' => e($request->input('goods')),
                'score' => $totalData,
            ];

            $subjectModel = new Subject();
            if ($subjectModel->addSubject($data, $formData)) {
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
            'title' => 'required',
            'desc' => 'sometimes',
            'content' => 'required',
            'score' => 'required',
            'stem' => 'required',
            'equipments' => 'required',
            'goods' => 'required'
        ], [
            'id.required' => '课题ID必须填写',
            'title.required' => '课题名称必须填写',
            'content.required' => '评分标准必须填写',
            'score.required' => '评分必须填写',
        ]);

        $data = [
            'title' => e($request->get('title')),
            'description' => $request->get('note'),
            'stem' => $request->input('stem'),
            'equipments' => $request->input('equipments'),
            'goods' => $request->input('goods')
        ];
        $id = intval($request->get('id'));

        $subjectModel = new Subject();
        try {
            $formData = SubjectItem::builderItemData($request->get('content'), $request->get('score'),
                $request->get('description'));

            if ($subjectModel->editTopic($id, $data, $formData)) {
                return redirect()->route('osce.admin.topic.getList');
            } else {
                throw new \Exception('编辑失败');
            }
        } catch (\Exception $ex) {
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
    public function getAddTopic()
    {
        return view('osce::admin.resourceManage.subject_manage_add');
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
        OsceCommon::objIsNull($subject, '没有找到对应的科目', -1000);

        $items = $subject->items;
        $items = SubjectItem::builderItemTable($items);
        $prointNum = 1;
        $optionNum = [
            0 => 0
        ];
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
        return view('osce::admin.resourceManage.subject_manage_edit',
            ['item' => $subject, 'list' => $items, 'prointNum' => $prointNum, 'optionNum' => $optionNum]);
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


}