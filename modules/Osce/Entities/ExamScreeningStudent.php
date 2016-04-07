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

    public function addExaminee($examineeData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //查询id_card是否已经存在student表中
            $result = Student::where('id_card', '=', $examineeData['id_card'])->select('id')->first();
            //将examinee表的数据插入student表
            if(!$result && !$student = Student::create($examineeData)){
                throw new \Exception('');
            }

            $connection->commit();
            return $result;

        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }


    //exam_screening
    public function screening(){
        return $this->hasOne('Modules\Osce\Entities\ExamScreening', 'id', 'exam_screening_id');
    }


    //查找学生所报考试
    public function getExamings($student){
        $builder = $this->where('exam_screening_student.is_signin','=',1)->where('exam_screening_student.is_end','=',1)->leftjoin('exam_screening',function($examScreening){
            $examScreening->on('exam_screening.id','=','exam_screening_student.exam_screening_id');
        })->leftjoin('exam_queue',function($examQueue){
            $examQueue->on('exam_queue.exam_screening_id','=','exam_screening.id');
        })->leftjoin('exam_station',function($exam_station){
            $exam_station->on('exam_station.station_id','=','exam_queue.station_id');
        })->leftjoin('exam',function($exam){
            $exam->on('exam.id','=','exam_station.exam_id');
        })->select('exam.id','exam.name','exam_station.station_id','exam.status')->get();

        return $builder;
    }
}