<?php
/**
 * Created by PhpStorm.
 * 科室模型
 * @author tangjun <tangjun@misrobot.com>
 * @date 2015-12-29 13:58
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Msc\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TeacherDept
 * @package Modules\Msc\Entities
 */

class TeacherDept extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'teacher_dept';
    protected $fillable 	=	["id","name","code","pid","level","created_user_id","description"];
    public $incrementing	=	true;
    public $timestamps	=	false;


    /**
     * @param $data
     * @return Array
     * @author tangjun <tangjun@misrobot.com>
     * @date 2015-12-29 14:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function AddDept($data){
        return  $this->create($data);
    }


    /**
     * @param $id
     * @param $data
     * @return bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日14:45:26
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function UpdateDept($id,$data){
        return  $this->where('id','=',$id)->update($data);
    }

    /**
     * @param $id
     * @return  bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日14:47:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function DelDept($id){
        return  $this->where('id','=',$id)->delete();
    }


    /**
     * @param int $pid
     * @return Array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日14:54:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SelectDept(){
        return  $this->select(["id","name","code","pid","level","description"])->get();
    }


    /**
     * @param int $pid
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2015年12月29日16:27:39
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function PidSelectDept($pid=0){
        return  $this->where('pid','=',$pid)->get();
    }


}