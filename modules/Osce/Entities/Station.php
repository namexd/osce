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
    protected $fillable = ['name', 'code', 'room_id', 'type', 'description', 'create_user_id'];

    /**
     * 定义访问器 字段名：type
     * @param $value
     * @return string
     */
    public function getTypeAttribute($value)
    {
        switch ($value) {
            case 1 :
                $value = '操作';
                break;
            case 2 :
                $value = 'SP';
                break;
            case 3 :
                $value = '理论';
                break;
            default :
                $value = '此数据不合法';
        }
        return $value;
    }

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
        try {
            $resultArray = [];
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            //将place表的数据插入place表
            $placeData = $formData[0];
            $result = $this->create($placeData);
            //获得插入后的id
            $station_id = $result->id;
            array_push($resultArray, $result);


            //将考场摄像头的关联数据写入关联表中
            $placeVcrData = [
                $formData[1],
                'station_id' => $station_id
            ];
            $result = StationVcr::create($placeVcrData);
            array_push($resultArray, $result);

            //更改摄像机表中摄像机的状态
            $vcr_id = $placeVcrData['vcr_id'];
            $vcr = Vcr::findOrFail($vcr_id);
            $vcr->status = 0;  //变更状态,但是不一定是0
            $result = $vcr->save();
            array_push($resultArray, $result);

            //判断$resultArray中是否有键值为false,如果有，那就说明前面有错误
            if (array_search(false, $resultArray) !== false) {
                $connection->rollBack();
                throw new \Exceptio('新建房间时发生了错误,请重试!');
            } else {
                $connection->commit();
                return true;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 为编辑着陆页准备的回显数据
     * @param $id
     * @return mixed
     */
    public function rollMsg($id)
    {
        $builder = $this->leftJoin(
            'place_vcr',
            function ($join) use ($id) {
                $join->on('place_vcr.station_id', '=', $id);
            }
        );

        $builder->select([
            $this->table . '.id as id',
            $this->table . '.name as name',
            $this->table . '.type as type',
            $this->table . '.time as time',
            $this->table . '.room_id as room_id',
            $this->table . '.create_user_id as create_user_id',
            $this->table . '.subject_id as subject_id',
            $this->table . '.description as description',
            'place_vcr.vcr_id as vcr_id',
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
            $resultArray = [];
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            //将原来的摄像机的状态回位
            //通过传入的考站的id找到原来的摄像机
            $originalVcrObj = StationVcr::where('station_id', '=', $id)->select('vcr_id')->first();

            if (empty($originalVcrObj)) {
                throw new \Exception('没有找到原来设定的摄像机');
            }

            $originalVcrId = $originalVcrObj->id;
            $result = Vcr::findOrFail($originalVcrId);
            //修改其状态
            $result->status = 1; //可能是1，也可能是其他值
            //保存
            $result = $result->save();
            array_push($resultArray, $result);

            //修改station表
            $placeData = $formData[0];
            $result = $this->where($this->table . '.id', '=', $id)->update($placeData);
            //获得修改后的id
            $station_id = $result->id;
            array_push($resultArray, $result);

            //删除与本考站相关的考站_摄像头关联
            $result = StationVcr::where('station_id', '=', $id)->delete();
            array_push($resultArray, $result);

            //将考场摄像头的关联数据重新写进关联表中
            $placeVcrData = [
                $formData[1],
                'station_id' => $station_id
            ];
            $result = StationVcr::where('station_id', '=', $id)->create($placeVcrData);
            array_push($resultArray, $result);

            //更改摄像机表中摄像机的状态
            $vcr_id = $placeVcrData['vcr_id'];
            $vcr = Vcr::findOrFail($vcr_id);  //找到选择的摄像机
            $vcr->status = 0;  //变更状态,但是不一定是0
            $result = $vcr->save();
            array_push($resultArray, $result);

            //判断$resultArray中是否有键值为false
            if (array_search('false', $resultArray) !== false) {
                $connection->rollBack();
                throw new \Exception('新建房间时发生了错误,请重试!');
            } else {
                $connection->commit();
                return true;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}