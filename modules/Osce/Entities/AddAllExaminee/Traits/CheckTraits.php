<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/21
 * Time: 10:34
 */

namespace Modules\Osce\Entities\AddAllExaminee\Traits;


use Modules\Osce\Entities\Student;

trait CheckTraits
{
    public function regularCheck($examId, $data, $type = 'idcard', $key = 0)
    {
        switch ($type) {
            case 'idcard':  $this->checkIdCard($examId, $data, $key);
                break;
            case 'mobile':
                if (!preg_match('/^1[3|5|7|8]{1}[0-9]{9}$/', $data)) {
                    throw new \Exception('第' . ($key) . '行手机号不符规格，请修改后重试！');
                }
                break;
            case 'exam_sequence':
                if (empty(trim($data))) {
                    throw new \Exception('第' . ($key) . '行准考证号不能为空，请修改后重试！');
                }
                break;
            default:
                throw new \Exception('系统错误，请联系管理员');
                break;
        }
        return true;
    }

    /**
     * 验证考生数据的正确性
     * @param array $data
     * @author Jiangzhiheng
     * @time 2016-04-21 11：29
     */
    public function checkExaminee($examId, array $data, $user, $studentModel, $key = 0)
    {
        $code = $studentModel->where('code', $data['code'])->where('user_id', '<>', $user->id)->first();
        if (!is_null($code)) {
            throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该学号已经有别人使用！');
        }

        $student = $studentModel->where('user_id', $user->id)->where('exam_id', $examId)->first();
        if ($student) {
            throw new \Exception((empty($key) ? '' : ('第' . $key . '行')) . '该考生已经存在，不能再次添加！');
        }

        return $data;
    }

    /**
     * 身份证号验证
     * @param $examId
     * @param $idCard
     * @param $key
     * @return bool
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-5-09 16:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function checkIdCard($examId, $idCard, $key)
    {
        //1、验证身份证的正确性
        if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $idCard)) {
            throw new \Exception('第' . ($key) . '行身份证号不符规格，请修改后重试！');
        }

        //2、查询同一场考试中，身份证号是否已经存在
        $result = Student::where('exam_id', '=', $examId)->where('idcard','=', $idCard)->first();
        if(!is_null($result)){
            throw new \Exception('第' . ($key) . '行身份证号已经存在，请修改后重试！');
        }
        return true;
    }
}