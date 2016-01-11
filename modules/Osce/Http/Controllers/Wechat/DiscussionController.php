<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/9 0009
 * Time: 15:13
 */
namespace Modules\Osce\Http\Controllers\Wechat;
use App\Http\Requests\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Osce\Entities\Discussion;
use Modules\Osce\Entities\Reply;
use Modules\Osce\Http\Controllers\CommonController;

class DiscussionController extends  CommonController{

    /**
     *获取问题列表
     * @method GET
     * @url /user/
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
//          $user=Auth::users();
//          if(!$user){
//              return response()->json(
//                  $this->success_rows(1,'请先登录')
//              );
//          }
          $discussionModel	=	new Discussion();
          $pagination				=	$discussionModel	->	getDiscussionPagination();
          $list=Discussion::select()->orderBy('create_at','desc');

            $data=[];
            foreach($list as $item){
               $data[]=[
                   'id'             =>$item->id,
                   'title'          =>$item->title,
                   'context'        =>$item->context,
                   'question'       =>$item->question,
                   'create_user'    =>$item->getAuthor,
                   'create_at'      =>$item->create_at,
                   'update_at'      =>$item->update_at,
               ];
            }

          return response()->json(
              $this->success_rows(1,'获取问题列表',$pagination->total(),config('osce.page_size'),$pagination->currentPage(),$data)
          );
      }


    /**
     *查看问题
     * @method GET
     * @url /user/
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
          $user=Auth::users();
          if(!$user){
              return response()->json(
                  $this->success_rows(1,'请先登录')
              );
          }
          $id    =   intval($request   ->  get('id'));
          $list=Discussion::where('id',$id)->select();

          foreach($list as $item){
              $data=[
                  'id'             =>$item->id,
                  'title'          =>$item->title,
                  'context'        =>$item->context,
                  'question'       =>$item->question,
                  'create_user'    =>$item->getAuthor,
                  'create_at'      =>$item->create_at,
                  'update_at'      =>$item->update_at,
              ];
          }
          //获取回复人信息
            $replyModel=new Reply();
//            $pagination				=	$replyModel	->	getPaginate($user->id);
            $replys=$replyModel->getReplyList($id);

            $row=array(
                'replys' =>$replys,
                'data'   =>$data,
            );
          return response()->json(
              $this->success_rows(1,'success',$row)
          );
      }

    /**
     *提交问题
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
      public function postAddQuestion(Request $request){
          //验证规则
          $this->validate($request,[

          ]);
          $user=Auth::users();
          if(!$user){
            return response()->json(
                $this->success_data(1,'请先登录')
            );
          }
          $userId=$user->id;
          $data=$request->only(['']);
          $data['user_id']=$userId;
          $discussionModel= new Discussion();
          $result=$discussionModel->save($data);
          if($result){
              return response()->json(
                  $this->success_data(1,'提交问题成功')
              );
          }
          return response()->json(
              $this->success_data(1,'提交问题失败')
          );
      }

    /**
     *提交回答
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
      public function postAddReply(Request $request){
          $this->validate($request,[

          ]);
          $user=Auth::users();
          if(!$user){
              return response()->json(
                  $this->success_data(1,'请先登录')
              );
          }
          $userId=$user->id;
          $data=$request->only([]);
          $replyModel= new Reply();
          $data['user_id']=$userId;
          $result=$replyModel->save($data);
          if($result){
              return response()->json(
                  $this->success_data(1,'回复成功')
              );
          }
          return response()->json(
              $this->success_data(1,'回复失败')
          );
      }
}