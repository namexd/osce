<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:04
 */

namespace Modules\Msc\Entities;

use App\Repositories\Common;
use Illuminate\Database\Eloquent\Model;
use DB;

class ResourcesOpenlabHistory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_history';
    protected $fillable 	=	['resources_openlab_apply_id', 'resources_lab_id', 'begin_datetime','end_datetime','group_id','teacher_uid','result_poweroff','result_init'];

    public    $timestamps	=	true;
    protected $primaryKey	=	'id';
    public    $incrementing	=	true;

    /**
     * 根据申请ID 删除历史记录
     * @access public
     *
     * * string        id        申请ID(必须的)
     *
     * @return booler
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 215-12-23 11:10
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function delHistoryByApplyId($id){
        try{
            $history    =   $this   ->  where   ('resources_openlab_apply_id','=',$id)->first();
            if($history)
            {
                $result =   $history    ->  delete();
                if(!$result)
                {
                    throw new \Exception('删除历史记录失败');
                }
            }
            return true;
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
}