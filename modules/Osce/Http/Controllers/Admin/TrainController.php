<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:01
 */
namespace Modules\Osce\Http\Controllers;
use Illuminate\Http\Request;
use Modules\Osce\Entities\Reply;
use Modules\Osce\Entities\Train;

class TrainController extends  CommonController{

    public function getTrainList(){

         $list=Train::select()->orderBy('create_at')->get();

         return view()->with('list',$list);
    }


    public function postAddTrain(Request $request){
         $this->validate($request,[
             //��֤����
         ]);

         $data=$request->only([]);
         $result=$this->create($data);
         if($result){
             return view()->with('success','����ɹ�');
         }
             return view()->withErrors('����ʧ��');
    }

    public function getEditTrain(Request $request){
          $this->validate($request,[
              'id'  => 'required|integer'
          ]);
          $id=intval($request->get('id'));
          $list=Train::find($id);
          return view()->with('list',$list);
    }


    public function postEditTrain(Request $request){
          $this->validate($request,[]);

          $data=$request->only([]);
          $result=Train::where('id',$data['id'])->update($data);
            if($result){
                return view()->with('success','�޸ĳɹ�');
            }
            return view()->withErrors('�޸�ʧ��');
    }
//    public function getDelTrain(Request $request){
//        $this->validate($request,[
//            'id'  =>'required|integer'
//        ]);
//
//        $id=intval($request->get('id'));
//        $result=Train::where('id',$id)->deleted();
//        if($result){
//            return view()->with('success','ɾ���ɹ�');
//        }
//        return view()->withErrors('ɾ��ʧ��');
//
//    }
}