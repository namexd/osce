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
      if(!$user){
        return redirect()->back()->withErrors(['请登陆']);
      }

        $trainModel=new InformTrain();
//        $pagination=$trainModel->getPaginate();
//        $list=InformTrain::select()->orderBy('begin_dt','DESC')->get();
        $list=$trainModel->getInformList();
        return view('osce::admin.examManage.train_list',['list'=>$list]);

    }



    /**
     *新增培训
     * @method GET
     * @url /osce/admin/train/add-train
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name               培训名称(必须的)
     * * string        address            地址(必须的)
     * * datetime      begin_dt           开始时间(必须的)
     * * datetime      end_dt             结束时间(必须的)
     * * string        teacher            培训讲师(必须的)
     * * string        content            内容(必须的)
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
        ]);

        $user=Auth::user();
        if(!$user){
            return redirect()->back()->withErrors(['请登陆']);
        }
        $userId=$user->id;
//        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content']);
//        $data['attachments']=serialize($request->input('file'));
//        $data['create_user_id']=$userId;
        $data=[
            'name'               => $request->get('name'),
            'address'            => $request->get('address'),
            'begin_dt'           => $request->get('begin_dt'),
            'end_dt'             => $request->get('end_dt'),
            'teacher'            => $request->get('teacher'),
            'content'            => $request->get('content'),
            'attachments'        => serialize($request->input('file')),
            'create_user_id'     => $userId,
            'clicks'             => 0,
        ];
        $result=InformTrain::create($data);
        if($result){
         return redirect('/osce/admin/train/train-list')->with('success','新增成功');
        }
        return redirect()->back()->withInput()->withErrors(['新增失败']);
    }

    /**
     *编辑培训回显
     * @method GET
     * @url /osce/admin/train/edit-train
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        培训id(必须的)
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
        if(!$userId){
            return \Response::json(array('code'=>2));
        }
        $createId=InformTrain::where('id',$id)->select()->first()->create_user_id;

        if($createId!=$userId){
            $url=1;
        }elseif($userId==config('config.superRoleId')){
            $url=2;
        }else{
            $url=2;
        }
        $list=InformTrain::where('id',$id)->select()->get();

        foreach($list as $item){
            $data=[
                'id'   =>$item->id,	
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
        if($data['attachments']){
            $data['attachments']=unserialize($data['attachments']);
        }
        return view('osce::admin.examManage.train_edit')->with(['data'=>$data,'url'=>$url]);
    }

    /**
     *保存编辑培训
     * @method POST
     * @url /osce/admin/train/edit-train
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id                 主键id(必须的)
     * * string        name               培训名称(必须的)
     * * string        address            地址(必须的)
     * * datetime      begin_dt           开始时间(必须的)
     * * datetime      end_dt             结束时间(必须的)
     * * string        teacher            培训讲师(必须的)
     * * string        content            内容(必须的)
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
        ]);
        $data=$request->only(['id','name','address','begin_dt','end_dt','teacher','content']);

        $user=Auth::user();
        $userId=$user->id;
        $connection	=	\DB::connection('sys_mis');
        $userRole	=	$connection	->	table('sys_user_role')	->	where('user_id','=',$user->id)->first();
        $createId=InformTrain::where('id',$data['id'])->select()->first()->create_user_id;
        if($userId==$createId || $userRole->role_id==config('config.superRoleId')){
            $data['attachments']=serialize($request->input('file'));
            $result=InformTrain::where('id',$data['id'])->update($data);
            if($result){
                return redirect('/osce/admin/train/train-list')->with('success','编辑成功');
            }
            return redirect()->back()->withInput()->withErrors(['编辑失败']);
        }else{
            return redirect()->back()->withInput()->withErrors(['权限不足']);
        }
    }

    /**
     *删除培训
     * @method GET
     * @url /osce/admin/train/del-train
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        培训id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDelTrain(Request $request)
    {
        $this->validate($request,[
            'id'  =>'required|integer'
        ]);

        try{
            $id = intval($request->get('id'));
            $informTrain = InformTrain::where('id', $id)->first();
            if($informTrain){
                $createId = $informTrain->create_user_id;
            }else{
                throw new \Exception('没有找到当前信息');
            }

//        $manager=config('osce.manager');
//        if($userId!==$createId || $createId!==$manager[0]){
//            return redirect()->back()->withInput()->withErrors('权限不足');
//        }

            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            if(!count($user->roles)){
                throw new \Exception('还没分配角色');
            }
            $userId = $user->id;
            if($userId == $createId || $user->roles[0]->id == config('config.superRoleId')){
                $result = InformTrain::where('id',$id)->delete();
                if($result){
                    return $this->success_data(['删除成功']);
//                    return redirect('/osce/admin/train/train-list')->with('success','删除成功');
                }
                throw new \Exception('删除失败');
//                return redirect()->back()->withErrors(['删除失败']);
            }else{
                throw new \Exception('权限不足');
//                return redirect()->back()->withErrors(['权限不足']);
            }
        } catch(\Exception $ex){
            return $this->fail($ex);
        }

    }

    /**
     *查看考前培训
     * @method GET
     * @url /osce/admin/train/train-detail
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
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
        $train=InformTrain::where('id',$id)->select()->get();
        $clicks=InformTrain::where('id',$id)->first()->clicks;
        $clicks=$clicks+1;
        InformTrain::where('id',$id)->update(['clicks'=>$clicks]);
        foreach($train as $item){
            $data=[
                'id'    =>$item->id,
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
        if($data['attachments']){
            $data['attachments']=unserialize($data['attachments']);
        }

//        if(!$train->isEmpty()){
//            $data = $train->first();
////            $data -> attachments = serialize($data -> attachments);
//            $data -> attachments = unserialize($data -> attachments);
//        }
        return view('osce::admin.examManage.train_detail')->with('data',$data);
    }

    /**
     *上传文件
     * @method GET
     * @url /osce/admin/train/upload-file
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postUploadFile(Request $request){
       try {

           if ($request->hasFile('file'))
           {
               $file   =   $request->file('file');
               $file_ex=$file->getClientOriginalExtension();
               if (!in_array($file_ex, array('docx', 'xlsx'))){
                   throw new \Exception('上传文件类型失败');
//                   return back()->withErrors('上传文件类型失败');
               }
               $path   =   'osce/file/'.date('Y-m-d').'/'.rand(1000,9999).'/';
               $destinationPath    =   public_path($path);
               $fileName           =   $file->getClientOriginalName();
               $file   ->  move($destinationPath,iconv("UTF-8","gb2312",$fileName));
               $pathReturn    =   '/'.$path.$fileName;
           }
           echo json_encode(
               array(
                   "code" => 1,
                   "url" => $pathReturn,
                   "title" => $fileName,
                   "original" => $file->getClientOriginalExtension(),
                   "type" => $file->getClientMimeType(),
                   "size" => $file->getClientSize()
               )
           );
       }catch (\Exception $ex){
           return json_encode($this->fail($ex));
//           throw $ex;
       }
    }


    protected function uploadFile(Request $request){
        try {
            $data   =   [
                'path'  =>  ''
            ];
            if ($request->hasFile('file'))
            {
                $file   =   $request->file('file');
                $path   =   'osce/file/'.date('Y-m-d').'/'.rand(1000,9999).'/';
                $destinationPath    =   public_path($path);
                //.'.'.$file->getClientOriginalExtension()
                $fileName           =   $file->getClientOriginalName();
                $file->move($destinationPath,$fileName);
                $pathReturn    =   '/'.$path.$fileName;
                $data   =   [
                    'path'=>$pathReturn,
                    'name'=>$fileName
                ];
            }
//            return $data;
            echo json_encode(
                $this->success_data($data,1,'上传成功')
            );
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     *跳转新增页面
     * @method GET
     * @url /osce/admin/train/add-train
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddTrain(){
    	return view('osce::admin.examManage.train_add');
    }

    /**
     *下载文件
     * @method post
     * @url /osce/admin/train/download-document
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id                培训id(必须的)
     * * int        attch_index       文件排序(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDownloadDocument(Request $request){
        $this->validate($request,[
            'id'            =>'required|integer',
            'attch_index'   =>'required|integer',
        ]);
        $id     =   $request->get('id');
        $key    =   $request->get('attch_index');
        $info  =   InformTrain::find($id);
        $attchments =   unserialize($info->attachments);

        $thisFile   =   $attchments[$key];
        $fileNameArray   =   explode('/',$thisFile);
        $this->downloadfile(array_pop($fileNameArray),public_path().$thisFile);
    }
    private function downloadfile($filename,$filepath){
        $file=explode('.',$filename);
        $tFile=array_pop($file);
        $filename=md5($filename).'.'.$tFile;
        $filepath   =   iconv('utf-8', 'gbk', $filepath);
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