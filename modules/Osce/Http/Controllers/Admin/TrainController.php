<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 14:49
 */
namespace Modules\Osce\Http\Controllers\Admin;



use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\InformTrain;
use Modules\Osce\Http\Controllers\CommonController;

class TrainController extends  CommonController{
    /**
     *培训列表
     * @method GET
     * @url
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
          return false;
        }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get();

        return view()->with(['list'=>$list,'pagination'=>$pagination]);

    }



    /**
     *新增培训
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
    public function postAddTrain(Request $request){
        $this->validate($request,[
            //验证规则
            'name'                    =>'required|max:64',
            'address'                 =>'required|max:64',
            'begin_dt'                =>'required',
            'end_dt'                  =>'required',
            'teacher'                 =>'required',
            'content'                 =>'required',
            'status'                  =>'required',
        ]);
        $user=Auth::user();
        $userId=$user->id;
        $attachments=$this->postUploadFile();
        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content','status']);
        $data['create_user_id']=$userId;
        $data['attachments']=serialize($attachments);
        $result=InformTrain::insert($data);
        if($result){
         return view()->with('success','新增成功');
        }
        return redirect()->back()->withInput()->withErrors('新增失败');
    }

    /**
     *编辑培训回显
     * @method GET
     * @url /osce/wechat/train/edit-train
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
    public function getEditTrain(Request $request){
        $this->validate($request,[
            'id'  => 'required|integer'
        ]);
        $id=intval($request->get('id'));
        $user=Auth::user();
        $userId=$user->id;
        $creteId=InformTrain::where('id',$id)->select()->first()->create_user_id;
        $manager=config('osce.manager');
        if($userId!==$id || $creteId!==$manager[0]){
            return response()->json(
                $this->success_rows(3,'false')
            );
        }
        $list=InformTrain::find($id);

        foreach($list as $item){
            $data=[
                'name' =>$item->name,
                'address' =>$item->address,
                'begin_dt' =>$item->begin_dt,
                'end_dt' =>$item->end_dt,
                'teacher' =>$item->teacher,
                'content' =>$item->content,
                'status' =>$item->status,
                'attachments' =>$item->attachments,
                'create_user_id' =>$item->create_user_id,
            ];
        }
        $data['attachments']=unserialize($data['attachments']);
        return view()->with('data',$data);
    }

    /**
     *保存编辑培训
     * @method POST
     * @url /osce/wechat/train/edit-train
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
    public function postEditTrain(Request $request){
        $this->validate($request,[
            'id'                      =>'required|integer',
            'name'                    =>'required|max:64',
            'address'                 =>'required|max:64',
            'begin_dt'                =>'required',
            'end_dt'                  =>'required',
            'teacher'                 =>'required',
            'content'                 =>'required',
            'status'                  =>'required',
            'create_user_id'          =>'required',
        ]);
        $attachments=$this->postUploadFile();
        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content','status','create_user_id']);
        $user=Auth::user();
        $userId=$user->id;
        $creteId=InformTrain::where('id',$data['id'])->select()->first()->create_user_id;
        $manager=config('osce.manager');
        if($userId!==$creteId || $creteId!==$manager[0]){
            return response()->json(
                $this->success_rows(3,'false')
            );
        }
        $data['attachments']=unserialize($attachments);
        $result=InformTrain::where('id',$data['id'])->update($data);
        if($result){
            return view()->with('success','编辑成功');
        }
        return redirect()->back()->withInput()->withErrors('编辑失败');
    }

    /**
     *删除培训
     * @method GET
     * @url /osce/wechat/train/del-train
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
    public function getDelTrain(Request $request){
        $this->validate($request,[
            'id'  =>'required|integer'
        ]);

        $id=intval($request->get('id'));
        $user=Auth::user();
        $userId=$user->id;
        $creteId=InformTrain::where('id',$id)->select()->first()->create_user_id;
        $manager=config('osce.manager');
        if($userId!==$creteId || $creteId!==$manager[0]){
            return response()->json(
                $this->success_rows(3,'false')
            );
        }
        $result=InformTrain::where('id',$id)->delete();
        if($result){
            return view()->with('success','删除成功');
        }
        return redirect()->back()->withInput()->withErrors('删除失败');
    }

    /**
     *上传文件
     * @method GET
     * @url /osce/wechat/train/upload-file
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
    public function postUploadFile(){
        $user=Auth::user();
        $userId=$user->id;
        $file=\Input::file('doc');
        $time=date('Y-m-d',time());
        $fileName=$file->getFilename();
        $file_ex=$file->getClientOriginalExtension();
//         $file_size=round($file->getSize() /1024);
//         $file_mime=$file->getMimeType();
        $uploadDir='/uploads/'.$time.'/doc';
        if(!dir($uploadDir)){
            mkdir('uploads',777,true);
        }
        if (!in_array($file_ex, array('doc', 'xlsx'))) {
            return \Redirect::to('/')->withErrors('上传类型不合法');
        }
        $newname=strtotime(date('Ymd')).'-'.$fileName.$userId;
        if(\Request::file('doc')){
            $result= \Request::file()->move(base_path().$uploadDir.'/',$newname);
            if(!$result){
                return \Response::json('false',400);
            }
        }
        $path['doc']=\Input::file('doc')->getRealPath();

        $file=\Input::file('xlsx');
        $fileName=$file->getFilename();
        $file_ex=$file->getClientOriginalExtension();
        $uploadDir='/uploads/'.$time.'/xlsx';
        if(!dir($uploadDir)){
            mkdir('uploads',777,true);
        }
        if (!in_array($file_ex, array('doc', 'xlsx'))) {
            return \Redirect::to('/')->withErrors('上传类型不合法');
        }
        $newname=strtotime(date('Ymd')).'-'.$fileName.$userId;
        if(\Request::file('xlsx')){
            $result= \Request::file()->move(base_path().$uploadDir.'/',$newname);
            if(!$result){
                return \Response::json('false',400);
            }
        }
        $path['xlsx']=base_path().$uploadDir.'/'.$newname;
        return $path;
    }


}