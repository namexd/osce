<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;


use Illuminate\Support\Facades\DB;

class Area extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'area';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'cate', 'description','created_user_id'];

    /**
     * 摄像机和区域的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areaVcr()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Vcr','area_vcr','area_id','vcr_id');
    }


    /**
     * 查出对应的类型数据
     * @return mixed
     */
    public function showRoomCateList()
    {
        return $this->select('id','name','cate')->get();
    }

    /**
     * 删除考试区域
     * @return mixed
     */
    public function deleteArea($id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try{
            $area = $this->where('id',$id)->first();
            if($area->cate ==1 && Room::first()){
                throw new \Exception('该考试区域已关联，无法删除！');
            }
            if($result = AreaVcr::where('area_id',$id)->first()){
                throw new \Exception('该考试区域已与摄像机关联，无法删除！');
            }
            //删除考试区域
            if(!$result = $this->where('id',$id)->delete()){
                throw new \Exception('删除失败，请重试！');
            }
            $connection->commit();
            return true;

        } catch(\Exception $ex){
            $connection->rollback();
            throw $ex;
        }
    }

    public function editAreaData($id, $vcr_id, $formData)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            $user = Auth::user();
            if(!$user){
                throw new \Exception('操作人不存在，请先登录');
            }
            //更新考场数据
            $result = $this->updateData($id, $formData);
            if(!$result){
                throw new \Exception('数据修改失败！请重试');
            }
            //更新考场绑定摄像机的数据
            $roomVcr = RoomVcr::where('room_id',$id)->first();
            if(!empty($roomVcr)){
                if(!$roomVcr->update(['vcr_id'=>$vcr_id])){
                    throw new \Exception('考场绑定摄像机失败！请重试');
                }
            }else{
                if(!Area::create(['room_id'=>$id, 'vcr_id'=>$vcr_id, 'created_user_id'=>$user->id])){
                    throw new \Exception('考场绑定摄像机失败！请重试');
                }
            }
            //更改$vcr_id对应的摄像机状态为在线
            if(!Vcr::where('id', $vcr_id)->update(['status'=>1])){
                throw new \Exception('摄像机状态修改失败！请重试');
            }
            $connection->commit();
            return true;

        } catch(\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }

    public function createRoom($formData, $vcrId, $userId)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection -> beginTransaction();

            if (!$room = $this->create($formData)) {
                throw new \Exception('新建房间失败');
            }

            $data=[
                'area_id'=>$room->id,
                'vcr_id'=>$vcrId,
                'created_user_id' => $userId
            ];

            if (!AreaVcr::create($data)) {
                throw new \Exception('新建房间失败');
            }

            $vcr = Vcr::findOrFail($vcrId);
            $vcr->status = 1;
            if (!$vcr->save()) {
                throw new \Exception('新建房间失败');
            }

            $connection->commit();
            return $room;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}