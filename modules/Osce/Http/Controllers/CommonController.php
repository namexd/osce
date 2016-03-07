<?php
/**
 * osce 公共控制器
 * author   Luohaihua<luohaihua@misrobot.com>
 * date 2015-12-28  15:51
 */
namespace Modules\Osce\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\DB;

abstract class CommonController extends Controller
{

    /**
     * 返回成功的json数据
     *
     * @return string
     *
     * [
     *    'code'            =>    1,
     *    'message'        =>    'success',
     *    'data'            =>    ''
     * ];
     *
     */
    public function success_data($data = [], $code = 1, $message = 'success')
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 返回多行成功后的json数据
     *
     * @return string
     * [
     *        'code'            =>    1,
     *        'message'        =>    'success',
     *        'data'            =>    [
     *        'total'        =>    10,
     *        'pagesize'    =>    10,
     *        'pageindex'    =>    1,
     *        'rows'        =>    []
     *        ]
     * ];
     */
    public function success_rows(
        $code = 1,
        $message = 'success',
        $total = 0,
        $pagesize = 10,
        $pageindex = 0,
        $rows = []
    ) {

        return [
            'code' => $code,
            'message' => $message,
            'data' => [
                'total' => $total,
                'pagesize' => $pagesize,
                'page' => $pageindex,
                'rows' => $rows
            ]
        ];
    }

    /**
     * 返回失败的json数据
     *
     * @param \Exception $ex
     * @return string 'code'            =>    -999,
     *
     * 'code'            =>     -999,
     * 'message'        =>      'fail'
     * ];
     */
    public function fail(\Exception $ex)
    {
        if ($ex->getCode() == 0) {
            $code = -999;
        } else {
            $code = $ex->getCode();
        }
        return [
            'code' => $code,
            'message' => '错误信息:' . $ex->getMessage(),
//            'errorLine' => '错误行数:' . $ex->getLine(),
//            'errorFile' => '错误文件:' . $ex->getFile()
        ];
    }

    /**
     * 将错误代码和错误信息写进日志
     * @param $ex
     * @author Jiangzhiheng
     * @time
     */
    protected function errorLog(\Exception $ex)
    {
        \Log::info('Error', [
            'ErrorCode:' . $ex->getCode(),
            'ErrorMessage:' . $ex->getMessage()
        ]);
    }

    /**
     * 正则验证
     * @param $value
     * @param $rule
     * @return bool
     * @author Jiangzhiheng
     * @time 2016-03-07 09:41
     */
    public static function regex_vali($value, $rule)
    {
        $validate = [
            'require' => '/\S+/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url' => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/^\d+$/',
            'zip' => '/^\d{6}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
            'alpha&no' => '/^[A-Za-z0-9]+$/',
            'positive_integer' => '/^[+]?[1-9]+$/', //正整数
            'date' => '/^\d{4}(\-|\/|.)\d{1,2}\1\d{1,2}$/',
            'datetime' => '/^\d{4}(\-|\/|.)\d{1,2}\1\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$/'
        ];
        // 检查是否有内置的正则表达式
        if (isset($validate[strtolower($rule)])) {
            $rule = $validate[strtolower($rule)];
        }
        return preg_match($rule, $value) === 1;
    }

}