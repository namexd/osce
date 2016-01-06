<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/6
 * Time: 10:33
 */

namespace Modules\Osce\Entities;

use DB;
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
     * 新增考试的方法
     * @param $id
     * @return bool
     */
    public function addExam($data)
    {
        try{
            $resultArray = [];
            $connection = DB::connection($this->connection);
            $connection ->beginTransaction();

            //将exam表的数据插入exam表
            $examData = $data[0];
            $result = $this->create($examData);
            //获得插入后的id
            $exam_id = $result->id;
            array_push($resultArray, $result);

            //将考试对应的考次关联数据写入考试场次表中
            $examScreeningData = $data[1];
            foreach($examScreeningData as $key => $value){
                $examScreeningData[$key]['exam_id'] = $exam_id;
            }
            $result = ExamScreening::create($examScreeningData);
            array_push($resultArray, $result);

            //判断$resultArray中是否有键值为false,如果有，那就说明前面有错误
            if (array_search(false, $resultArray) !== false) {
                $connection->rollBack();
                throw new \Exceptio('新增考试失败,请重试!');
            } else {
                $connection->commit();
                return true;
            }

        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}