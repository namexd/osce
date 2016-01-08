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

    //考生查询
    public function getList($formData=''){
         $builder=$this->Join(
             'student','student.id','=','exam.student_id'
         );
         if($formData['exam_name']){
            $builder=$builder->where('exam.name','like','%'.$formData['exam_name'].'');
         }
        if($formData['student_name']){
            $builder=$builder->where('student.name','like','%'.$formData['student_name'].'');
        }

        $builder->select([
            'exam.name as exam_name',
            'student.name as student_name',
            'student.gender as gender',
            'student.code as code',
            'student.id_card as idCard',
            'student.mobile as mobile',
        ]);

        $builder->orderBy('exam.begin_dt');
        return $builder->paginate(10);
    }
}