<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5 0005
 * Time: 16:24
 */

namespace Modules\Osce\Entities;


class ExamGradation extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_gradation';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['exam_id', 'order', 'gradation_number', 'sequence_cate', 'created_user_id'];


    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam', 'id', 'exam_id');
    }

    static public function gradations($examId)
    {
        return ExamGradation::where('exam_id', $examId)
            ->get()
            ->keyBy('order');
    }

    /**
     * 根据阶段ID 获取 场次信息
     * @param $gradationId
     * @return mixed
     * @throws \Exception
     */
    public static function getScreenIdByGradationId($gradationId)
    {
        $gradation = ExamGradation::where('id', '=', $gradationId)->select('order')->first();
        if (is_null($gradation)){
            throw new \Exception('阶段有误！');
        }
        $screening = ExamScreening::where('gradation_order', '=', $gradation->order)->first();
        if (is_null($screening)){
            throw new \Exception('场次有误！');
        }
        return $screening;
    }


}