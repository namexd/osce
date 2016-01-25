<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/24 0024
 * Time: 17:26
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use App\Entities\UsersPm;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Config;
use Modules\Osce\Entities\InformInfo;
use Modules\Osce\Entities\Notice;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
class NoticeListController   extends CommonController
{
    /**
     * 通知列表
     * @url GET /osce/wechat/notice-list/system-list
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
        //查询当前操作人是学生、老师、sp老师 TODO zhoufuxiang 16-1-22
        $user = \Auth::user();
        if(!$user){
            throw new \Exception('没有找到当前操作人的信息！');
        }
        $student = Student::where('user_id', $user->id)->first();
        $spTeacher = Teacher::where('id',$user->id)->where('type',2)->first();

        // TODO zhoufuxiang 16-1-22
        $notice = new UsersPm();

        $noticeList =$notice->getList($user->id);
//       dd($noticeList['data']);
         if($noticeList['total']!==0){
              foreach($noticeList['data'] as  $index => $item){
                  $list[]=[
                      'title'=>$item->title,
                      'content' =>$item->content,
                      'accept_user_id'=>$item->accept_user_id,
                      'send_user_id'=>$item->send_user_id,
                      'created_at'=>$item->created_at,
                      'updated_at'=>$item->updated_at,
                  ];
              }
        }else{
             $list   =   [];
         }
//        dd($list);

//        $notice =   new InformInfo();
//        $config = Config::where('name','=','type')->first();
//
//        if(empty($config) || in_array(4,json_decode($config->value))){
//            $list   =   $notice ->  getList();
//            //根据操作人去除不给他接收的数据
//            if(!empty($list)){
//                foreach ($list as $index => $item) {
//                    if(!in_array($accept, explode(',', $item->accept))){
//                        unset($list[$index]);
//                    }
//                }
//            }
//        }else{
//            $list   =   [];
//        }

        return view('osce::wechat.exammanage.exam_notice',['list'=>$list]);
    }

    /**
     * 查看通知详情
     * @url /osce/wechat/notice-list/view
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
            'accept_user_id'    =>  'required',
        ]);

        $id     =   $request    ->  get('accept_user_id');
        $notice =   UsersPm::find($id);
        dd($notice);

        if(is_null($notice))
        {
            //消息不存在
            abort(404,'你要查看的通知不存在');
        }

        return view('osce::wechat.exammanage.exam_notice_detail',['notice'=>$notice]);
    }
}