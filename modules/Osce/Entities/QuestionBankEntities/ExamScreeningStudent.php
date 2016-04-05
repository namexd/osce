<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7 0007
 * Time: 14:55
 */

namespace Modules\Osce\Entities;


use Illuminate\Support\Facades\DB;

class ExamScreeningStudent extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_screening_student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_screening_id', 'student_id', 'is_notity', 'is_signin', 'signin_dt', 'watch_id', 'create_user_id'];

    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }
    public function getExamList(){
        $DB = \DB::connection('osce_mis');

/*        $builder = $this->ExamResultModel->leftJoin('station', function($join){
            $join -> on('station.id', '=', 'exam_result.station_id');
        })->leftJoin('subject', function($join){
            $join -> on('subject.id', '=','station.subject_id');
        })->leftJoin('exam_screening', function($join){
            $join -> on('exam_screening.id', '=','exam_result.exam_screening_id');
        })->where('exam_screening.exam_id','=',$ExamId);

        //TODO 加上该条件为统计合格人数
        if($qualified){
            $builder->where($DB->raw('exam_result.score/subject.score'),'>=','0.6');
        }

        $return = $builder->groupBy('subjectId')
            ->select(
                'subject.id as subjectId',
                'subject.title',
                'station.mins',
                $DB->raw('avg(exam_result.time) as timeAvg'),
                $DB->raw('avg(exam_result.score) as scoreAvg'),
                $DB->raw('count(exam_result.student_id) as studentQuantity'),
                'subject.score as scoreTotal',
                'exam_result.score as score'
            )
            ->get();
        return  $return;*/
    }

    /**
     * 根据场次id 查询 相应的student
     * @access public
     *
     * @param $exam_screening_id
     * @return object
     * @throws \Exception
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        exam_screening_id        考场id(必须的)
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function selectExamScreeningStudent($exam_screening_id)
    {
        try {
            $result = $this->where('exam_screening_id', '=', $exam_screening_id)
                ->leftJoin('student', function ($join) {
                    $join->on($this->table.'.student_id', '=', 'student.id');
                });

            return $result->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}