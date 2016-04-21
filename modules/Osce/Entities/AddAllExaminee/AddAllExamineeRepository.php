<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/20
 * Time: 17:07
 */

namespace Modules\Osce\Entities\AddAllExaminee;

use App\Entities\User;
use App\Repositories\Common;
use Illuminate\Container\Container as App;
use Illuminate\Http\Request;
use Modules\Osce\Entities\AddAllExaminee\Traits\CheckTraits;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\Student;

class AddAllExamineeRepository extends AbstractAddAllExaminee
{
    use CheckTraits;
    public function model()
    {
        return 'Modules\Osce\Entities\AddAllExaminee\AddAllExaminee';
    }

    public function __construct(App $app, Request $request, $fileName)
    {
        parent::__construct($app);
        $this->model->setData($this->getData($request, $fileName));
    }

    public function getData(Request $request, $fileName)
    {
        return Common::getExclData($request, $fileName);
    }

    public function importStudent($examId, $studentModel)
    {
        $backArr = [];
        $studentArrays = [];

        $total = 0;     //处理过的考生总数
        $sucNum = 0;    //导入成功的学生数
        $exiNum = 0;    //已经存在的学生数
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();

        try {
            $data = $this->model->getData();
            
            foreach ($data as $key => $studentData) {
                $total++;
                //性别处理
                $studentData['gender'] = $this->model->handleSex($studentData['gender']);

                //姓名不能为空
                if (empty(trim($studentData['name']))) {
                    if (!empty($studentData['idcard']) && !empty($studentData['mobile'])) {
                        $backArr[] = ['key' => $key + 2, 'title' => 'name'];
                    }
                    continue;
                }

                //验证
                $this->model->check($studentData, $key + 2);

                $user = User::where('username', $studentData['mobile'])->select('id')->first();
                if ($user) {
                    $student = $studentModel->where('user_id', $user->id)->where('exam_id', $examId)->first();
                } else {
                    $student = null;
                }

                //考生存在就跳过
                if ($student) {
                    $exiNum++;
                    continue;
                }

                //用户数据
                $userData = [
                    'name' => $studentData['name'],
                    'gender' => $studentData['gender'],
                    'idcard' => $studentData['idcard'],
                    'mobile' => $studentData['mobile'],
                    'code' => $studentData['code'],
                    'avatar' => $studentData['avator'],
                    'email' => $studentData['email']
                ];
                //处理用户数据
                $userData = $studentModel->handleUser($userData);

                //考生数据
                $studentArray = [
                    'name' => $studentData['name'],
                    'idcard' => $studentData['idcard'],
                    'mobile' => $studentData['mobile'],
                    'code' => $studentData['code'],
                    'avator' => $studentData['avator'],
                    'description' => $studentData['description'],
                    'exam_sequence' => $studentData['exam_sequence'],
                    'grade_class' => $studentData['grade_class'],
                    'teacher_name' => $studentData['teacher_name']
                ];

                $studentArray = $this->checkExaminee($examId, $studentArray, $userData, $studentModel, $key + 2);
                $studentArray['exam_id'] = $examId;
                $studentArray['user_id'] = $userData->id;
                $studentArray['create_user_id'] = \Auth::id();

                //拼装一个二维数组
                $studentArrays[] = $studentArray;

                //成功的考生数加1
                $sucNum++;
            } //循环over

            //将拼装好的$studentArrays一次性插入student表
            if (count($studentArrays) != 0) {
                if (!$studentModel->insert($studentArrays)) {
                    throw new \Exception('保存学生时出错！');
                }
            }

            $message = "成功导入{$sucNum}个学生";
            if ($exiNum) {
                $message .= "，有{$exiNum}个学生已存在";
            }
            //返回信息数组不为空
            if (!empty($backArr)) {
                $mes1 = '';
                foreach ($backArr as $item) {
                    if ($item['title'] == 'name') {
                        $mes1 .= $item['key'] . '、';
                    }
                }
                if ($mes1 != '') {
                    $message .= '，第' . rtrim($mes1, '、') . '行姓名不能为空！';
                }
            }
            if ($exiNum || isset($mes1)) {
                if (isset($mes1)) {
                    throw new \Exception(trim($message, '，'));
                }
                throw new \Exception(trim($message, '，'));
            }

            //更新考试的人数
            $exam = Exam::doingExam($examId);
            $exam->total = $sucNum;
            if (!$exam->save()) {
                throw new \Exception('保存考试人数失败！');
            }
            $connection->commit();
            return $sucNum;     //返回导入成功的个数
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该手机号码已经使用，请输入新的手机号');
            }
            $connection->rollBack();
            throw $ex;
        }

    }
}