<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:47
 */

namespace Modules\Osce\Entities;

use DB;

class CaseModel extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'cases';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'description', 'create_user_id'];
    public $search = [];

    /**
     * 获取列表
     * $formData内有
     * @param $formData
     * @return mixed
     */
    public function getList($paginate)
    {

        //查询出数据
        $builder = $this->select([
            'id',
            'name',
            'description'
        ])->paginate($paginate);

        return $builder;
    }

    /**
     * 删除病例
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteData($id)
    {
        try {
            //判断在关联表中是否有数据
            $result = StationCase::where('station_case.case_id', '=', $id)->select('id')->first();
            if ($result) {
                throw new \Exception('不能删除此病例，因为与其他条目相关联');
            }

            return $this->where($this->table.'.id', '=', $id)->delete();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}