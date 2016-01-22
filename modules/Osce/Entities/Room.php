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
    public function showRoomList($keyword = '', $type = 1, $id = '')
    {
        try {
            //如果传入了id,就说明是编辑,那就只读取该id的数据
            //如果传入的type是1，就说明是编辑考场
            if ($id !== "") {
                //如果传入的type是其他值，就说明是编辑其他地点，展示对应的摄像头
                if ($type != 1) {
                    return Vcr::findOrFail($id);
                }
                $builder = $this->where('id', '=', $id);
                $result = $builder->select('id', 'name', 'description', 'address', 'code')->first();
                if (!$result){
                    throw new \Exception('查无此考场！');
                }
                return $result;
            }

            //判断传入的type是否合法
            $area = Area::where('area.cate', '=', $type)->first();

			//0.1 测试分支 合并到0.2时 因冲突注释
            //$area = Area::where('area.cate', '=', $type)->get();
            if (!$area) {
                throw new \Exception('传入的场所区域不合法!');
            }

            //根据type选择要查询的对象
            if ($type == 1) {
                //如果是1，就说明是考场
                //选择查询的字段
                $builder = $this->select([
                    $this->table . '.id as id',
                    $this->table . '.name as name',
                    $this->table . '.code as code',
                    $this->table . '.nfc as nfc',
                    $this->table . '.address as address',
                    $this->table . '.description as description',
                ]);
            } else {
                //如果是其他，就只与摄像头之间关联
                //得到当前传入的type对应哪个区域的摄像头
                $areaId = $area->id;

                //通过关联拿到对应的摄像机的数据
                $vcr = Area::find($areaId)->areaVcr()->get();
                if (!$vcr) {
                    throw new \Exception('系统出了问题，请重试！');
                }
            }
            //如果keyword不为空，那么就进行模糊查询
            if ($keyword !== "") {
                $builder = $builder->where($this->table . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($this->table . '.description', 'like', '%' . $keyword . '%');
            }

            //判断是否是考场
            $result = empty($vcr) ? $builder->paginate(10) : $vcr;

            //将区域的信息全部传回去
            $area = Area::all();

            return array($area, $result);
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
            //根据id在关联表中寻找，如果有的话，就报错，不允许删除
            if (RoomStation::where('room_id',$id)->first()) {
                $connection->rollBack();
                throw new \Exception('该房间已经与考站相关联，无法删除！');
            };

            if (RoomVcr::where('room_id',$id)->first()) {
                $connection->rollBack();
                throw new \Exception('该房间已经与摄像头相关联，无法删除');
            }

            if ($result = $this->where('id',$id)->delete()) {
                $connection->commit();
                return $result;
            }

        } catch (\Exception $ex) {
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
}