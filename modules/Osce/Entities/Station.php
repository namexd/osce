<?php
/**
 * 考站
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 10:31
 */

namespace Modules\Osce\Entities;

use DB;

class Station extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'mins', 'room_id', 'type', 'subject_id'];

    /**
     * 定义访问器 字段名：type
     * @param $value
     * @return string
     */
//    public function getTypeAttribute($value)
//    {
//        switch ($value) {
//            case 1 :
//                $value = '操作';
//                break;
//            case 2 :
//                $value = 'SP';
//                break;
//            case 3 :
//                $value = '理论';
//                break;
//            default :
//                $value = '此数据不合法';
//        }
//        return $value;
//    }

    /**
     * 与摄像机_考站表的关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vcrStation()
    {
        return $this->hasMany('Modules\Osce\Entities\StationVcr', 'station_id', 'id');
    }

    /**
     * 获得station列表
     * @param array $order
     * @return mixed
     * @throws \Exception
     */
    public function showList($order = ['created_at', 'desc'])
    {
        try {
            //获得排序
            list($orderType, $orderBy) = $order;

            //开始查询
            $builder = $this->select([
                'id',
                'name',
                'type',
                'description'
            ])->orderBy($orderType, $orderBy);

            return $builder->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 将数据插入各表,创建考室
     * @param $formData 0为$placeData,1为$vcrId,2为$caseId
     * @return bool
     * @throws \Exceptio
     * @throws \Exception
     */
    public function addStation($formData)
    {


            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
            list($stationData, $vcrId, $caseId) = $formData;

            //将station表的数据插入station表
            $result = $this->create($stationData);

            //获得插入后的id
            $station_id = $result->id;
            if ($result === false) {
                $connection->rollBack();

                return false;
            }
            //将考场摄像头的关联数据写入关联表中
            $stationVcrData = [
                 'vcr_id'=>$vcrId,
                'station_id' => $station_id
            ];
            $result = StationVcr::create($stationVcrData);
            if ($result === false) {
                $connection->rollBack();
                return false;
            }

            //更改摄像机表中摄像机的状态
            $vcr = Vcr::findOrFail($vcrId);
            $vcr->status = 0;  //变更状态,但是不一定是0
            $result = $vcr->save();
            if ($result === false) {
                $connection->rollBack();
                return false;
            }

            //改变考站病历表的状态
            $stationCaseData = [
                'case_id'=>$caseId,
                'station_id' => $station_id
            ];
            $result = StationCase::create($stationCaseData);
            if ($result === false) {
                $connection->rollBack();
                return false;
            }

            $connection->commit();
            return true;

    }

    /**
     * 为编辑着陆页准备的回显数据
     * @param $id
     * @return mixed
     */
    public function rollMsg($id)
    {
        $builder = $this->leftJoin(
            'station_vcr',
            function ($join) use ($id){
                $join->on('station_vcr.station_id', '=', $this->table . '.id')
                     ->where('station_vcr.station_id', '=' ,$id);
            }
        )->leftJoin(
            'station_case',
            function ($join) use ($id){
                $join->on('station_case.station_id', '=',$this->table . '.id')
                    ->where('station_case.station_id', '=' ,$id);
            }
        );

        $builder->select([
            $this->table . '.id as id',
            $this->table . '.name as name',
            $this->table . '.type as type',
            $this->table . '.mins as mins',
            $this->table . '.room_id as room_id',
            $this->table . '.create_user_id as create_user_id',
            $this->table . '.subject_id as subject_id',
            $this->table . '.description as description',
            'station_vcr.vcr_id as vcr_id',
            'station_case.case_id as case_id'
        ]);

        return $builder->first();
    }

    /**
     * 更新的方法
     * @param $formData
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function updateStation($formData, $id)
    {
        try {
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            list($stationData, $vcrId, $caseId) = $formData;
            //将原来的摄像机的状态回位
            //通过传入的考站的id找到原来的摄像机
            $originalVcrObj = StationVcr::where('station_id', '=', $id)->select('vcr_id')->first();
            if (empty($originalVcrObj)) {
                throw new \Exception('没有找到原来设定的摄像机');
            }

            $originalVcrId = $originalVcrObj->vcr_id;

            $result = Vcr::findOrFail($originalVcrId);
            //修改其状态
            $result->status = 1; //可能是1，也可能是其他值
            //保存
            $result = $result->save();
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改摄像机状态失败');
            }

            //修改station表
            $result = $this->where($this->table . '.id', '=', $id)->update($stationData);
            //获得修改后的id
            $station_id = $result;
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改考站失败');
            }

            //删除与本考站相关的考站_摄像头关联
            $result = StationVcr::where('station_id', '=', $id)->delete();
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('删除考站摄像头关联失败');
            }

            //将考场摄像头的关联数据重新写进关联表中
            $stationVcrData = [
                'vcr_id' => $vcrId,
                'station_id' => $station_id
            ];
            $result = StationVcr::create($stationVcrData);
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改摄像头失败');
            }

            //更改摄像机表中摄像机的状态
            $vcr = Vcr::findOrFail($vcrId);  //找到选择的摄像机
            $vcr->status = 0;  //变更状态,但是不一定是0
            $result = $vcr->save();
            if (!$result) {
                $connection->rollBack();
                throw new \Exception('更改摄像机状态失败');
            }

            //改变考站病历表的状态
            $stationCaseData = [
                'case_id'=>$caseId,
                'station_id' => $station_id
            ];
            $result = StationCase::create($stationCaseData);
            if ($result === false) {
                $connection->rollBack();
                throw new \Exception('更改病例关联失败');
            }
            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function deleteData($id)
    {
        try {
            //判断在关联表中是否有数据
            $result1 = StationCase::where('station_case.station_id', '=', $id)->select('id')->first();
            $result2 = StationVcr::where('station_vcr.station_id', '=', $id)->select('id')->first();
            if ($result1 && $result2) {
                throw new \Exception('不能删除此考站，因为与其他条目相关联');
            }
            return $this->where($this->table.'.id', '=', $id)->delete();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}