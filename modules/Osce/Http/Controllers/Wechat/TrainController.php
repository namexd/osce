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
        $user=Auth::user();
        $userId=$user->id;

        if(!$userId){
            return response()->json(
                $this->success_rows(0,'false')
            );
        }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get();

       return view()->with(['list'=>$list,'pagination'=>$pagination]);
    }


}