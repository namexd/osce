<?php
/**
 * PAD模型
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 16:01
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Entities\MachineInterface;
use DB;
class Pad extends CommonModel implements MachineInterface
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'pad';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	['code', 'name', 'status', 'create_user_id', 'factory', 'sp', 'purchase_dt'];
    public      $search    =   [];

    protected $statuValues  =   [
        1   =>  '使用中',
        0   =>  '未使用',
        2   =>  '报废',
        3   =>  '维修',
    ];

    public function getMachineStatuValues(){
        return $this->statuValues;
    }

    /**
     * 新增设备
     * @access public
     *
     * * @param $data
     * * string        name             设备名称(必须的)
     * * string        code             设备编号(必须的)
     * * string        status           设备状态(必须的)
     * * string        create_user_id   创建人(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addMachine($data){
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
                throw new \Exception('新增PAD失败');
            }
            if(empty($machineData))
            {
                throw new \Exception('没有找到PAD新增数据');
            }

            $machine    =   true;
            if($machine)
            {
                $connection -> commit();
                return $vcr;
            }
            else
            {
                throw new   \Exception('新增PAD资源失败');
            }
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            throw $ex;
        }
    }
    /**
     * 编辑设备
     * @access public
     *
     * * @param $data
     * * int            id              设备ID(必须的)
     * * string        name             设备名称(必须的)
     * * string        code             设备编号(必须的)
     * * string        status           设备状态(必须的)
     * * string        create_user_id   创建人(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editMachine($data){
        $connection =   DB::connection($this->connection);
        $connection ->beginTransaction();
        try
        {
            $vcr            =   $this   ->  find($data['id']);
            if($vcr)
            {
                foreach($data as $feild=> $value)
                {
                    if($feild=='id')
                    {
                        continue;
                    }
                    $vcr    ->  $feild  =   $value;
                }
                if($vcr     ->  save())
                {
//                    $machine    =   Machine ::where('item_id','=',$vcr    ->  id)
//                                            ->where('type','=',1)
//                                            ->first();
                }
                else
                {
                    $machine    =   false;
                }
            }
            else
            {
                throw new \Exception('没有找到该PAD');
            }

//            if($machine)
//            {
//                $machine    ->  name    =   $data['name'];
//                if($machine->save())
//                {
            $connection -> commit();
//                }
//                else
//                {
//                    throw new \Exception('保存PAD资源信息失败');
//                }

            return $vcr;
//            }
//            else
//            {
//                throw new   \Exception('没有找到PAD资源信息');
//            }
        }
        catch(\Exception $ex)
        {
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
     * @author Luohaihua <Luohaihua@misrobot.com> Zhoufuxiang 2016-01-13 15:21:48
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList($name, $status,$nfc_code=''){
        $bulder =   $this;
        if($name != ''){
            $bulder =   $bulder    ->  where('name', 'like', '%\\'.$name.'%');
        }

        if($status != '')
        {
            $bulder =   $bulder    ->  where('status', '=', $status);
        }
        $bulder = $bulder -> select(['id', 'code', 'name', 'status']);

        return  $bulder ->  paginate(config('osce.page_size'));
    }
}