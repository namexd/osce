<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:00
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;

class Room extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'room';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'address', 'nfc', 'description'];
    public $search = [];

    /**
     * 关联user表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creater()
    {
        return $this->belongsTo('App\Entities\User', 'create_user_id', 'id');
    }


    public function stations(){
        return $this    ->  hasMany('\Modules\Osce\Entities\RoomStation','room_id','id');
    }

    public function Vcrs(){
        return $this->hasMany('\Modules\Osce\Entities\RoomVcr','room_id','id');
    }
    /**
     * 得到room的列表
     * @param string $keyword
     * @param int $type
     * @param string $id
     * @return array
     * @throws \Exception
     * @internal param $formData
     * @internal param int $pid
     * @internal param null $id
     */
    public function showRoomList($keyword = '', $type = '0', $id = '')
    {
        try {

            //如果传入了id,就说明是编辑,那就只读取该id的数据
            //如果传入的type是1，就说明是编辑考场
            if ($id !== "") {
                //如果传入的type是其他值，就说明是编辑其他地点，展示对应的摄像头
                if ($type === '0') {
                    return Room::findOrFail($id);
                } else {
                    return Area::findOrFail($id);
                }

            } else {
                //通过传入的$type来展示响应的数据
                if ($type === "0") {
//                    dd($keyword);
                    $builder = Room::select([
                        'id',
                        'name',
                        'address',
                        'description'
                    ]);

                    if ($keyword !== "") {

                        $builder = $builder->where('name','like','%'.  $keyword . '%');
                    }
                    return $builder -> paginate(config('osce.page_size'));
                } else {
                    $builder = Area::select([
                        'id',
                        'name',
                        'cate',
                        'description'
                    ]);
                    if ($keyword !== "") {
                        $builder = $builder->where('name','like',$keyword);
                    }
                    return $builder->where('cate',$type)->paginate(config('osce.page_size'));
                }

            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public function showVcr($id, $type)
    {
        //根据id和type拿到对应的模型

    }

    /**
     * 房间的删除
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteData($id)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();
            //根据id在关联表中寻找，如果有的话，就删除，否则就报错
            $roomStations = RoomStation::where('room_id','=',$id)->get();
            if (!$roomStations->isEmpty()) {
                if  (!RoomStation::where('room_id',$id)->delete()) {
                    throw new \Exception('房间考站关联删除失败');
                }
            };

            $roomVcrs = RoomVcr::where('room_id','=',$id)->get();
            if (!$roomVcrs->isEmpty()) {
                if (!RoomVcr::where('room_id',$id)->delete()) {
                    throw new \Exception('房间摄像头关联删除失败');
                }
                foreach ($roomVcrs as $roomVcr) {
                    $vcr = Vcr::findOrFail($roomVcr->vcr_id);
                    $vcr->status = 0;
                    if (!$vcr->save()) {
                        throw new \Exception('更新摄像机状态失败！');
                    }
                }

            }


            $room = $this->where('id','=',$id)->first();
            if (!$result = $room->delete()) {
                throw new \Exception('房间删除失败');
            }
            $connection->commit();
            return $result;
        } catch (\Exception $ex) {

            $connection->rollBack();
            throw $ex;
        }

    }


    public function editRoomData($id, $vcr_id, $formData)
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
                if(!RoomVcr::create(['room_id'=>$id, 'vcr_id'=>$vcr_id, 'created_user_id'=>$user->id])){
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

    public function createRoom($formData,$vcrId,$userId)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection -> beginTransaction();

            if (!$room = $this->create($formData)) {
                throw new \Exception('新建房间失败');
            }

            $data=[
                'room_id'=>$room->id,
                'vcr_id'=>$vcrId,
                'created_user_id' => $userId
            ];

            if (!RoomVcr::create($data)) {
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