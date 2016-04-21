<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13 0013
 * Time: 20:54
 */

namespace Modules\Osce\Entities;


class Standard extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'standard';
    public    $timestamps   = false;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['title'];
    public    $search       = [];


    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    public function standardItem(){
        return $this->hasMany('Modules\Osce\Entities\StandardItem','standard_id','id');

    }

    /**
     * 创建考核点、考核项
     * @access public
     * @param $point
     * @return static
     *
     * @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date   2016-04-12 12:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addStandard($standard_name){
        $result = $this->where('title','=',$standard_name)->first();
        if (is_null($result)){
            $data = ['title'=>$standard_name];
            $result = $this->create($data);
        }
        return $result;
    }

    /**
     * 删除考试项目 对应的 评分标准
     *
     * @param $subject
     * @return bool
     * @throws \Exception
     *
     * @author Zhoufuxiang
     * @date   2016-04-13 10:55
     */
    public function delStandard($subject)
    {
        try{
            if(!$subject->standards->isEmpty())
            {
                $standardItem = new StandardItem();
                foreach ($subject->standards as $standard)
                {
                    $standardItem->delItemBySubject($standard);
                    //删除评分标准
                    if(!$standard->delete()){
                        throw new \Exception('删除评分标准失败');
                    }
                }
            }
            return true;

        } catch (\Exception $ex){
            throw $ex;
        }
    }
    
}