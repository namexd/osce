<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/4/14
 * Time: 10:39
 */

namespace Modules\Osce\Entities\ConfigRepository;


class SysParam
{
    /**
     * 返回sysParam需要的数据
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-04-13 18:20
     */
    public function getData()
    {
        $data = config('osce.sys_param');
        return $data;
    }

    /**
     * 重写文件的方法
     * @param array $data
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-04-13 20:30
     */
    public function setData(array $data)
    {
        try {
            $str = view('osce::admin.systemManage.sys_param_config', $data)->render();
            $str = '<?php ' . $str;

            if (!is_writable(SYS_PARAM)) {
                throw new \Exception('config/sys_param.php文件不可写');
            }
            file_put_contents(SYS_PARAM, $str);

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}