<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/6
 * Time: 15:50
 */

namespace Modules\Osce\Http\Controllers\Admin;

use App\Repositories\Common;
use Modules\Osce\Entities\Notice;
use Modules\Osce\Http\Controllers\CommonController;
use Overtrue\Wechat\Message;

class NoticeController extends CommonController
{
    public function getMsg(){
        $Message  =   Common::CreateWeiXinMessage([
            [
                'title' =>'邀请通知',
                'desc'  =>'osce考试第一期邀请',
                'url'=>'http://www.baidu.com'
            ]
            //['title'=>'osce考试第一期邀请','url'=>'http://www.baidu.com'],
        ]);
        //oI7UquKmahFwGV0l2nyu_f51nDJ4
        //oI7UquPKycumti7NU4HQYjVnRjPo
        //Common::sendWeiXin(['oI7UquKmahFwGV0l2nyu_f51nDJ4','oI7UquPKycumti7NU4HQYjVnRjPo'],$Message);
        dd(123);
        Common::sendWeiXin('oI7UquKmahFwGV0l2nyu_f51nDJ4',$Message);
    }

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
    public function getAddNotice(){
        //return view();
    }

    /**
     * 新增通知
     * @url /osce/admin/notice/add-notice
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        title        通知标题(必须的)
     * * string        content      通知内容(必须的)
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
    public function postAddNotice(){

    }

    /**
     *
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
    public function getEditNotice(){

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
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postEditNotice(){

    }


}