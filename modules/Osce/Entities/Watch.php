<?php
/**
 * 腕表模型
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 16:01
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Entities\MachineInterface;
use DB;

class Watch extends CommonModel implements MachineInterface
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'watch';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'name', 'code','factory','sp','description','status','create_user_id'];
    public      $search    =   [];

    protected $statuValues  =   [
        0   =>  '未使用',
        1   =>  '使用中',
        2   =>  '维修',
        3   =>  '报废',
    ];

    /**
     * 获取设备状态值
     * @access public
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 16:33
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
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
//            $machineData    =   [];
            if($vcr =   $this   ->  create($data))
            {
                $machineData=   [
                    'item_id'    =>  $vcr    ->  id,
                    'type'      =>  1,
                ];
            }
            else
            {
                throw new \Exception('新增腕表失败');
            }
            if(empty($machineData))
            {
                throw new \Exception('没有找到腕表新增数据');
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
                throw new   \Exception('新增腕表资源失败');
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
                throw new \Exception('没有找到该腕表');
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
//                    throw new \Exception('保存腕表资源信息失败');
//                }

            return $vcr;
//            }
//            else
//            {
//                throw new   \Exception('没有找到腕表资源信息');
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
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList($name='',$status=''){
        $builder =   Watch::select();

        if($name != '')
        {
            $builder =   $builder    ->  where('name', 'like', '%'.$name.'%');
        }
        if($status || ($status==0&&$status!=''))
        {
            $builder =   $builder    ->  where('status', '=', $status);
        }
        $builder = $builder -> select(['id', 'name', 'status']);

        return  $builder ->  paginate(config('osce.page_size'));
    }


}