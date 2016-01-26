<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/9
 * Time: 11:06
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Config;
use Modules\Osce\Entities\InformInfo;
use Modules\Osce\Entities\Notice;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Teacher;
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
    public function getSystemList(Request $request)
    {

        //查询当前操作人是学生、老师、sp老师 TODO zhoufuxiang 16-1-22
        $user = \Auth::user();
        if (!$user) {
            throw new \Exception('没有找到当前操作人的信息！');
        }
        $student = Student::where('user_id', $user->id)->first();
        $spTeacher = Teacher::where('id', $user->id)->where('type', 2)->first();
        if (!empty($student)) {
            $accept = 1;       //接收着为学生
        } elseif (!empty($spTeacher)) {
            $accept = 3;       //接收着为sp老师
        } else {
            $accept = 2;
        }
        // TODO zhoufuxiang 16-1-25
//        $way    = $request -> get('way');       //通知方式
        $config = Config::where('name', '=', 'type')->first();
//        if(!empty($way) && !empty($config)){
//            //查看 系统设置中，是否有此 通知方式
//            if(!in_array($way, json_decode($config->value))){
//                return view('osce::wechat.exammanage.exam_notice',['list'=>[]]);
//            }
//        }

//        $notice =   new InformInfo();
//        $config = Config::where('name','=','type')->first();
//        if(empty($config) || in_array(4,json_decode($config->value))){
//            $list   =   $notice ->  getList();
//            //根据操作人去除不给他接收的数据
//            if(!empty($list)){
//                foreach ($list as $index => $item) {
//                    if(!in_array($accept, explode(',', $item->accept))){
//                        unset($list[$index]);
//                    }
//            }
//        }else{
//            $list   =   [];
//        }

        return view('osce::wechat.exammanage.exam_notice');

    }


  ///osce/wechat/notice/system-view
    public function   getSystemView(Request $request)
    {
        $trainModel = new  InformInfo ();
        $pagination = $trainModel->getList();
        $notice =   new InformInfo();
        $list   =   $notice ->getList();
        //$list = InformInfo::select()->orderBy('created_at')->get()->toArray();
        $data   =   $list->toArray();
        return response()->json(
            $this->success_rows(1, 'success', $pagination->total(), config('osce.page_size'), $list->currentPage(), $data['data'])
        );
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
    public function getView(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $id = $request->get('id');
        $notice = InformInfo::find($id);
        if($notice->attachments){
            $notice->attachments = explode(',', $notice->attachments);
        }

        if (is_null($notice)) {
            //消息不存在
            abort(404, '你要查看的通知不存在');
        }

        return view('osce::wechat.exammanage.exam_notice_detail', ['notice' => $notice]);
    }
}