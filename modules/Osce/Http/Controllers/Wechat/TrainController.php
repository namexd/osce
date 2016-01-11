<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:01
 */
namespace Modules\Osce\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\InformTrain;
use Modules\Osce\Http\Controllers\CommonController;

class TrainController extends  CommonController{

    /**
     *��ѵ�б�
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
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
                $this->success_rows(404,'���ȵ�½')
            );
        }
        $trainModel=new InformTrain();
        $pagination=$trainModel->getPaginate();

        $list=InformTrain::select()->orderBy('begin_dt')->get();

        return response()->json(
            $this->success_rows(1,'success',$pagination->total(),config('osce.page_size'),$pagination->currentPage(),$list)
        );
    }

    /**
     *������ѵ
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
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
            //��֤����
            'name'                    =>'required|max:64',
            'address'                 =>'required|max:64',
            'begin_dt'                =>'required',
            'end_dt'                  =>'required',
            'teacher'                 =>'required',
            'content'                 =>'required',
            'status'                  =>'required',
            'create_user_id'          =>'required',
        ]);
        $attachments=$request->input('attachments');
        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content','status','create_user_id']);
        $data['attachments']=serialize($attachments);
        $result=$this->create($data);
        if($result){
            return response()->json(
                $this->success_data(1,'������ѵ�ɹ�')
            );
        }
        return response()->json(
            $this->success_data(0,'������ѵʧ��')
        );
    }

    /**
     *�༭��ѵ����
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
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
//        $user=Auth::user();
//        $userId=$user->id;
//        $creteId=InformTrain::where('id',$id)->select()->first()->create_user_id;
//        if($userId!==$creteId){
//            return response()->json(
//                $this->success_rows(3,'false')
//            );
//        }
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
        return response()->json(
            $this->success_data(1,'success',$data)
        );
    }

    /**
     *����༭��ѵ
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
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
        $attachments=$request->input('attachments');
        $data=$request->only(['name','address','begin_dt','end_dt','teacher','content','status','create_user_id']);
        $user=Auth::user();
        $userId=$user->id;
        $creteId=InformTrain::where('id',$data['id'])->select()->first()->create_user_id;
        if($userId!==$creteId){
            return response()->json(
                $this->success_rows(3,'false')
            );
        }
        $data['attachments']=unserialize($attachments);
        $result=InformTrain::where('id',$data['id'])->update($data);
        if($result){
            return response()->json(
                $this->success_rows(1,'success')
            );
        }
        return response()->json(
            $this->success_rows(0,'false')
        );
    }

    /**
     *ɾ����ѵ
     * @method GET
     * @url /user/
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
     * * string        ����Ӣ����        ����������(�����)
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
        if($userId!==$creteId){
            return response()->json(
                $this->success_rows(3,'false')
            );
        }
        $result=InformTrain::where('id',$id)->delete();
        if($result){
            return response()->json(
                $this->success_rows(1,'success')
            );
        }
        return response()->json(
            $this->success_rows(0,'false')
        );
    }
}