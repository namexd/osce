<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@sulida.com>
 * @date 2016年3月9日11:02:12
 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamPlanRecord;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreeningStudent;

/**监控标记学生替考记录表
 * Class Answer
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class ExamMonitor extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_monitor';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','station_id', 'exam_id', 'student_id', 'created_user_id', 'created_at', 'updated_at','type','description','exam_screening_id','reason','room_id','status'];

    public function ExamMonitor($examId,$studentId,$screeningId){
        $ExamMonitor = $this->where('exam_id','=',$examId)
                            ->where('student_id','=',$studentId)
                            ->where('exam_screening_id','=',$screeningId)
                            ->get();
        return $ExamMonitor;
    }
}