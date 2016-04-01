<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 17:34
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\CommonModel;


class Supply extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'supply';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'create_user_id', 'archived'];


    public $search = [];


    //获取用物列表
    public function getList($name)
    {
        if (!is_null($name)) {
            return $this->where('name', 'like', '%' . $name . '%')->paginate(config('osce.page_size'));
        } else {
            return $this->paginate(config('osce.page_size'));
        }


    }

    public function delSubject($subject)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();

//        $SubjectItemModel = new SubjectItem();
        try {
//            //判断该用物是否关联考试项目
//            $SubjectSupplies = SubjectSupplies::where('supplies_id','=',$subject->id)->get();
//            if(!$SubjectSupplies->isEmpty()){
//                throw new \Exception('该用物已被考试项目关联使用');
//            }
//            $SubjectItemModel->delItemBySubject($subject);
            if ($subject->delete()) {
                $connection->commit();
                return true;
            } else {
                throw new \Exception('删除失败');
            }
        } catch (\Exception $ex) {
            $connection->rollBack();
            if ($ex->getCode() == 23000) {
                throw new \Exception('该用物已经被使用了,不能删除');
            } else {
                throw $ex;
            }
        }
    }


    public function getSupplyList($name)
    {
        if (!is_null($name)) {
            return $this->where('name', 'like', '%' . $name . '%')
                ->select([
                    'id',
                    'name',
                ])->get();
        }
        //查询出数据
        $builder = $this->select([
            'id',
            'name',
        ])->get();
        return $builder;


    }
    
    
    
    

}