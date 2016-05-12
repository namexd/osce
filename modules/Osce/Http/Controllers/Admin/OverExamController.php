<?php

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamMidway\ExamMidwayRepository;
use Modules\Osce\Http\Controllers\CommonController;

class OverExamController extends CommonController
{
    /**
     * 重置考试的着陆页
     * @url osce/over-exam/index
     * @access public
     * @请求字段：
     * @return \Illuminate\View\View
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-14
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function index()
    {
        return view('osce::admin.destroy_exam');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        throw new \Exception('目前方法尚未实现');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        throw new \Exception('目前方法尚未实现');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        throw new \Exception('目前方法尚未实现');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        throw new \Exception('目前方法尚未实现');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        throw new \Exception('目前方法尚未实现');
    }

    /**
     * 重置一场考试（注意：此考试无法继续使用）
     * @url osce/over-exam/destroy
     * @access public
     * @param Request $request
     * @param ExamMidwayRepository $examMidway
     * @请求字段：
     * 考试id exam_id
     * @return string
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-14
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function destroy(Request $request, ExamMidwayRepository $examMidway)
    {
        $this->validate($request, [
            'exam_id' => 'required|integer'
        ]);

        $examId = $request->input('exam_id');

        try {
            $examMidway->reset($examId);

            return '成功！' . mt_rand(1, 500);
        } catch (\Exception $ex) {
            return '失败！' . mt_rand(1, 500) . '错误原因：' . $ex->getMessage();
        }

    }
}
