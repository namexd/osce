<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31 0031
 * Time: 15:37
 */
namespace Modules\Osce\Entities;

use DB;
use Auth;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\CommonModel;

class Supplies extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'supplies';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'id',
        'name',
        'create_user_id',
    ];
    public $search = [];
    
    
    //获取用物列表
    public  function getList($name){
        if (!is_null($name)) {
            return $this->where('name', 'like', '%' . $name . '%')->paginate(config('osce.page_size'));
        } else {
            return $this->paginate(config('osce.page_size'));
        }
        
        
    }
    //


    
}