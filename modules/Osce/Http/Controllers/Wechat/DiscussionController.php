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
     *��ȡ�����б�
     * @method GET
     * @url /osce/wechat/discussion/question-list
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

      public function getQuestionList(){
          $user=Auth::user();
          if(!$user){
              return response()->json(
                  $this->success_rows(2,'���ȵ�¼')
              );
          }
          $discussionModel	=	new Discussion();
          $pagination				=	$discussionModel	->	getDiscussionPagination();
          $list=Discussion::where('pid',0)->select()->orderBy('created_at','desc')->get();

          return response()->json(
              $this->success_rows(1,'success',$pagination->total(),config('osce.page_size'),$pagination->currentPage(),$list)
          );
      }


    /**
     *�鿴����
     * @method GET
     * @url /osce/wechat/discussion/check-question
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        ����Ӣ����        ����������(�����)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
      public function getCheckQuestion(Request $request){
          $user=Auth::user();
          if(!$user){
              return response()->json(
                  $this->success_rows(2,'���ȵ�¼')
              );
          }
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

          //��ȡ�ظ�����Ϣ
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
                'question'   =>$question,
                'replys' =>$replys,
                'countReply' =>$countReply,
            );

          return response()->json(
              $this->success_rows(1,'success',$pagination->total(),config('osce.page_size'),$pagination->currentPage(),$row)
          );
      }

    /**
     *�ύ����
     * @method POST
     * @url /osce/wechat/discussion/add-question
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
      public function postAddQuestion(Request $request){
          //��֤����
          $this->validate($request,[
               'title'    =>'required|max:256',
               'content'  =>'required',
          ]);
          $user=Auth::user();
          if(!$user){
            return response()->json(
                $this->success_data(2,'���ȵ�¼')
            );
          }
          $userId=$user->id;
          $data=$request->only(['title','content']);
          $data['user_id']=$userId;
          $discussionModel= new Discussion();
          $result=$discussionModel->save($data);
          if($result){
              return response()->json(
                  $this->success_data(1,'success')
              );
          }
          return response()->json(
              $this->success_data(0,'false')
          );
      }

    /**
     *�ύ�ش�
     * @method POST
     * @url    /osce/wechat/discussion/add-reply
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
      public function postAddReply(Request $request){
          $this->validate($request,[
               'pid'      => 'required|integer',
               'content'  => 'required|integer',
          ]);
          $user=Auth::users();
          if(!$user){
              return response()->json(
                  $this->success_data(2,'���ȵ�¼')
              );
          }
          $userId=$user->id;
          $data=$request->only(['pid','content']);
          $result=Discussion::insert(['content'=>$data['content'],'pid'=>$data['pid'],'create_user_id'=>$userId]);
          if($result){
              return response()->json(
                  $this->success_data($data,1,'success')
              );
          }
          return response()->json(
              $this->success_data($data,0,'false')
          );
      }

    /**
     *ɾ��������
     * @method GET
     * @url /osce/wechat/discussion/del-question
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
     public function getDelQuestion(Request $request){
          $this->validate($request,[
              'id'  =>'required|integer'
          ]);
         $user=Auth::users();
         if(!$user){
             return response()->json(
                 $this->success_data($user,2,'false')
             );
         }
         $userId=$user->id;
         $id=$request->get('id');
         $manager=config('osce.manager');
         if($userId!==$id || $id!==$manager[0]){
              return response()->json(
                  $this->success_data($user,3,'false')
              );
         }
         $result=Discussion::where('id',$id)->delete();
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