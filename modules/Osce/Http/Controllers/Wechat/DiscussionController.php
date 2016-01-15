<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/9 0009
 * Time: 15:13
 */
namespace Modules\Osce\Http\Controllers\Wechat;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\Discussion;
use Modules\Osce\Http\Controllers\CommonController;

class DiscussionController extends  CommonController{

    /**
     *鑾峰彇闂鍒楄〃
     * @method GET
     * @url /osce/wechat/discussion/question-list
     * @access public
     *
     * @param Request $request post璇锋眰<br><br>
     * <b>post璇锋眰瀛楁锛�</b>
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function getQuestionList(){
//        $user=Auth::user();
//        $userId=$user->id;
//        if(!$userId){
//            return response()->json(
//                $this->success_rows(2,'璇峰厛鐧诲綍')
//            );
//        }

          $discussionModel	=	new Discussion();
          $pagination				=	$discussionModel	->	getDiscussionPagination();
          $row=Discussion::where('pid',0)->select()->orderBy('created_at','desc')->get();
          $list=[];
          foreach($row as $item){
              $countReply=Discussion::where('pid',$item->id)->count();
              $time=time()-strtotime($item->created_at);
              if ($time < 0) {
                 $time = $time;
              } else {
                  if ($time < 60) {
                      $time= $time . '绉掑墠';
                  } else {
                      if ($time < 3600) {
                          $time=  floor($time / 60) . '鍒嗛挓鍓�';
                      } else {
                          if ($time < 86400) {
                              $time= floor($time / 3600) . '灏忔椂鍓�';
                          } else {
                              if ($time < 259200) {//3澶╁唴
                                  $time= floor($time / 86400) . '澶╁墠';
                              } else {
                                  $time =  $time;
                              }
                          }
                      }
                  }
              }
              $list[]=[
                'id' =>$item->id,
                'title' =>$item->title,
                'content' =>$item->content,
                'create_at' =>$item->created_at,
                'name'   =>$item->getAuthor,
                'time' =>$time,
                'count' =>$countReply,
            ];
          }
          return view('osce::wechat.discussion.discussion_list')->with(['list'=>$list,'pagination'=>$pagination]);
      }

    /**
     *鏌ョ湅闂
     * @method GET
     * @url /osce/wechat/discussion/check-question
     * @access public
     *
     * @param Request $request post璇锋眰<br><br>
     * <b>post璇锋眰瀛楁锛�</b>
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function getCheckQuestion(Request $request){
          // $user=Auth::user();
          // $userId=$user->id;
          // if(!$userId){
          //     return \Response::json(array('code'=>2));
          // }
          $id    =   intval($request   ->  get('id'));
          $list=Discussion::where('id',$id)->select()->get();
          $discussionModel	=	new Discussion();
          $pagination				=	$discussionModel	->	getDiscussionPagination();
          foreach($list as $item){
              $question=[
                  'id'             =>$item->id,
                  'title'          =>$item->title,
                  'context'        =>$item->context,
                  'create_user'    =>$item->getAuthor,
                  'create_at'      =>$item->create_at,
                  'update_at'      =>$item->update_at,
              ];
          }
          $countReply=Discussion::where('pid',$id)->count();

          //鑾峰彇鍥炲浜轰俊鎭�
           $replys=Discussion::where('pid',$id)->select()->get();
//           $data=[];
//          foreach($replys as $itm){
//              $data[]=[
//                  'id'             =>$itm->id,
//                  'title'          =>$itm->title,
//                  'context'        =>$itm->context,
//                  'create_user'    =>$itm->getAuthor,
//                  'create_at'      =>$itm->create_at,
//                  'update_at'      =>$itm->update_at,
//              ];
//          }
            $row=array(
                // 'question'   =>$question,
                'replys' =>$replys,
                'countReply' =>$countReply,
            );

          return view('osce::wechat.discussion.discussion_detail')->with(['row'=>$row,'pagination'=>$pagination]);

      }


    /**
     *鎻愪氦闂
     * @method POST
     * @url /osce/wechat/discussion/add-question
     * @access public
     *
     * @param Request $request post璇锋眰<br><br>
     * <b>post璇锋眰瀛楁锛�</b>
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function postAddQuestion(Request $request){
          //楠岃瘉瑙勫垯
          $this->validate($request,[
               'title'    =>'required|max:256',
               'content'  =>'required',
          ]);
          $user=Auth::user();
          $userId=$user->id;
//          if(!$userId){
//              return response()->json(
//                  $this->success_rows(2,'璇峰厛鐧诲綍')
//              );
//          }
          $data=$request->only(['title','content']);
          $data['user_id']=$userId;
          $data['pdi']=0;
          $discussionModel= new Discussion();
          $result=$discussionModel->save($data);
          if($result){
              return view()->with('success','鎻愪氦鎴愬姛');
          }
          return redirect()->back()->withInput()->withErrors('鎻愪氦澶辫触');
      }

    /**
     *鎻愪氦鍥炵瓟
     * @method POST
     * @url    /osce/wechat/discussion/add-reply
     * @access public
     *
     * @param Request $request post璇锋眰<br><br>
     * <b>post璇锋眰瀛楁锛�</b>
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function postAddReply(Request $request){
          $this->validate($request,[
               'pid'      => 'required|integer',
               'content'  => 'required|integer',
          ]);
          $user=Auth::user();
          $userId=$user->id;
//          if(!$userId){
//              return response()->json(
//                  $this->success_rows(2,'璇峰厛鐧诲綍')
//              );
//          }
          $data=$request->only(['pid','content']);
          $result=Discussion::insert(['content'=>$data['content'],'pid'=>$data['pid'],'create_user_id'=>$userId]);
          if($result){
              return view()->with('success','鍥炲鎴愬姛');
          }
          return redirect()->back()->withInput()->withErrors('鍥炲澶辫触');
      }

    /**
     *鍒犻櫎璇ラ棶棰�
     * @method GET
     * @url /osce/wechat/discussion/del-question
     * @access public
     *
     * @param Request $request post璇锋眰<br><br>
     * <b>post璇锋眰瀛楁锛�</b>
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     * * string        鍙傛暟鑻辨枃鍚�        鍙傛暟涓枃鍚�(蹇呴』鐨�)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
     public function getDelQuestion(Request $request){
          $this->validate($request,[
              'id'  =>'required|integer'
          ]);
//         $user=Auth::user();
//         $userId=$user->id;
//         if(!$userId){
//             return response()->json(
//                 $this->success_rows(2,'璇峰厛鐧诲綍')
//             );
//         }

         $id=$request->get('id');
//         $manager=config('osce.manager');
//         if($userId!==$id || $id!==$manager[0]){
//             return \Response::json(array('code'=>3));
//         }
         $result=Discussion::where('id',$id)->delete();
         if($result){
             return view()->with('success','鍒犻櫎鎴愬姛');
         }
         return redirect()->back()->withInput()->withErrors('鍒犻櫎澶辫触');
     }
}