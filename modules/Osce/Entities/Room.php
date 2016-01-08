<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:00
 */

namespace Modules\Osce\Entities;


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
            if ($id !== "") {
                $builder = $this->where('id', '=', $id);
                $result = $builder->select('id', 'name', 'description')->first();
                if (!$result){
                    throw new \Exception('查无此考场！');
                }
                return $result;
            }

            //判断传入的type是否合法
            $area = Area::where('area.cate', '=', $type)->first();
            if (!$area) {
                throw new \Exception('传入的场所区域不合法！');
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
                $builder = $builder->where($this->table . '.name', '=', '%' . $keyword . '%')
                    ->orWhere($this->table . '.description', '=', '%' . $keyword . '%');
            }

            //判断是否是考场
            $result = empty($vcr) ? $builder->paginate(10) : $vcr;

            return array($area, $result);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


}