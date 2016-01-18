<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
class Exam extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'begin_dt', 'end_dt', 'create_user_id', 'status', 'description'];

    /**
     * 考试与考站的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function examStation()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\Station','exam_flow_station','exam_id','station_id');
    }

    /**
     * 考试场次关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examScreening()
    {
        return $this    ->  hasMany('\Modules\Osce\Entities\ExamScreening','exam_id','id');
    }

    /**
     *  考生关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function students(){
        return $this    ->  hasMany('\Modules\Osce\Entities\Student','exam_id','id');
    }

    /**
     * 考试流程节点关联
     * @access public
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function flows(){
        return $this    ->  hasMany('\Modules\Osce\Entities\ExamFlow','exam_id','id');
    }
    /**
     * 展示考试列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showExamList($formData='')
    {
        try {
            //不寻找已经被软删除的数据
            $builder = $this->where('status' , '<>' , 0);

            if($formData){
               $builder=$builder->where('name','like',$formData['exam_name'].'%');
            }

            //寻找相似的字段
            $builder = $builder->select([
                'id',
                'name',
                'begin_dt',
                'end_dt',
                'description',
                'total'
            ])->orderBy('begin_dt', 'desc');

            return $builder->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除的方法
     * @param $id
     * @return bool
     */
    public function deleteData($id)
    {
        try {
            $connection = DB::connection($this->connection);
            $connection->beginTransaction();

            $model = $this->findOrFail($id);
            //更改名字
            $model->name = $model->name . '_delete';
            //更改状态
            $model->status = 0;
            if (!($model->save())) {
                $connection->rollBack();
                throw new \Exception('删除考试失败，请重试！');
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {

        }
    }

    /**
     *
     * @access public
     *
     * @param array $examData 考试基本信息
     * * string        name        参数中文名(必须的)
     * * string    create_user_id       参数中文名(必须的)
     * @param array $examScreeningData 场次信息
     * * string        exam_id          参数中文名(必须的)
     * * string        begin_dt         参数中文名(必须的)
     * * string        end_dt           参数中文名(必须的)
     * * string     create_user_id      参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addExam(array $examData,array $examScreeningData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //将exam表的数据插入exam表
            if(!$result = $this->create($examData))
            {
                throw new \Exception('创建考试基本信息失败');
            }
            //将考试对应的考次关联数据写入考试场次表中
            foreach($examScreeningData as $key => $value){
                $value['exam_id']    =   $result->id;
                if(!$examScreening = ExamScreening::create($value))
                {
                    throw new \Exception('创建考试场次信息失败');
                }
            }
            $connection->commit();
            return $result;

        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 保存编辑考试 数据
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        exam_id        考试id(必须的)
     * * string        begin_dt       开始时间(必须的)
     * * string        end_dt         结束时间(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editExam($exam_id, array $examData, array $examScreeningData)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {
            //更新考试信息
            if (!$result = $this->updateData($exam_id, $examData)) {
                throw new \Exception('修改考试信息失败!');
            }
            $examScreening_ids = [];
            //判断输入的时间是否有误
            foreach ($examScreeningData as $value) {
                //不存在id,为新添加的数据
                if (!isset($value['id'])) {
                    $value['exam_id'] = $exam_id;

                    if (!$result = ExamScreening::create($value)) {
                        throw new \Exception('添加考试场次信息失败');
                    }
                    array_push($examScreening_ids, $result->id);
                } else {
                    array_push($examScreening_ids, $value['id']);
                    $examScreening = new ExamScreening();

                    if (!$result = $examScreening->updateData($value['id'], $value)) {
                        throw new \Exception('更新考试场次信息失败');
                    }
                }
            }
            //查询是否有要删除的考试场次
            $result = ExamScreening::where('exam_id', '=', $exam_id)->whereNotIn('id', $examScreening_ids)->get();
            if (count($result) != 0) {
                foreach ($result as $value) {
                    if (!$res = ExamScreening::where('id', '=', $value['id'])->delete()) {
                        throw new \Exception('删除考试场次信息失败');
                    }
                }
            }

            $connection->commit();
            return true;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    //考生查询

    public function getList($formData=''){
         $builder=$this->Join('student','student.exam_id','=','exam.id');
        if($formData['exam_name']){
            $builder=$builder->where('exam.name','like','%'.$formData['exam_name'].'');
         }
        if($formData['student_name']){
            $builder=$builder->where('student.name','like','%'.$formData['student_name'].'');
        }

        $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.code as code',
            'student.idcard as idCard',
            'student.mobile as mobile',
            'student.user_id as user_id',
        ]);

        $builder->orderBy('exam.begin_dt');
        return $builder->paginate(config('msc.page_size'));
    }
//查询今日考试
    public function getTodayList($startTime,$endtime){

          $builder=$this->select(DB::raw(
              implode(',',[
                  $this->table.'.id as id',
                  $this->table.'.name as exam_name',
                  $this->table.'.begin_dt as begin_dt',
                  $this->table.'.end_dt as end_dt',
                  $this->table.'.description as description',
              ])
            )
          );
        $builder=$builder->whereRaw(
             'unix_timestamp('.$this->table.'.begin_dt) > ?',
             [
                 $startTime
             ]
         );
        $builder=$builder->whereRaw(
             'unix_timestamp('.$this->table.'.end_dt) < ?',
             [
                 $endtime
             ]
         );
        $data=$builder->get();

        return $data;
    }

    public function getExamRoomData($exam_id)
    {
        try {
            return $this->leftJoin( 'exam_room',
                function ($join) {
                    $join->on($this->table . '.id' , '=' , 'exam_room.exam_id');
                })->leftJoin ( 'exam_flow_room',
                function ($join) {
                    $join->on('exam_flow_room.room_id' , '=' , 'exam_room.room_id');
                })->leftJoin( 'room',
                function ($join) {
                    $join->on('room.id' , '=' , 'exam_room.room_id');
                })->where($this->table.'.id', '=', $exam_id)
                ->select([
                    'room.id',
                    'room.name',
                    'exam_flow_room.serialnumber as serialnumber',
                    'exam_flow_room.flow_id as flow_id'
                ])->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //获取候考教室列表
    public function getWriteRoom($exam_id){
       $time=time();
       try {
           $builder = $this->Join('exam_room', 'exam.id', '=', 'exam_room.exam_id');
           $builder = $builder->Join('room', 'room.id', '=', 'exam_room.room_id');
           $builder = $builder->where('exam.id', $exam_id);
           $builder = $builder->whereRaw(
               'unix_timestamp(' . $this->table . '.begin_dt) > ?',
               [
                   $time
               ]
           );
       }
       catch(\Exception $ex)
       {
            throw new $ex;
       }
    }

    public function getTodayList($time){

    }
}