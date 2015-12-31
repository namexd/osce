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
    protected $connection	=	'osce_mis';
    protected $table 		= 	'vcr';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	    =	['id', 'name', 'code','ip','username','password','port','channel','description'];
    public      $search         =   [];

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
        $connection =   DB::connection($this->connection);
        $connection ->beginTransaction();
        try
        {
            $machineData    =   [];

            if($vcr =   $this   ->  create($data))
            {
                $machineData=   [
                    'item_id'    =>  $vcr    ->  id,
                    'type'      =>  1,
                ];
            }
            else
            {
                throw new \Exception('新增摄像机失败');
            }
            if(empty($machineData))
            {
                throw new \Exception('没有找到摄像机新增数据');
            }
            //$machine    =   Machine::create($machineData);
            $machine    =   true;
            if($machine)
            {
                $connection -> commit();
                return $vcr;
            }
            else
            {
                throw new   \Exception('新增摄像机资源失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            throw $ex;
        }
    }


}