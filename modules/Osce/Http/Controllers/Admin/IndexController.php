<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2016/1/18
 * Time: 15:21
 */

namespace Modules\Osce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\Common as AppCommon;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Student;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;


class IndexController extends CommonController
{
    public function dashboard(){
        $exam   =   new Exam();
        $data   =   $exam->selectExamToday();
        if(count($data) > 0){
            return view('osce::admin.index.examboard',['data'=>$data]);
        }else{
            return view('osce::admin.index.dashboard');
        }
    }

    /**
     * 设置开考
     * @api GET /osce/admin/indwx/set-exam
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        考试ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getSetExam(Request $request)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try{
            $this->validate($request,[
                'id'    => 'required|integer'
            ],[
                'id.required'   => '没有考试ID',
                'id.integer'   => 'ID必须为数字'
            ]);
            //获取考试ID
            $exam_id = $request->get('id');
            if(Exam::where('status','=',1)->count()>0)
            {
                throw new \Exception('当前已经有一场正在进行的考试了');
            }
            //查询考试实例
            $exam = Exam::find($exam_id);
            if(is_null($exam)){
                throw new \Exception('没有找到相关考试');
            }
            $exam->status = 1;      //将考试状态 设为正在考试
            if($exam->save())
            {
                //获取考试对应的场次
                $examScreening = Common::getExamScreening($exam->id);
                if($examScreening->status != 1)
                {
                    $examScreening->status = 1;
                    if(!$examScreening->save())
                    {
                        throw new \Exception('场次开考失败！');
                    }
                }
                $examScreeningId = $examScreening->id;
                $time = date('Y-m-d H:i:s', time());
                //todo 添加所有的学生到queue表
//                $students = Student::query()->where(['exam_id' => $exam_id])
//                    ->get()
//                    ->pluck('id')
//                    ->toArray();
                $students = ExamPlan::query()
                    ->where(['exam_id' => $exam_id, 'exam_screening_id' => $examScreeningId])
                    ->get()
//                    ->groupBy('student_id')
                    ->pluck('student_id')
                    ->toArray();
                $students = array_unique($students);

                $screeningStudentData = [];
                $examQue = new ExamQueue();
                foreach ($students as $s) {
                    $screeningStudentData[] = [
                        'student_id'        => $s,
                        'signin_dt'         => $time,
                        'exam_screening_id' => $examScreeningId,
                        'is_signin'         => 1
                    ];
                    //创建考试队列
                    $examQue ->createExamQueue($exam_id, $s, time(), $examScreeningId);
                }
                ExamScreeningStudent::insert($screeningStudentData);

                // 更新全部缓存
                Common::updateAllCache($exam_id, $examScreeningId);
                $connection->commit();
                //开考成功，返回首页
                return redirect()->route('osce.admin.index.dashboard');
            }else
            {
                throw new \Exception('开考失败！');
            }
        } catch(\Exception $ex){
            $connection->rollback();
            return redirect()->back()->withErrors(['msg'=>$ex->getMessage()]);
        }
    }

    /**
     * 发布成绩
     * @param Request $request
     * @return $this
     */
    public function getReleaseScore(Request $request)
    {
        try{
            $this->validate($request,[
                'id'    => 'required|integer'
            ],[
                'id.required'   => '没有考试ID',
                'id.integer'    => 'ID必须为数字'
            ]);
            //获取考试ID
            $exam_id = intval($request->get('id'));
            $confirm = $request->get('confirm')? :null;

            $data = Student::leftJoin('exam_result', 'exam_result.student_id', '=', 'student.id')
                ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
                ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
                ->where('exam_screening.exam_id', '=', $exam_id)
                ->groupBy('exam_result.student_id')->orderBy('score_total', 'desc')
                ->select(\DB::raw(implode(',',
                    [
                        'student.name',
                        'student.mobile',
                        'sum(exam_result.score) as score_total',
                        'sum(exam_result.original_score) as original_score_total',
                    ])))
                ->having('score_total', '<>', 0)
                ->having('student.name', '<>', '高让')
                ->get();

            //根据confirm参数判断是否发送短信
            if(is_null($confirm)){
                return view('osce::admin.index.send',['data'=>$data, 'exam_id'=>$exam_id]);
            }else
            {
                if($data->isEmpty()){
                    return $this->success_data([], -1, '数据为空，发送失败！');
                }else
                {
                    foreach ($data as $item)
                    {
                        $content = $item->name." 同学，临床医学专业临床技能多站考试你的成绩为 ".round($item->score_total, 2)." 分。";
                        //模板1类型 mb1 模板2类型 mb2 依此类推 变量name1 name2 以此类推
                        $template = array("mb"=>"mb3",'name1'=> $item->name,"name2"=>round($item->score_total, 2));
                        AppCommon::sendSms($item->mobile, $content,$template);
                    }
                    $messageContro = \App::make('Modules\Osce\Http\Controllers\Admin\MessageController');
                    $messageContro ->getSendMessage();
                    return $this->success_data([], 1, '发送成功！');
                }
            }

        }catch (\Exception $ex)
        {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }
}