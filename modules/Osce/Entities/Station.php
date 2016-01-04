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
    public $search = [];

    public function vcrStation()
    {
        return $this->hasMany('Modules\Osce\Entities\PlaceVcr', 'station_id', 'id');
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
            $result = PlaceVcr::create($placeVcrData);
            array_push($resultArray, $result);

            //更改摄像机表中摄像机的状态
            $vcr_id = $placeVcrData['vcr_id'];
            $vcr = Vcr::findOrFail($vcr_id);
            $vcr->status = 0;  //变更状态,但是不一定是0
            $result = $vcr->save();
            array_push($resultArray, $result);

            //判断$resultArray中是否有键值为false
            if (array_search('false', $resultArray) !== false) {
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
            'place_vcr.vcr_id as vcr_id',
        ]);

        return $builder->first();
    }


    public function updateStation($formData, $id)
    {
        try {
            $resultArray = [];
            //开启事务
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            //将原来的摄像机的状态回位
            //通过传入的考站的id找到原来的摄像机
            $originalVcrId = PlaceVcr::where('station_id', '=', $id)->select('vcr_id')->first();
            $result = Vcr::findOrFail($originalVcrId);
            $result->status = 1; //可能是1，也可能是其他值
            $result = $result->save();
            array_push($resultArray, $result);

            //将place表的数据修改place表
            $placeData = $formData[0];
            $result = $this->where($this->table.'.id', '=', $id)->update($placeData);
            //获得修改后的id
            $station_id = $result->id;
            array_push($resultArray, $result);

            //将考场摄像头的关联数据编辑关联表中
            $placeVcrData = [
                $formData[1],
                'station_id' => $station_id
            ];
            $result = PlaceVcr::where('station_id', '=', $id)->update($placeVcrData);
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
                throw new \Exceptio('新建房间时发生了错误,请重试!');
            } else {
                $connection->commit();
                return true;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}