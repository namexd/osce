<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/9
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use Modules\Osce\Entities\InformInfo;
use Modules\Osce\Entities\Notice;
use Modules\Osce\Http\Controllers\CommonController;

class NoticeController extends CommonController
{
    /**
     * 通知列表
     * @url GET /osce/wechat/notice/system-list
     * @access public
     *
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSystemList(Request $request){
        $notice =   new InformInfo();
        $list   =   $notice ->  getList();
        return view('osce::wechat.exammanage.exam_notice',['list'=>$list]);
    }

    /**
     * 查看通知详情
     * @url /osce/wechat/notice/view
     * @access public
     *
     * * @param Request $request
     * <b>get 请求字段：</b>
     * * string        id        消息ID(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getView(Request $request){
        $this   ->  validate($request,[
            'id'    =>  'required',
        ]);

        $id     =   $request    ->  get('id');
        $notice =   InformInfo::find($id);

        if(is_null($notice))
        {
            //消息不存在
            abort(404,'你要查看的通知不存在');
        }

        return view('osce::wechat.exammanage.exam_notice_detail',['notice'=>$notice]);
    }
}