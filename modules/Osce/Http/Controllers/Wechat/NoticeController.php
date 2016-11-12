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
use Modules\Osce\Entities\StationTeacher;
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


  //osce/wechat/notice/system-view
    public function  getSystemView(Request $request)
    {
        //查询当前操作人是学生、老师、sp老师 TODO zhoufuxiang 16-1-22
        $user = \Auth::user();
        if (!$user) {
            throw new \Exception('没有找到当前操作人的信息！');
        }

        $student = Student::where('user_id', $user->id)->first();
        $spTeacher = Teacher::where('id', $user->id)->where('type', 2)->first();
        $exam_ids = [];
        if (!empty($student)) {
            $examIds = Student::where('user_id', $user->id)->select(['exam_id'])->get();
            $accept = 1;       //接收者:1为学生

        } else{
            $stationTeacher = new StationTeacher();
            $examIds = $stationTeacher->getExamToUser($user->id);
            //接收者（3为sp老师，2为监、巡考老师）
            $accept = empty($spTeacher)? 2:3;
        }
        //取出考试ID
        if(isset($examIds) && count($examIds)){
            foreach($examIds as $value){
                array_push($exam_ids,$value->exam_id);
            }
        }
        //获取 资讯&通知 列表
        $informInfo = new  InformInfo();
        $pagination = $informInfo->getList($accept, $exam_ids);
        $list       = $informInfo->getList($accept, $exam_ids);
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
        //消息不存在
        if (is_null($notice)) {
            return redirect()->back()->withErrors('你要查看的通知不存在！');
//            abort(404, '你要查看的通知不存在');
        }
        if($notice->attachments){
            $notice->attachments = explode(',', $notice->attachments);
        }

        return view('osce::wechat.exammanage.exam_notice_detail', ['notice' => $notice]);
    }

    /**
     * 附件下载
     */
    public function getDownloadDocument(Request $request)
    {
        try{
            $this->validate($request,[
                'id'            =>'required|integer',
                'attch_index'   =>'required|integer',
            ]);
            $id     =   $request->get('id');
            $key    =   $request->get('attch_index');
            $info   =   InformInfo::find($id);
            if (is_null($info)) {
                //消息不存在
                abort(404, '你要下载的东西不存在');
            }
            if($info->attachments){
                $attchments = explode(',', $info->attachments);
            }else{
                throw new \Exception('没有找到相应的文件');
            }

            $thisFile       =   $attchments[$key];
            $fileNameArray  =   explode('/',$thisFile);

            $this->downloadfile(array_pop($fileNameArray),public_path().$thisFile);

        } catch(\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 文件下载
     */
    private function downloadfile($filename,$filepath){
        $file   = explode('.',$filename);
        $tFile  = array_pop($file);
        $filename = md5($filename).'.'.$tFile;
        //TODO:Zhoufuxiang 2016-3-14
//        $encode = mb_detect_encoding($filepath, array("ASCII","GB2312","GBK","UTF-8",'BIG5'));
//        if($encode == 'UTF-8'){
//            $filepath = iconv('utf-8', 'gbk', $filepath);
//        }

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