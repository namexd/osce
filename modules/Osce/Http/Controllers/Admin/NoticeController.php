<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/6
 * Time: 15:50
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Repositories\Common;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Notice;
use Modules\Osce\Http\Controllers\CommonController;
use Overtrue\Wechat\Message;

class NoticeController extends CommonController
{
    /**
     * 已发布通知列表
     * @api GET /osce/admin/notice/list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList()
    {
        $notice = new Notice();
        $list = $notice->getList();
        return view('osce::admin.examManage.exam_notice', ['list' => $list]);
    }

    /**
     * 新增通知
     * @url /osce/admin/notice/add-notice
     * @access public
     *
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-06 17:07
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getAddNotice(Request $request)
    {
        $list = Exam::get();
        return view('osce::admin.examManage.exam_notice_add', ['list' => $list]);
    }

    /**
     * 新增通知
     * @url /osce/admin/notice/add-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        title            通知标题(必须的)
     * * string        content          通知内容(必须的)
     * * string        exam_id          考试ID(必须的)
     * * array         group            用户组(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-06 19:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postAddNotice(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'exam_id' => 'required',
            'attach' => 'sometimes',
        ]);

        $title = $request->get('title');
        $content = $request->get('content');
        $exam_id = $request->get('exam_id');
        $groups = $request->get('groups');
        $attach = $request->get('attach');
        if (!empty($attach)) {
            $attach = e(implode(',', $attach));
        } else {
            $attach = '';
        }

        try {
            $contentLen = mb_strlen($content);
            if($contentLen > 10000){
                throw new \Exception('内容字数超过限制，请修改后重试！');
            }
            if (!is_array($groups)) {
                throw new \Exception('请选择接收人所属角色');
            }
            $noticeModel = new Notice();
            if ($noticeModel->sendNotice($title, $content, $exam_id, $groups, $attach)) {
                return redirect()->route('osce.admin.notice.getList');
            } else {
                throw new \Exception('通知创建失败');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 变更通知表单
     * @api GET /osce/admin/notice/edit-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id               通知ID(必须的)
     * * string        title            通知标题(必须的)
     * * string        content          通知内容(必须的)
     * * string        exam_id          考试ID(必须的)
     * * array         group            用户组(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getEditNotice(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $list = Exam::get();
        $id = $request->get('id');
        $item = Notice::find($id);
        return view('osce::admin.examManage.exam_notice_edit', ['item' => $item, 'list' => $list]);
    }

    /**
     *  更新通知
     * @api GET /osce/admin/invigilator/edit-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id               通知ID(必须的)
     * * string        title            通知标题(必须的)
     * * string        content          通知内容(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditNotice(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'content' => 'required',
            'attach' => 'sometimes',
            'id' => 'required',
           'accept'=>'required'
        ],[
           'accept.required' =>'请选择通知人'
        ]);

        $id = $request->get('id');
        $name = $request->get('name');
        $content = $request->get('content');
        $exam_id = $request->get('exam_id');
        $groups = $request->get('accept');
        $attach = $request->get('attach');

        if (!empty($attach)) {
            $attach = e(implode(',', $attach));
        } else {
            $attach = '';
        }
        $NoticeModel = new Notice();
        try {
            $contentLen = mb_strlen($content);
            if($contentLen > 10000){
                throw new \Exception('内容字数超过限制，请修改后重试！');
            }
            if ($NoticeModel->editNotice($id, $name, $content, $attach, $groups)) {
                return redirect()->route('osce.admin.notice.getList');
            } else {
                throw new \Exception('更新通知失败');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 删除通知
     * @url /osce/admin/invigilator/del-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        通知ID(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-01-06 20：46
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getDelNotice(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $id = $request->get('id');

        $notice = Notice::find($id);

        try {
            if ($notice) {
                if ($notice->delete()) {
                    //return redirect()->route('osce.admin.notice.getList');
                    return response()->json(
                        $this->success_data(['result'=>true],1,'删除成功')
                    );
                } else {
                    throw new \Exception('删除失败');
                }
            } else {
                throw new \Exception('没有找到相应的通知');
            }
        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }
    }
}


