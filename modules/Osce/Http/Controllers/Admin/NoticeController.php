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
    public function getList(){
        $notice =   new Notice();

        $list   =   $notice ->  getList();
        //return view('',['list'=>$list]);
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
    public function getAddNotice(Request $request){
        $list   =   Exam::get();
        return view('osce::admin.exammanage.exam_notice_add',['list'=>$list]);
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
    public function postAddNotice(Request $request){
        $this   ->  validate($request,[
            'title'     =>  'required',
            'content'   =>  'required',
            'exam_id'   =>  'required',
        ]);

        $title      =   $request    ->  get('title');
        $content    =   $request    ->  get('content');
        $exam_id    =   $request    ->  get('exam_id');
        $groups    =   $request     ->  get('groups');

        try
        {
            if(!is_array($groups))
            {
                throw new \Exception('请选择接收人所属角色');
            }
            $noticeModel    =   new Notice();
            if($noticeModel    ->  sendNotice($title,$content,$exam_id,$groups))
            {
                return redirect()->route('osce.admin.notice.getList');
            }
            else
            {
                throw new \Exception('通知创建失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()   ->  back()  ->withErrors($ex);
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
    public function getEditNotice(Request $request){
        $this   ->  validate($request,[
            'id'        =>  'required',
        ]);
        $id     =   $request    ->  get('id');
        $item   =   Notice::find($id);
        //return view('',['item'=>$item]);
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
    public function postEditNotice(Request $request){
        $this   ->  validate($request,[
            'title'     =>  'required',
            'content'   =>  'required',
            'id'        =>  'required',
        ]);

        $id             =   $this   ->  get('id');
        $content        =   $this   ->  get('content');
        $title          =   $this   ->  get('title');

        $NoticeModel    =   new Notice();
        try
        {
            if($NoticeModel    ->editNotice($id,$title,$content))
            {
                return redirect()->route('osce.admin.notice.getList');
            }
            else
            {
                throw new \Exception('更新通知失败');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
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
    public function getDelNotice(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required'
        ]);

        $id     =   $request    ->  get('id');

        $notice =   Notice::find($id);

        try
        {
            if($notice)
            {
                if($notice->delete())
                {
                    return redirect()->route('osce.admin.notice.getList');
                }
                else
                {
                    throw new \Exception('删除失败');
                }
            }
            else
            {
                throw new \Exception('没有找到相应的通知');
            }
        }
        catch(\Exception $ex)
        {
            return redirect()->back()->withErrors($ex);
        }
    }
}