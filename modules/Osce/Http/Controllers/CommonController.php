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
        if($ex->getCode() == 0) {
            $code   =   -999;
        } else {
            $code   =   $ex -> getCode();
        }
        return [
            'code' => $code,
            'message' => '错误信息:' . $ex->getMessage(),
            'errorLine' => '错误行数:' . $ex->getLine(),
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
       \Log::info('Error',[
           'ErrorCode:' . $ex->getCode(),
           'ErrorMessage:' . $ex->getMessage()
       ]) ;
    }
}