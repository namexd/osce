<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/14 0014
 * Time: 14:49
 */
namespace Modules\Osce\Http\Controllers\Admin;



use App\Entities\User;
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
     * @url  /osce/admin/train/train-list
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
//        return false;
//      }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get();

        return view('osce::wechat.train.train_list')->with(['list'=>$list,'pagination'=>$pagination]);

    }



    /**
     *新增培训
     * @method GET
     * @url /osce/admin/train/add-train
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
        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content','status']);
        $data['create_user_id']=$userId;
        $id=InformTrain::insertGetId($data);
        $array=array(
            'id'      =>$id,
            'userId'  =>$userId,
        );
        $result= $this->postUploadFile($request,$array);
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
     *查看考前培训
     * @method GET
     * @url /user/
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
    public function getTrainDetail(Request $request){
        $id=$request->get('id');
        $train=InformTrain::find($id);
        return view('osce::wechat.train.train_detail')->with('train',$train);
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
    public function postUploadFile(Request $request, Array $array){
       try {
           $time = date('Y-m-d');
           list($id, $userId) = $array;
           $username=User::findOrFail($userId)->first()->name;
           $params=[
               'username' =>$username,
               'userId'   =>$userId,
           ];
           list($userId,$time)=$array;
           if(!$request->hasFile('word')){
               throw new \Exception('上传的word文档不存在');
           }
           if(!$request->hasFile('excel')){
               throw new \Exception('上传的excel文件不存在');
           }

           $words=$request->file('word');
           $excels=$request->file('excel');

           if (!$words->isValid()) {
               throw new \Exception('上传的word文档出错');
           }
           if (!$excels->isValid()) {
               throw new \Exception('上传的excel文件出错');
           }
           //拼装文件名
           $resultPhoto[] = self::uploadFile($words, $time, $params, $id);
           $resultRadio[] = self::uploadFile($excels, $time, $params, $id);

           $result = [$resultPhoto, $resultRadio];
           return $result;
       }catch (\Exception $ex){
           throw $ex;
       }
    }


    protected function uploadFile($files, $date, array $params,$id){
        try {
            //将上传的文件遍历
            foreach ($files as $key => $file) {
                //拼凑文件名字
                $fileName = '';
                $fileMime = $file->getMimeType();
                foreach ($params as $param) {
                    $fileName .= $param;
                }
                $fileName .= $file->getClientOriginalExtension(); //获取文件名的正式版

                $savePath = public_path('osce/Train/') . $fileMime . '/' . $date . '/' . $params['username'] . '/';
                $savePath = realpath($savePath);


                if (!file_exists($savePath)) {
                    mkdir($savePath, 0755, true);
                }

                //将文件放到自己的定义的目录下
                if (!$file->move($savePath, $fileName)) {
                    throw new \Exception('文件保存失败！请重试！');
                }

                //生成附件url地址
                $attachUrl = $savePath . $fileName;

                //将要插入数据库的数据拼装成数组
                $data = [
                    'url' => $attachUrl,
                    'type' => $fileMime,
                    'name' => $fileName,
                    'description' => $date . '-' . $params['username'],
                ];

                //将内容插入数据库
                if (!$result = InformTrain::where('id',$id)->update(['attachments'=>serialize($data)])) {
                    throw new \Exception('附件数据保存失败');
                }
                return $result;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}