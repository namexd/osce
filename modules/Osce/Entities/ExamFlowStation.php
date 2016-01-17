<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/14
 * Time: 11:04
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Exception;
class ExamFlowStation extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_flow_station';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['serialnumber', 'station_id', 'flow_id', 'created_user_id'];

    public function queueStation(){
        return $this->hasMany('\Modules\Osce\Entities\ExamQueue','station_id','station_id');
    }

    public function station(){
        return $this->hasOne('\Modules\Osce\Entities\Station','id','station_id');
    }
    public function createExamAssignment($examId, array $stationIds = [] , array $teacherIds = [])
    {
        //将数据插入各表，使用事务
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            $user = Auth::user();
            if ($user->isEmpty) {
                throw new Exception('未找到当前操作人信息！');
            }

            //查询考试名
            $exam = Exam::findOrFail($examId)->first();
            foreach ($stationIds as $key => $item) {
                foreach ($item as $stationId) {
                    //根据station_id查对应的名字
                    $station = Station::findOrFail($stationId)->first();
                    //为流程表准备数据
                    $flowsData = [
                        'name' => $exam->name . '=' . $station->name,
                        'created_user_id' => $user->id
                    ];

                    if (!$flowsResult = Flows::create($flowsData)) {
                        throw new Exception('考试流程添加失败');
                    }
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}