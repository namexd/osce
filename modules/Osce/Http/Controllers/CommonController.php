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

    /**
     * 判断对象集合是否为空
     * 如果为空，报错
     * @param object $obj
     * @param string $message
     * @param int $code
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-10 15:01
     */
    static public function objIsEmpty($obj, $message = '系统错误', $code = -999)
    {
        if (is_object($obj)) {
            if (!$obj->isEmpty()) {
                return true;
            } else {
                throw new \Exception($message, $code);
            }
        } else {
            throw new \Exception('系统错误，请重试');
        }
    }

    /**
     * 判断对象是否为空
     * 如果为空，报错
     * @param object $obj
     * @param string $message
     * @param int $code
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-10 15:19
     */
    static public function objIsNull($obj, $message = '系统错误', $code = -999)
    {
        if (is_object($obj)) {
            if (!is_null($obj)) {
                return true;
            } else {
                throw new \Exception($message, $code);
            }
        } else {
            throw new \Exception('系统错误，请重试');
        }
    }



    static public function storeAnchor($stationId, $studentId, $examId, $teacherId, array $timeAnchors)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        try {
            //获得站点摄像机关联表
            $stationVcr = StationVcr::where('station_id', $stationId)->first();
            if (is_null($stationVcr)) {
                throw new \Exception('该考站未关联摄像机', -200);
            }

            foreach ($timeAnchors as $timeAnchor) {
                //拼凑数组
                $data = [
                    'station_vcr_id' => $stationVcr->id,
                    'begin_dt' => date('Y-m-d H:i:s', $timeAnchor),
                    'end_dt' => date('Y-m-d H:i:s', $timeAnchor),
                    'created_user_id' => $teacherId,
                    'exam_id' => $examId,
                    'student_id' => $studentId,
                ];

                //将数据插入库
                if (!$result = StationVideo::create($data)) {
                    throw new \Exception('保存失败！请重试', -210);
                }
            }

            $connection->commit();
            return ['锚点上传成功！'];
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }




}