<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;


use Illuminate\Support\Facades\DB;
use Auth;

class Area extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'area';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'name',
        'code',
        'cate',
        'description',
        'created_user_id',
        'address',
        'floor',
        'room_number',
        'proportion'
    ];

    /**
     * 摄像机和区域的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areaVcr()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Vcr', 'area_vcr', 'area_id', 'vcr_id');
    }


    /**
     * 查出对应的类型数据
     * @return mixed
     */
    public function showRoomCateList()
    {
        return $this->select('id', 'name', 'cate')->get();
    }

    /**
     * 删除考试区域
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteArea($id)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
            //根据id在关联表中寻找，如果有的话，就报错，不允许删除
            $areaVcrs = AreaVcr::where('area_id', $id)->get();
            if (!$areaVcrs->isEmpty()) {
                if (!AreaVcr::where('area_id', $id)->delete()) {
                    throw new \Exception('该区域已经与摄像头相关联，无法删除！');
                }
                foreach ($areaVcrs as $areaVcr) {
                    $vcr = Vcr::findOrFail($areaVcr->vcr_id);
                    $vcr->used = 0;
                    if (!$vcr->save()) {
                        throw new \Exception('更新摄像机状态失败！');
                    }
                }
            };

            if (!$result = $this->where('id', $id)->delete()) {
                throw new \Exception('删除区域失败！');
            }

            $connection->commit();
            return $result;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 修改区域
     * @param $id
     * @param $vcr_id
     * @param $formData
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-17 10：16
     */
    public function editAreaData($id, $vcr_id, $formData)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('操作人不存在，请先登录');
            }
            //更新考场数据
            $result = $this->updateData($id, $formData);
            if (!$result) {
                throw new \Exception('数据修改失败！请重试');
            }
            //更新考场绑定摄像机的数据
            //先删除目前的关联
            $areaVcrs = AreaVcr::where('area_id', $id)->get();
            if (!$areaVcrs->isEmpty()) {
                $areaVcr = $areaVcrs->first();
                if (!$areaVcr->delete()) {
                    throw new \Exception('考场绑定摄像机失败！请重试');
                }
                if ($vcr_id !== '0') {
                    $data = [
                        'area_id' => $id,
                        'vcr_id' => $vcr_id,
                        'created_user_id' => $user->id
                    ];

                    if (!AreaVcr::create($data)) {
                        throw new \Exception('考场绑定摄像机失败！请重试');
                    };

                    //修改当前摄像机状态
                    $vcr = Vcr::FindOrFail($vcr_id);
                    $vcr->used = 1;
                    if (!$vcr->save()) {
                        throw new \Exception('考场绑定摄像机失败！请重试');
                    }
                }
                //将原来的摄像机的状态恢复
                $vcr = Vcr::findOrFail($areaVcr->vcr_id);
                $vcr->used = 0;
                if (!$vcr->save()) {
                    throw new \Exception('考场绑定摄像机失败！请重试');
                }
            } else {
                if ($vcr_id !== '0') {
                    $this->vcr($vcr_id, $user->id, Area::find($id));
                }
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 场所的新增
     * @param $formData
     * @param $vcrId
     * @param $userId
     * @return static
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-17 10:07
     */
    public function createRoom($formData, $vcrId, $userId)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            if (!$room = $this->create($formData)) {
                throw new \Exception('新建房间失败');
            }

            if ($vcrId !== "0") {
                $this->vcr($vcrId, $userId, $room);
            }

            $connection->commit();
            return $room;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $vcrId
     * @param $userId
     * @param $room
     * @throws \Exception
     * @author Jiangzhiheng
     * @time
     */
    private function vcr($vcrId, $userId, $room)
    {
        $data = [
            'area_id' => $room->id,
            'vcr_id' => $vcrId,
            'created_user_id' => $userId
        ];

        if (!AreaVcr::create($data)) {
            throw new \Exception('新建房间失败');
        }

        $vcr = Vcr::findOrFail($vcrId);
        $vcr->used = 1;
        if (!$vcr->save()) {
            throw new \Exception('新建房间失败');
        }
    }


}