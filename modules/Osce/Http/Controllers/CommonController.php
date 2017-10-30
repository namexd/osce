<?php
/**
 * osce 公共控制器
 * author   Zouyuchao<Zouyuchao@sulida.com>
 * date 2015-12-28  15:51
 */
namespace Modules\Osce\Http\Controllers;

use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\StationVideo;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\DB;
class CommonController extends Controller
{
    /**
     * 文件上传
     *
     * @return string url
     */
    public function uploadFile($inputName = 'Filedata' ,$fileSize = 5 ,$filePath='uploads/temp/' )
    {
        $file = Input::file($inputName);
        try {
            if ($file->isValid()) {
                //取得上传文件的大小：
                $size = $file->getSize();
                if ($size > 1024 * 1024 * $fileSize) {
                    $info['code'] = 2;
                    $info['message'] = '图片太大，上传失败';
                    return $info;
                }
                //取得上传文件的MIME类型：//$mime = $file->getMimeType();
                $mime = $file->getClientMimeType();
                //if (($mime == "image/gif") || ($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/png")) {
                if ( in_array($mime ,['image/gif','image/jpeg','image/pjpeg','image/png','text/plain'])|| strpos($mime,'application/vnd.')===0 || strpos($mime,'application/msword')===0) {
                    $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
                    $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
                    $path = $file->move($filePath, $newName);
                    //$filepath = getcwd().'/'.$filePath  . $newName;
                    $filepath = '/'.$filePath  . $newName;
                    $info['code'] = 1;
                    $info['message'] = '上传成功';
                    $info['filepath'] = $filepath;
                    //$info['filepath'] = Storage::put($filepath);
                    //unlink($filepath);
                    return $info;
                }
                $info['code'] = 3;
                $info['message'] = '文件类型不支持';
                return $info;
            }
        } catch (\Exception $ex) {
            $info['code'] = intval(0);
            $info['message'] = $ex->getMessage();
            return $info;
        }
    }
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
         
        if ('Trying to get property of non-object' == $ex->getMessage()) {
            return [
                'code' => -50000,
                'message' => '当前系统错误，请重试！',
            ];
        } else {
           
            return [
                'code' => $code,
                'message' => $ex->getMessage(),
            ];
        }
    }

    /**
     * 将错误代码和错误信息写进日志
     * @param $ex
     * @author ZouYuChao
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
     * @author ZouYuChao
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
     * 上传锚点的保存
     * @param $stationId 考站id
     * @param $studentId 学生id
     * @param $examId 考试id
     * @param $teacherId 老师id
     * @param array $timeAnchors 锚点数据
     * @return array
     * @throws \Exception
     * @author ZouYuChao
     * @time 2016-03-14 10:50
     */
    static public function storeAnchor($stationId, $studentId, $examId, $teacherId, array $timeAnchors)
    {
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
		try {
            //获得站点摄像机关联表
            $stationVcr = StationVcr::where('station_id', $stationId)->first();
            if (!is_null($stationVcr)) {
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
            }
            $connection->commit();
            return ['锚点上传成功！'];
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }


    /**
     *  判断考试状态
     * @method GET
     * @url
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string
     * @return json
     * @version
     * @author weihuiguo <weihuiguo@sulida.com>
     * @date
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function checkType($status){
        if(!is_null($status)){
            if($status < 2){
                $name = '等待中';
            }elseif($status == 2){
                $name = '考试中';
            }else{
                $name = '已结束';
            }
        }else{
            $name = '没有考试';
        }


        return $name;
    }

    /**
     * 获取当前控制器与方法
     *
     * @return array
     */
    public function getCurrentAction()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);

        return ['controller' => $class, 'method' => $method];
    }

    /**
     * 获取当前控制器名
     *
     * @return string
     */
    public function getCurrentControllerName()
    {
        return $this->getCurrentAction()['controller'];
    }

    /**
     * 获取当前方法名
     *
     * @return string
     */
    public function getCurrentMethodName()
    {
        return $this->getCurrentAction()['method'];
    }

    /*返回信息接口*/
    public function rmsg($code=1, $msg=''){
        $data = [
            'status' => $code,
            'msg' => $msg,
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        return $data;
    }
}