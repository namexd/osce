<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/12 0012
 * Time: 14:48
 */

namespace Modules\Osce\Entities;


class SubjectStandard extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_standard';
    public    $timestamps   = false;
//    protected $primaryKey   = 'id';
    public    $incrementing = false;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'standard_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

    public function standard(){
        return $this->hasOne('\Modules\Osce\Entities\Standard', 'id', 'standard_id');
    }

    /**
     * 添加考试项目、评分标准间的关系
     * @param $subject
     * @param $standard
     * @return object
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date 2016-04-06 12:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addSubjectStandard($subject, $standard){
        $data = [
            'subject_id'    => $subject->id,
            'standard_id'   => $standard->id
        ];
        return $this->create($data);
    }

    /**
     * 获取考核标准信息（ID）
     * @param $subject
     * @param $standard_name
     * @return object
     *
     * @author Zhoufuxiang <zhoufuxiang@misrobot.com>
     * @date   2016-04-06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStandard($subject, $standard_name){
        $result = $this->where('subject_id','=',$subject->id)->select('standard_id as id')->first();
        if (is_null($result)){
            $standardModel = new Standard();
            $result = $standardModel->addStandard($standard_name);
            //添加考试项目、评分标准间的关系
            $this->addSubjectStandard($subject, $result);
        }
        return $result;
    }
}