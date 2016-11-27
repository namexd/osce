<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@163.com>
 * @date 2016年3月9日11:02:12
 * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;

/**考试-试卷-考站关联模型
 * Class ExamPaperExamStation
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class ExamPaperExamStation extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_paper_exam_station';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['id', 'exam_id', 'exam_paper_id', 'station_id'];

    public function exam()
    {
        $this->hasOne('Modules\Osce\Entities\Exam', 'id', 'exam_id');
    }

    public function examPaper()
    {
        $this->hasOne('Modules\Osce\Entities\ExamPaper', 'id', 'exam_paper_id');
    }
}