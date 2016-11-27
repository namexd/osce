<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/7 0007
 * Time: 14:00
 */

namespace Modules\Osce\Entities;

use DB;

class SubjectSpecialScore extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject_special_score';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['subject_id', 'title', 'score', 'rate', 'created_user_id'];


    public function subject(){
        return $this->hasOne('\Modules\Osce\Entities\Subject', 'id', 'subject_id');
    }

    public function getSubjectSpecialScore($suject_id){
        $result = $this->where('subject_id', '=', $suject_id)->get();
        return $result;
    }

    /**
     * 添加考试项目——特殊评分项
     * @param $subject_id
     * @param $speScores
     * @param $user_id
     * @return bool
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @data   2016-07-01  16:00
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function addSubjectSpecialScore($subject_id, $speScores, $user_id)
    {
        $ids = [];
        //添加、更新 考试项目——特殊评分项
        if(!empty($speScores)){
            foreach ($speScores as $speScore)
            {
                //存在ID，则更新特殊评分项信息
                if(array_key_exists('id', $speScore))
                {
                    $ids[] =  $speScore['id'];
                    $result = $this->where('id', '=', $speScore['id'])->update($speScore);
                    if(!$result){
                        throw new \Exception('更新特殊评分项信息失败');
                    }
                }else       //否则添加新的特殊评分项
                {
                    $data = [
                        'subject_id'        => $subject_id,
                        'title'             => $speScore['title'],
                        'score'             => $speScore['score'],
                        'rate'              => $speScore['rate'],
                        'created_user_id'   => $user_id
                    ];
                    $result = SubjectSpecialScore::create($data);
                    if(!$result){
                        throw new \Exception('添加新的特殊评分项失败');
                    }
                    $ids[] =  $result->id;
                }
            }
        }

        //根据ID 数组删除多余的特殊评分项
        $subjectSpeScores = SubjectSpecialScore::whereNotIn('id', $ids)->where('subject_id', '=', $subject_id)->get();
        if(!$subjectSpeScores->isEmpty()){
            foreach ($subjectSpeScores as $subjectSpeScore) {
                $subjectSpeScore->delete();
            }
        }

        return true;
    }
}