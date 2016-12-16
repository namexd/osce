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
use Modules\Osce\Repositories\Common as OsceCommon;

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

    /**
     * 导入考生的方法
     * @param $examId
     * @param $studentModel
     * @return int
     * @throws \Exception
     * @author ZouYuChao
     * @time 2016-06-28 11:17
     */
    public function importStudent($exam, $studentModel)
    {
        $parentStudentNum = 0;
        $studentArrays = [];

        $sucNum = 0;            //导入成功的学生数
        $exiNum = 0;            //已经存在的学生数
        $examId = $exam->id;    //获取考试ID
//        $connection = \DB::connection('osce_mis');
//        $connection->beginTransaction();

        try {
            $data = $this->model->getData();
            //查询是否有父考试，并获取父考试中所有的学生用户ID
            $userss = $this->getParentExamStudent($exam);
            
            foreach ($data as $key => $studentData)
            {
                //1、数据验证
                //姓名不能为空，为空的跳过
                if (empty(trim($studentData['name']))) {
                    if (!empty($studentData['idcard']) && !empty($studentData['mobile'])) {
                        throw new \Exception('第' .($key+2). '行的姓名不能为空！');
                    }
                    continue;
                }
                //性别处理
                $studentData['gender'] = $this->model->handleSex($studentData['gender']);
                //证件类型处理（将中文转换为对应的数字）
                $studentData['idcard_type'] = $this->model->handleIdcardType($studentData['idcard_type'], $key+2);
                //去掉证件号码中的空格
                $studentData['idcard'] = str_replace(' ', '', $studentData['idcard']);

                //查询用户是否已经参加考试
                $user = User::where('username', $studentData['mobile'])->select('id')->first();
                if ($user) {
                    $student = $studentModel->where('user_id', $user->id)->where('exam_id', $examId)->first();
                    //判断该学生用户是否在父考试的考生当中
                    if(!empty($userss) && in_array($user->id, $userss))
                    {
                        $parentStudentNum++;
                    }

                } else {
                    $student = null;
                }
                //考生存在就跳过
                if (!is_null($student)) {
                    $exiNum++;
                    continue;
                }

                //验证(身份证、手机号、准考证号)
                $this->model->check($examId, $studentData, $key + 2);

                //用户数据
                $userData = [
                    'name'          => $studentData['name'],
                    'gender'        => $studentData['gender'],
                    'idcard_type'   => intval($studentData['idcard_type']),
                    'idcard'        => trim($studentData['idcard']),
                    'mobile'        => trim($studentData['mobile']),
                    'code'          => trim($studentData['code']),
                    'avatar'        => $studentData['avator'],
                    'email'         => $studentData['email']
                ];
                //处理用户数据 TODO：fandian 2016-06-03 18:06
                $role_id  = config('osce.studentRoleId');
                $userData = OsceCommon::handleUser($userData, $role_id);

                //考生数据
                $studentArray = [
                    'name'          => $studentData['name'],
                    'idcard'        => trim($studentData['idcard']),
                    'mobile'        => trim($studentData['mobile']),
                    'code'          => trim($studentData['code']),
                    'avator'        => $studentData['avator'],
                    'description'   => $studentData['description'],
                    'exam_sequence' => $studentData['exam_sequence'],
                    'grade_class'   => $studentData['grade_class'],
                    'teacher_name'  => $studentData['teacher_name']
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

            //更新考试的人数
            $exam = Exam::doingExam($examId);
            $exam->total = $sucNum + $exam->total;
            if (!$exam->save()) {
                throw new \Exception('保存考试人数失败！');
            }

            //将拼装好的$studentArrays一次性插入student表
            if (count($studentArrays) != 0) {
                if (!$studentModel->insert($studentArrays)) {
                    throw new \Exception('保存学生时出错！');
                }
            }

            //返回信息
            $message = "成功导入{$sucNum}个学生";
            if ($exiNum) {
                $message .= "，有{$exiNum}个学生已存在";
            }
            if($parentStudentNum){
                $message .= "，有{$parentStudentNum}个考生与父考试重复";
            }
            if ($exiNum || $parentStudentNum) {
                throw new \Exception(trim($message, '，'));
            }
            unset($userData);
//            $connection->commit();
            return $sucNum;     //返回导入成功的个数

        } catch (\Exception $ex)
        {
            if ($ex->getCode() == 23000) {

                throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该手机号码已经使用，请输入新的手机号');
            }
            throw $ex;
        }
    }

    /**
     * 获取父类考试中的所有学生 用户ID
     * @param $exam
     * @param array $user_ids
     * @return array
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-15 14:55
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getParentExamStudent($exam, $user_ids = [])
    {
        //判断是否为顶级考试（无父考试）
        if($exam->pid != 0){
            $parentExam = Exam::doingExam($exam->pid);
            if(is_null($parentExam)){
                throw new \Exception('没找到对应的父考试');
            }
            //获取对应考试中的所有学生用户ID
            $studentUsers = Student::where('exam_id', '=', $parentExam->id)
                                   ->select(['user_id'])->get()
                                   ->pluck('user_id')->toArray();
            $user_ids = array_merge($user_ids, $studentUsers);  //合并
            $user_ids = array_unique($user_ids);                //去重
            //查询是否还有父考试
            $this->getParentExamStudent($parentExam, $user_ids);
        }
        //返回所有的学生用户ID
        return $user_ids;
    }
}