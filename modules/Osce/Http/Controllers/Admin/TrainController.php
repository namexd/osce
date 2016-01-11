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
             //验证规则
         ]);

         $data=$request->only([]);
         $result=$this->create($data);
         if($result){
             return view()->with('success','新添成功');
         }
             return view()->withErrors('新添失败');
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
                return view()->with('success','修改成功');
            }
            return view()->withErrors('修改失败');
    }
//    public function getDelTrain(Request $request){
//        $this->validate($request,[
//            'id'  =>'required|integer'
//        ]);
//
//        $id=intval($request->get('id'));
//        $result=Train::where('id',$id)->deleted();
//        if($result){
//            return view()->with('success','删除成功');
//        }
//        return view()->withErrors('删除失败');
//
//    }
}