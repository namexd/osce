<?php
/**
 * 考核项目
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use DB;

class Subject extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'subject';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['title', 'score', 'order', 'status', 'created_user_id'];
    public $search = [];

    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    /**
     * 获取课题列表（考核点的盒子的列表）
     * @access public
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 21:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList(){
        return $this->  paginate(config('osce.page_size'));
    }

    /**
     * 新增课题
     * @access public
     * * @param $data
     * * @param $points
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 22:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addSubject($data,$points){
        $connection =   DB::connection($this->connection);
        $connection ->beginTransaction();

        try{
            if($subject =   $this   ->  create($data))
            {
                $this   ->  addPoint($subject,$points);
            }
            else
            {
                throw new \Exception('新增考核标准失败');
            }
            $connection ->  commit();
            return $subject;
        }
        catch(\Exception $ex)
        {
            $connection ->  rollBack();
            throw $ex;
        }
    }

    /**
     * 编辑课题
     * @access public
     *
     * * @param $id
     * * @param $data
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editTopic($id,$data){
        $subject    =   $this->find($id);
        $connection =   DB::connection($this->connection);
        $connection ->beginTransaction();

        try{

        }
        catch(\Exception $ex)
        {
            return $ex;
        }

    }

    /**
     * 新增考核点
     * @access public
     *
     * * @param $subject
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * @param array $points
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE}${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    protected function addPoint($subject,array $points){
        $SubjectItemModel    = new SubjectItem();

        try{
            foreach($points as $point)
            {
                $SubjectItemModel   -> addItem($subject,$point);
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
}