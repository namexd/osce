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
    protected $fillable = ['name', 'code', 'address', 'nfc', 'create_user_id'];
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
     * @param $formData
     * @return
     * @internal param int $pid
     * @internal param null $id
     */
    public function showRoomList($formData)
    {
        if (array_key_exists('id', $formData)) {
            //如果传入了id,那就只读取该id的数据
            $builder = $this->where($this->table . '.id', $formData['id']);
            return $builder->first();
        }


        //如果order不为空的话，就使用order的数据，否则就指定，暂时不考虑排序
//        $orderName = empty($formData['order_name']) ? 1 : $formData['order_name'];
//        $orderBy = empty($formData['order_by']) ? 'desc' : $formData['order_by'];
//        $paramArray = ['created_at'];
//        $builder = $this->order($builder, $orderName, $orderBy, $paramArray);

        //选择查询的字段
        $builder = $this->select([
            $this->table . '.id as id',
            $this->table . '.name as name',
            $this->table . '.code as code',
            $this->table . '.nfc as nfc',
            $this->table . '.address as address',
            $this->table . '.description as description',
            $this->table . '.create_user_id as create_user_id'
        ]);

        //如果keyword不为空，那么就进行模糊查询
        if ($formData['keyword'] !== null) {
            $builder = $builder->where($this->table . '.created_at', '=', '%' . $formData['keyword'] . '%');
        }




        return $builder->paginate(10);
    }



}