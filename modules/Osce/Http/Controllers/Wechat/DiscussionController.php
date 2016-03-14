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

class DiscussionController extends CommonController
{

    /**
     *问题列表页面数据
     * @method GET
     * @url /osce/wechat/discussion/question-list
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        page        页码(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getQuestionList(Request $request)
    {
        $this->validate($request, [
            'page' => 'sometimes|integer'
        ]);
        $user = Auth::user();
        $userId = $user->id;
        if (!$userId) {
            return response()->json(
                $this->success_rows(2, '请先登陆')
            );
        }
        $page = $request->get('page', 1);

        $discussionModel = new Discussion();
        $pagination = $discussionModel->getDiscussionPagination();
        $row = Discussion::where('pid', 0)->select()->orderBy('created_at', 'desc')->get();

        $list = [];
        foreach ($row as $item) {
            $countReply = Discussion::where('pid', $item->id)->count();
            $time = time() - strtotime($item->created_at);
            if ($time < 0) {
                $time = $time;
            } else {
                if ($time < 60) {
                    $time = $time . '秒前';
                } else {
                    if ($time < 3600) {
                        $time = floor($time / 60) . '分钟前';
                    } else {
                        if ($time < 86400) {
                            $time = floor($time / 3600) . '小时前';
                        } else {
                            if ($time < 2592000) {
                                $time = floor($time / 86400) . '天前';
                            } else {
                                if ($time < 31536000) {
                                    $time = floor($time / 2592000) . '月前';
                                } else {
                                    $time = floor($time / 31536000) . '年前';
                                }
                            }
                        }
                    }
                }
            }

            $list[] = [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'create_at' => $item->created_at,
                'user' => $item->getAuthor,
                'time' => $time,
                'count' => $countReply,
            ];
        }
        return response()->json(
            $this->success_rows(1, 'success', $pagination->total(), $pagesize = config('msc.page_size'),
                $pagination->currentPage(), $list)
        );
//          return view('osce::wechat.discussion.discussion_list')->with(['list'=>$list,'pagination'=>$pagination]);
    }

    /**
     *请求列表登录页
     * @method GET
     * @url /osce/wechat/discussion/discussion-lists
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
    public function getDiscussionLists()
    {
        return view('osce::wechat.discussion.discussion_list');
    }

    /**
     *查看问题
     * @method GET
     * @url /osce/wechat/discussion/check-question
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        主键id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCheckQuestion(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        try {
            $user = Auth::user();
            if (is_null($user)) {
                throw new \Exception('对不起，你当前没有关注我');
            }
            $userId = $user->id;
            $manager = config('osce.manager');
            if (!$userId) {
                return \Response::json(array('code' => 2));
            }
            $id = intval($request->get('id'));
            $createId = Discussion::where('id', $id)->select()->first()->create_user_id;

            if ($createId != $userId) {
                $url = 1;
            } elseif ($userId == $manager[0]) {
                $url = 2;
            } else {
                $url = 2;
            }
            $list = Discussion::where('id', $id)->select()->get();
            $discussionModel = new Discussion();
            $pagination = $discussionModel->getReplyPagination($id);
            foreach ($list as $item) {
                $question = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'create_at' => $item->created_at,
                    'name' => $item->getAuthor,
                ];
            }


            $countReply = Discussion::where('pid', $id)->count();

            //回复内容
            $replys = Discussion::where('pid', $id)->select()->get();
            $data = [];
            foreach ($replys as $itm) {
                $time = time() - strtotime($item->created_at);
                if ($time < 0) {
                    $time = $time;
                } else {
                    if ($time < 60) {
                        $time = $time . '秒前';
                    } else {
                        if ($time < 3600) {
                            $time = floor($time / 60) . '分钟前';
                        } else {
                            if ($time < 86400) {
                                $time = floor($time / 3600) . '小时前';
                            } else {
                                if ($time < 2592000) {
                                    $time = floor($time / 86400) . '天前';
                                } else {
                                    if ($time < 31536000) {
                                        $time = floor($time / 2592000) . '月前';
                                    } else {
                                        $time = floor($time / 31536000) . '年前';
                                    }
                                }
                            }
                        }
                    }
                }
                $data[] = [
                    'id' => $itm->id,
                    'title' => $itm->title,
                    'content' => $itm->content,
                    'name' => $itm->getAuthor,
                    'time' => $time,
                    'update_at' => $itm->update_at,
                ];
            }
            $row = array(
                'question' => $question,
                'countReply' => $countReply,
            );
            return view('osce::wechat.discussion.discussion_detail')->with([
                'data' => $data,
                'row' => $row,
                'url' => $url
            ]);
        } catch (\Exception $ex) {
            return view('osce::wechat.discussion.discussion_detail')->with([
                'data' => $data,
                'row' => $row,
                'url' => $url
            ])->withErrors($ex->getMessage());
        }
    }


    public function getAddQuestion()
    {
        return view('osce::wechat.discussion.discussion_quiz');
    }

    /**
     *提交问题
     * @method GET
     * @url /osce/wechat/discussion/add-question
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        title         标题(必须的)
     * * string        content       内容(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddQuestion(Request $request)
    {
        //验证规则
        $this->validate($request, [
            'title' => 'required|max:256',
            'content' => 'required',
        ]);
        $user = Auth::user();
        $userId = $user->id;
        if (!$userId) {
            return response()->json(
                $this->success_rows(2, '请先登陆')
            );
        }
        $data = $request->only(['title', 'content']);
        $data['create_user_id'] = $userId;
        $data['pid'] = 0;
        $result = Discussion::create($data);
        if ($result) {
            return redirect('osce/wechat/discussion/question-lists')->with('success', '提交问题成功');
        }
        return redirect()->back()->withErrors(new \Exception('提交问题失败'));
    }


    /**
     *跳转回复页面
     * @method GET
     * @url /osce/wechat/discussion/add-reply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        问题id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getAddReply(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = intval($request->get('id'));
        $list = Discussion::where('id', $id)->select()->get();

        return view('osce::wechat.discussion.discussion_response', ['list' => $list], ['id' => $id]);
    }

    /**
     *提交回复
     * @method POST
     * @url /osce/wechat/discussion/add-reply
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        pid        问题Id(必须的)
     * * string     content    内容(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddReply(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'content' => 'required',
        ]);
        $user = Auth::user();
        $userId = $user->id;
        if (!$userId) {
            return redirect('osce/admin/login/index');
        }
        $data = $request->only(['id', 'content']);
        $result = Discussion::create([
            'content' => $data['content'],
            'pid' => $data['id'],
            'create_user_id' => $userId
        ]);
        if ($result) {
//              return  redirect('/osce/wechat/discussion/check-question?id='.$data['id'])->withErrors('回复成功');
            return response()->json(
                $this->success_data($data, 1, '回复成功')
            );
        }
//              return  redirect()->back()->withErrors('回复失败');
        return response()->json(
            $this->success_data($data, 2, '回复失败')
        );
    }

    /**
     *删除问题
     * @method GET
     * @url /osce/wechat/discussion/del-question
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        问题Id(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getDelQuestion(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $user = Auth::user();
        $userId = $user->id;
        if (!$userId) {
            return redirect('osce/admin/login/index');
        }
        $id = $request->get('id');
        $createId = Discussion::where('id', $id)->select()->first();
        if (!$createId) {
//             return  redirect()->back()->withErrors('删除失败');
            return \Response::json(array('code' => 2));
        }
        $createId = $createId->create_user_id;
        $manager = config('osce.manager');
        if (($userId == $createId) || ($userId == $manager[0])) {
            $pid = Discussion::where('pid', $id)->select('pid')->first();
            if ($pid) {
                $result = Discussion::where('pid', $id)->delete();
                if ($result) {
                    $result = Discussion::where('id', $id)->delete();
                    if ($result) {
//                         return redirect('osce/wechat/discussion/question-lists')->with('success','删除问题成功');
                        return \Response::json(array('code' => 1));

                    } else {
//                         return  redirect()->back()->withErrors('删除失败');
                        return \Response::json(array('code' => 0));

                    }
                }
            } else {
                $result = Discussion::where('id', $id)->delete();
                if ($result) {
//                     return redirect('osce/wechat/discussion/question-lists')->with('success','删除成功');
                    return \Response::json(array('code' => 1));

                } else {
//                     return  redirect()->back()->withErrors('删除失败');
                    return \Response::json(array('code' => 0));

                }
            }
        }

//         return  redirect()->back()->withErrors(new \Exception('删除失败'));
        return \Response::json(array('code' => 3));

    }

    /**
     *编辑问题
     * @method GET
     * @url /osce/wechat/discussion/edit-question
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        问题ID(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getEditQuestion(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);
        $id = $request->get('id');
        $list = Discussion::where('id', $id)->select()->get();
        return view('osce::wechat.discussion.discussion_edit')->with('list', $list)->with('id', $id);
    }

    /**
     *保存编辑
     * @method POST
     * @url /osce/wechat/discussion/edit-question
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int            id               主键id(必须的)
     * * string        title             标题(必须的)
     * * string        content           内容(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postEditQuestion(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
        ]);

        $id = $request->get('id');
        $title = $request->get('title');
        $content = $request->get('content');

        $result = Discussion::where('id', $id)->update(['title' => $title, 'content' => $content]);
        if ($result) {
            return redirect('osce/wechat/discussion/question-lists')->with('success', '编辑问题成功');
        }
        return redirect()->back()->withErrors(new \Exception('编辑失败'));

    }


    /**
     *获取回复内容
     * @method GET
     * @url osce/wechat/discussion/check-question-json
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int              id               问题id(必须的)
     * * int              pagesize         页码(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhouchong@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getCheckQuestions(Request $request)
    {
        \Log::alert('11234'. '请求讨论内容详情！');
        $this->validate($request, [
            'id' => 'required|integer',
            'pagesize' => 'sometimes|integer',
        ]);
        $pagesize = $request->get('pagesize', 1);
        $user = Auth::user();
        $userId = $user->id;
        if (!$userId) {
            return redirect('osce/admin/login/index');
        }
        $id = intval($request->get('id'));


        $discussionModel = new Discussion();
        $pagination = $discussionModel->getReplyPagination($id);

        //回复内容
        $replys = Discussion::where('pid', $id)->select()->get();
        $data = [];
        foreach ($replys as $itm) {
            $time = time() - strtotime($itm->created_at);

            if ($time < 0) {
                $time = $time;
            } else {
                if ($time < 60) {
                    $time = $time . '秒前';
                } else {
                    if ($time < 3600) {
                        $time = floor($time / 60) . '分钟前';
                    } else {
                        if ($time < 86400) {
                            $time = floor($time / 3600) . '小时前';
                        } else {
                            if ($time < 2592000) {
                                $time = floor($time / 86400) . '天前';
                            } else {
                                if ($time < 31536000) {
                                    $time = floor($time / 2592000) . '月前';
                                } else {
                                    $time = floor($time / 31536000) . '年前';
                                }
                            }
                        }
                    }
                }
            }
            $data[] = [
                'id' => $itm->id,
                'title' => $itm->title,
                'content' => $itm->content,
                'name' => $itm->getAuthor,
                'time' => $time,
                'update_at' => $itm->update_at,
            ];
        }

        return response()->json(
            $this->success_rows(1, 'success', $pagination->total(), $pagesize = config('msc.page_size'),
                $pagination->currentPage(), $data)
        );
    }
}

