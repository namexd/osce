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
     *获取问题列表
     * @method GET
     * @url /osce/wechat/discussion/question-list
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
      public function getQuestionList(){
//        $user=Auth::user();
//        $userId=$user->id;
//        if(!$userId){
//            return response()->json(
//                $this->success_rows(2,'请先登录')
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
                      $time= $time . '秒前';
                  } else {
                      if ($time < 3600) {
                          $time=  floor($time / 60) . '分钟前';
                      } else {
                          if ($time < 86400) {
                              $time= floor($time / 3600) . '小时前';
                          } else {
                              if ($time < 259200) {//3天内
                                  $time= floor($time / 86400) . '天前';
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
     *查看问题
     * @method GET
     * @url /osce/wechat/discussion/check-question
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

          //获取回复人信息
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
     *提交问题
     * @method POST
     * @url /osce/wechat/discussion/add-question
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
      public function postAddQuestion(Request $request){
          //验证规则
          $this->validate($request,[
               'title'    =>'required|max:256',
               'content'  =>'required',
          ]);
          $user=Auth::user();
          $userId=$user->id;
//          if(!$userId){
//              return response()->json(
//                  $this->success_rows(2,'请先登录')
//              );
//          }
          $data=$request->only(['title','content']);
          $data['user_id']=$userId;
          $data['pdi']=0;
          $discussionModel= new Discussion();
          $result=$discussionModel->save($data);
          if($result){
              return view()->with('success','提交成功');
          }
          return redirect()->back()->withInput()->withErrors('提交失败');
      }

    /**
     *提交回答
     * @method POST
     * @url    /osce/wechat/discussion/add-reply
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
      public function postAddReply(Request $request){
          $this->validate($request,[
               'pid'      => 'required|integer',
               'content'  => 'required|integer',
          ]);
          $user=Auth::user();
          $userId=$user->id;
//          if(!$userId){
//              return response()->json(
//                  $this->success_rows(2,'请先登录')
//              );
//          }
          $data=$request->only(['pid','content']);
          $result=Discussion::insert(['content'=>$data['content'],'pid'=>$data['pid'],'create_user_id'=>$userId]);
          if($result){
              return view()->with('success','回复成功');
          }
          return redirect()->back()->withInput()->withErrors('回复失败');
      }

    /**
     *删除该问题
     * @method GET
     * @url /osce/wechat/discussion/del-question
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
     public function getDelQuestion(Request $request){
          $this->validate($request,[
              'id'  =>'required|integer'
          ]);
//         $user=Auth::user();
//         $userId=$user->id;
//         if(!$userId){
//             return response()->json(
//                 $this->success_rows(2,'请先登录')
//             );
//         }

         $id=$request->get('id');
//         $manager=config('osce.manager');
//         if($userId!==$id || $id!==$manager[0]){
//             return \Response::json(array('code'=>3));
//         }
         $result=Discussion::where('id',$id)->delete();
         if($result){
             return view()->with('success','删除成功');
         }
         return redirect()->back()->withInput()->withErrors('删除失败');
     }
}