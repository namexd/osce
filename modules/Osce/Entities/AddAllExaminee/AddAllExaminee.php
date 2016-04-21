<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/20
 * Time: 17:15
 */

namespace Modules\Osce\Entities\AddAllExaminee;


use App\Repositories\Common;
use Illuminate\Http\Request;
use Modules\Osce\Entities\AddAllExaminee\Traits\CheckTraits;

class AddAllExaminee
{
    use CheckTraits;
    private $data;

    /**
     * 设置数据
     * @param Request $request
     * @param $fileName
     * @return mixed
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-20 18:10
     */
    public function setData(array $data)
    {
        return $this->data = $data;
    }

    /**
     * 获取data数据
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-21 14:29
     */
    public function getData()
    {
        $this->wipeOffSheet($this->data);
        $this->judgeTemplate($this->data);
        $this->fieldsChTOEn($this->data);
        return $this->data;
    }

    /**
     * 去掉sheet
     * @param array $data
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-20 18：11
     */
    public function wipeOffSheet(array $data)
    {
        return $this->data = array_shift($data);
    }

    /**
     * 判断模板数据
     * @param array $data
     * @author Jiangzhiheng
     * @time 2016-04-20 18：13
     */
    public function judgeTemplate(array $data)
    {
        $enFields = config('osce.importForCnToEn.student');
        $cnFields = array_flip($enFields);
//      $cnFields = ['姓名','性别','学号','身份证号','联系电话','电子邮箱','头像','备注','准考证号','班级','班主任姓名'];
        foreach ($data as $item) {
            $key = 0;
            if (count($item) != count($cnFields)) {
                throw new \Exception('模板列数有误');
            }
            foreach ($item as $k => $value) {
                $key++;
                if (!in_array($k, $cnFields)) {
                    throw new \Exception('第' . $key . '列模板表头有误');
                }
            }
        }

        return $this->data = $data;
    }

    /**
     * 将中文字段转换为英文
     * @param array $data
     * @param string $nameToEn
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-20 18:32
     */
    public function fieldsChTOEn(array $data, $nameToEn = 'osce.importForCnToEn.student')
    {
        return $this->data = Common::arrayChTOEn($data, $nameToEn);
    }

    /**
     * 处理用户的性别
     * @param $sex
     * @return int
     * @author Jiangzhiheng
     * @time 2016-04-21 10:30
     */
    public function handleSex($sex)
    {
        switch ($sex) {
            case '男':

                return $sex = 1;
            case '女':

                return $sex = 2;
            default:
                return $sex = 0;
        }
    }

    public function check(array $data, $key = 0)
    {
        $this->regularCheck($data['idcard'], 'id_cord' ,$key); //身份证验证
        $this->regularCheck($data['mobile'], 'mobile', $key); //手机号
        $this->regularCheck($data['exam_sequence'], 'exam_sequence', $key); //准考证号
    }

    

}