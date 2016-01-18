<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:01
 */
namespace Modules\Osce\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\InformTrain;
use Modules\Osce\Http\Controllers\CommonController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrainController extends  CommonController{

    /**
     *培训列表
     * @method GET
     * @url /osce/wechat/train/train-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTrainList(){
//      $user=Auth::user();
//      $userId=$user->id;
//
//      if(!$userId){
//          return response()->json(
//              $this->success_rows(0,'false')
//          );
//      }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get();
        $data=[];
        foreach($list as $item){
            $time=time()-strtotime($item->created_at);
            if ($time < 0) {
                $time = $time;
            } else {
                if ($time < 60) {
                    $time= $time . '秒前';
                } else {
                    if ($time < 3600) {
                        $time=  floor($time / 60) . '分钟前';
                    } else {
                        if ($time < 86400) {
                            $time= floor($time / 3600) . '小时前';
                        } else {
                            if ($time < 2592000) {
                                $time= floor($time / 86400) . '天前';
                            } else {
                                if($time<31536000){
                                    $time =  floor($time / 2592000).'月前';
                                }else{
                                    $time=floor($time/31536000).'年前';
                                }
                            }
                        }
                    }
                }
            }
            $data[]=[
                'id' =>$item->id,
                'name' =>$item->name,
                'address' =>$item->address,
                'create_at' =>$item->created_at,
                'begin_dt' =>$item->begin_dt,
                'teacher' =>$item->teacher,
                'content' =>$item->content,
                'end_dt' =>$item->end_dt,
                'author'   =>$item->getAuthor,
                'time' =>$time,
            ];

        }
        return view('osce::wechat.train.train_list')->with(['data'=>$data,'pagination'=>$pagination]);
    }

    /**
     *查看考前培训
     * @method GET
     * @url /osce/wechat/train/train-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        培训id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getTrainDetail(Request $request){
        $id=$request->get('id');
        $train=InformTrain::find($id);
        return view('osce::wechat.train.train_detail')->with('train',$train);
    }
}