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
     * 展示考试列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showExamList()
    {
        try {
            //不寻找已经被软删除的数据
            $builder = $this->where('status' , '<>' , 0);

            //寻找相似的字段
            $builder = $builder->select([
                'id',
                'name',
                'begin_dt',
                'end_dt',
                'description',
                'total'
            ])->orderBy('created_at', 'desc');

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

    public function editExam($exam_id, array $examData, array $examScreeningData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //更新考试信息
            if(!$result = $this->updateData($exam_id, $examData)){
                throw new \Exception('修改考试信息时报!');
            }
            //查询操作人信息
            $user   =   Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            $examScreening_ids = [];
            //判断输入的时间是否有误
            foreach($examScreeningData as $key => $value){
                if(!strtotime($value['begin_dt']) || !strtotime($value['end_dt'])){
                    throw new \Exception('输入的时间有误！');
                }
                //不存在id,为新添加的数据
                if(!isset($value['id'])){
                    $value['exam_id'] = $exam_id;
                    $value['create_user_id'] = $user -> id;

                    if(!$result = ExamScreening::create($value))
                    {
                        throw new \Exception('添加考试场次信息失败');
                    }
                    array_push($examScreening_ids, $result->id);
                }else{
                    array_push($examScreening_ids, $value['id']);
                    $examScreening = new ExamScreening();
                    if(!$result = $examScreening->updateData($value['id'], $value)){
                        throw new \Exception('更新考试场次信息失败');
                    }
                }
            }
            //查询是否有要删除的考试场次
            $result = ExamScreening::where('exam_id', '=', $exam_id)->whereNotIn('id', $examScreening_ids)->get();
            if(count($result) != 0){
                foreach($result as $value){
                    if(!$res = ExamScreening::where('id', '=', $value['id'])->delete()){
                        throw new \Exception('删除考试场次信息失败');
                    }
                }
            }

            $connection->commit();
            return true;
        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }
}