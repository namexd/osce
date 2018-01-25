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
    protected $fillable = ['exam_screening_id', 'student_id', 'is_notity', 'is_signin', 'signin_dt', 'watch_id', 'create_user_id','is_end','status','description'];

    public function student()
    {
        return $this->hasOne('\Modules\Osce\Entities\Student','id','student_id');
    }

    public function scopeWatch($query)
    {
        return $query->join('watch', 'watch.id', '=', 'exam_screening_student.watch_id');
    }

    public function scopeIsEnd($query, $value)
    {
        return $query->where($this->table . '.is_end', '=' ,$value);
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
     * @author fandian <fandian@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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

    /**
     * 根据watch code 返回学生id
     * @param $code  腕表code
     * @param $screenId 场次id
     * @return object
     *
     * @author wt <wangtao@sulida.com>
     * @date   2016-05-7
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getStudentByWatchCode($code,$screenId){
      return  $this->leftJoin('watch', function ($join) {
            $join->on($this->table.'.watch_id', '=', 'watch.id');
        })->where('watch.code',$code)->where($this->table.'.exam_screening_id',$screenId)->first();
    }

    /**
     * 缺考学生
     * @param $examScreenId
     * @param $studentId
     * @throws \Exception
     */
    public static function dropStudent($examScreenId, $studentId) {
        $connection = \DB::connection();
        $connection ->beginTransaction();
        try {
            ExamQueue::query()
                ->where('exam_screening_id', '=', $examScreenId)
                ->where('student_id', '=', $studentId)
                ->update(['status' => 4]);

//            ExamScreeningStudent::query()
//                ->where('exam_screening_id', '=', $examScreenId)
//                ->where('student_id', '=', $studentId)
//                ->update(['is_end' => 1, 'status' => 1]);

//            ExamOrder::query()
//                ->where('exam_screening_id', '=', $examScreenId)
//                ->where('student_id', '=', $studentId)
//                ->where('status', '=', 0)
//                ->update(['status' => 3]);
            $connection->commit();
        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }
}