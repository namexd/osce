<?php
/**
 * 设备摄像机模型
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/30
 * Time: 14:11
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Entities\MachineInterface;
use DB;

class Vcr extends CommonModel implements MachineInterface
{
    protected $connection   =	'osce_mis';
    protected $table 		= 	'vcr';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	['id', 'name', 'code','ip','username','password','port','realport','channel','description','status','created_user_id','sp','factory','purchase_dt','used'];
    public      $search     =   [];

    protected $statuValues  =   [
        1   =>  '在线',
        0   =>  '离线',
        2   =>  '报废',
        3   =>  '维修',
    ];

    /**
     *  获取设备状态值
     * @access public
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 15:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getMachineStatuValues(){
        return $this    ->  statuValues;
    }

    /**
     * 新增摄像机
     * @access public
     *
     * @param
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        status       摄像机状态(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-31 17:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addMachine($data)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try
        {
            if($vcr = $this -> create($data))
            {
                $connection -> commit();
                return $vcr;
            } else {
                throw new \Exception('新增摄像机失败');
            }

        } catch(\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 编辑摄像头
     * @access public
     *
     * * @param $data
     * * string        id           摄像机ID(必须的)
     * * string        name         摄像机名称(必须的)
     * * string        code         摄像机编码(必须的)
     * * string        ip           摄像机IP(必须的)
     * * string        username     摄像机用户名(必须的)
     * * string        password     摄像机密码(必须的)
     * * string        port         摄像机端口(必须的)
     * * string        status       摄像机状态(必须的)
     * * string        channel      摄像机频道(必须的)
     * * string        description  摄像机描述(必须的)
     *
     * @return view
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editMachine($data)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $vcr = $this    -> find($data['id']);

            if($vcr) {
                foreach($data as $feild=> $value) {
                    if($feild=='id') {
                        continue;
                    }
                    $vcr    ->  $feild  =   $value;
                }
                $connection->enableQueryLog();
                $result = $vcr -> save();
                $a = $connection->getQueryLog();
                if(!$result) {
                    throw new \Exception('修改失败，请重试！');
                }
            } else {
                throw new \Exception('没有找到该摄像机');
            }
            $connection -> commit();
            return $vcr;

        } catch(\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 根据设备名称获取 设备列表
     * @access public
     *
     * * @param string $name 设备名称
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList($name,$status,$nfc_code=''){
        $bulder =   $this;
        if($name != ''){
            $bulder =   $bulder    ->  where('name', 'like', '%\\'.$name.'%');
        }
        if($status !='')
        {
            $bulder =   $bulder    ->  where('status', '=', $status);
        }
        $bulder = $bulder -> select(['id', 'code', 'name', 'status']);

        return  $bulder ->  paginate(config('osce.page_size'));
    }


    /**
     * 查询没被其他考场关联的摄像机
     * @api GET /osce/wechat/resources-manager/selectVcr
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        id        参数中文名(必须的)
     * * string        type      参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function selectVcr($id, $type)
    {
        if ($type === '0') {
            $modelVcr = RoomVcr::where('room_id', $id)->first();
        } else {
            $modelVcr = AreaVcr::where('area_id', $id)->first();
        }

        $vcr = Vcr::whereNotIn('status',[2,3])
            ->where('used',0)
            ->orWhere('id', $modelVcr->vcr_id)
            ->select(['id', 'name'])->get();

        $result = [$vcr, $modelVcr];
        return $result;     //关联摄像机
    }



}