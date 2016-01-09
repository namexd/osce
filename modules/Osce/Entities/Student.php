<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7 0007
 * Time: 10:11
 */

namespace Modules\Osce\Entities;

use DB;
class Student extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'id_card', 'exam_id'];

    /**
     * 展示考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showStudentList()
    {
        try {
            $student = $this->select([
                'id',
                'name',
                'id_card',
                'exam_id'
            ]);

            return $student->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 展示 考试 对应的考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function selectExamStudent($exam_id, $keyword)
    {
        try {
            $result = $this->where('exam_id', '=', $exam_id);

            //如果keyword不为空，那么就进行模糊查询
            if ($keyword['keyword'] !== null) {
                $result = $result->where($this->table . '.name', '=', '%' . $keyword['keyword'] . '%')
                    ->orWhere($this->table . '.id_card', '=', '%' . $keyword['keyword'] . '%');
//                    ->orWhere($this->table . '.phone', '=', '%' .$keyword['keyword'] . '%')
//                    ->orWhere($this->table . '.学号', '=', '%' .$keyword['keyword'] . '%');
            }

            return $result->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除考生的方法
     * @param $id
     * @return bool
     */
    public function deleteData($student_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {

            if (!$result = $this->where($this->table.'.id', '=', $student_id)->delete())
            {
                throw new \Exception('删除考生失败，请重试！');
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 单个添加考生
     * @return mixed
     * @throws \Exception
     */
    public function addExaminee($examineeData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            //查询id_card是否已经存在student表中
            $student = $this->where('id_card', '=', $examineeData['id_card'])
                            ->where('exam_id', '=', $examineeData['exam_id'])
                            ->select('id')->first();

            //存在则更新数据, 否则新增
            if($student){
                //跟新考生数据
                $student->name    = $examineeData['name'];
                $student->id_card = $examineeData['id_card'];
                $student->exam_id = $examineeData['exam_id'];
                if (!($student->save())) {
                    throw new \Exception('新增考生失败！');
                }
            }else{
                if(!$result = $this->create($examineeData)){
                    throw new \Exception('新增考生失败！');
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