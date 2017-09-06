<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:47
 */

namespace Modules\Osce\Entities;

use DB;

class CaseModel extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'cases';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'description', 'create_user_id'];
    public $search = [];

    /**
     * 获取列表
     * $formData内有
     * @param $formData
     * @return mixed
     */
    public function getList($paginate)
    {

        //查询出数据
        $builder = $this->select([
            'id',
            'name',
            'description'
        ])->paginate(config('osce.page_size'));
        return $builder;
    }


    

    /**
     * 删除病例
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteData($id)
    {
        try {
            $caseName = $this->where('id', $id)->select(['name'])->first();
            if(empty($caseName)){
                throw new \Exception('未查询到此病例！');
            }
            //判断在关联表中是否有数据
            $stationCase = StationCase::where('station_case.case_id', '=', $id)->select(['station_id'])->first();
            if($stationCase) {
                $stationName = Station::where('id', $stationCase->station_id)->select(['name'])->first();
                throw new \Exception($caseName->name.'病例 已与 '.$stationName->name.'考站 相关联，不能被删除！');
            }
            $teacher = Teacher::where('case_id',$id)->select(['name'])->first();
            if ($teacher) {
                throw new \Exception($caseName->name.'病例 已与 '.$teacher->name.'SP教师 相关联，不能被删除！');
            }

            //查询是否和考试项目关联
            $subjectCases = SubjectCases::where('case_id','=',$id)->first();
            if($subjectCases){
                $subjectName = Subject::where('id','=',$subjectCases->subject_id)->first();
                throw new \Exception($caseName->name.'病例 已与 '.$subjectName->name.'考试项目 相关联，不能被删除！');
            }


            return $this->where($this->table.'.id', '=', $id)->delete();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 修改病例
     * @param $id
     * @param $formData
     * @return mixed
     * @throws \Exception
     * @author ZouYuChao
     */
    public function updateCase($id, $formData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try {
            $case = CaseModel::where('name', str_replace(' ','',$formData['name']))->where('id','<>',$id)->first();

            if (!is_null($case)) {
                throw new \Exception('已经有此病例名！');
            }
            //查询是否和考试项目关联
            $subjectCases = SubjectCases::where('case_id', '=', $id)->first();
            if(!is_null($subjectCases))
            {
                $subjectName = Subject::where('id', '=', $subjectCases->subject_id)->first();
                if(is_null($subjectName)){
                    throw new \Exception('考试项目 数据有误！');
                }
                throw new \Exception('该病例 已与 '.$subjectName->title.'考试项目 相关联，不能修改！');
            }

            if(!$result = $this->where('id',$id)->update($formData)){
                throw new \Exception('更新失败！');
            }

            $connection->commit();
            return $result;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     *ajax 获取列表
     * $formData内有
     * @param $formData
     * @return mixed
     */
    public function getCasesList($caseName){
        //查询出数据
        $builder = $this->select(['id', 'name', 'description'])->orderBy('id','desc');
        if (!empty($caseName)){
            $builder = $builder->where('name', 'like', '%\\'.$caseName.'%');
        }
        return $builder->get();

    }
}