<?php
/**
 * 考核项目
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use DB;

class Subject extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'subject';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = [
        'title',
        'score',
        'sort',
        'status',
        'created_user_id',
        'description',
        'goods',
        'stem',
        'equipments'
    ];
    public $search = [];

    public function user()
    {
        return $this->hasOne('App\Entities\User', 'created_user_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('Modules\Osce\Entities\SubjectItem', 'subject_id', 'id');
    }

    /**
     * 获取课题列表（考核点的盒子的列表）
     * @access public
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 21:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getList($name)
    {
        if (!is_null($name)) {
            return $this->where('title', 'like', '%' . $name . '%')->paginate(config('osce.page_size'));
        } else {
            return $this->paginate(config('osce.page_size'));
        }
    }

    /**
     * 新增课题
     * @access public
     * * @param $data
     * * @param $points
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-02 22:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addSubject($data, $points, $cases, $goods)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();

        try {
            $user = \Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }

            if ($subject = $this->create($data)) {          //创建考试项目
                $this->addPoint($subject, $points);         //添加考试项目对应的考核内容

                //添加考试项目——病例关系
                if(!$this->addSubjectCases($subject->id, $cases, $user->id)){
                    throw new \Exception('创建考试项目——病例关系失败');
                }
                //添加考试项目——用物关系
                if(!empty($goods)){
                    if(!$this->addSubjectGoods($subject->id, $goods, $user->id)){
                        throw new \Exception('创建考试项目——用物关系失败');
                    }
                }

            } else {
                throw new \Exception('新增考核标准失败');
            }
            $connection->commit();
            return $subject;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 编辑课题
     * @access public
     *
     * *
     * @param $id
     * @param array $data
     * @param $id
     * * * @param array $data
     * * * @param array $points
     * @return object
     * @throws \Exception @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-03 18:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editTopic($id, $data, $points, $cases, $goods)
    {
        $subject = $this->findOrFail($id);
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();

        try {
            $user = \Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }

            foreach ($data as $field => $value) {
                $subject->$field = $value;
            }
            if ($subject->save()) {
                $this->editPoint($subject, $points);

                //添加考试项目——病例关系
                if(!$this->addSubjectCases($subject->id, $cases, $user->id, $id)){
                    throw new \Exception('编辑考试项目——病例关系失败');
                }
                //编辑考试项目——用物关系
                if(!$this->editSubjectGoods($subject->id, $goods, $user->id)){
                    throw new \Exception('编辑考试项目——用物关系失败');
                }


            } else {
                throw new \Exception('更新考核点信息失败');
            }
            $connection->commit();
            return $subject;
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }

    }

    /**
     * 新增考核点
     * @access public
     *
     * *
     * @param $subject
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * @param array $points
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @throws \Exception @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-03
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    protected function addPoint($subject, array $points)
    {
        $SubjectItemModel = new SubjectItem();
        try {
            foreach ($points as $point) {
                $SubjectItemModel->addItem($subject, $point);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 编辑考核点详情
     * @access protected
     *
     * * @param $subject
     * * @param array $points
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-03 18:37
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    protected function editPoint($subject, array $points)
    {
        $SubjectItemModel = new SubjectItem();
        try {
            $SubjectItemModel->delItemBySubject($subject);
            foreach ($points as $point) {
                $SubjectItemModel->addItem($subject, $point);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delSubject($subject)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();

        $SubjectItemModel = new SubjectItem();
        try {
            $SubjectItemModel->delItemBySubject($subject);
            if ($subject->delete()) {
                $connection->commit();
                return true;
            } else {
                throw new \Exception('删除失败');
            }
        } catch (\Exception $ex) {
            $connection->rollBack();
            if ($ex->getCode() == 23000) {
                throw new \Exception('该科目已经被使用了,不能删除');
            } else {
                throw $ex;
            }
        }
    }


    /**
     * @author Jiangzhiheng
     * @param $examId
     * @param $subjectId
     * @return
     */
    public function CourseControllerAvg($examId, $subjectId)
    {
        return ExamResult::leftJoin('station', 'exam_result.station_id', '=', 'station.id')
            ->leftJoin('subject', 'station.subject_id', '=', 'subject.id')
            ->leftJoin('exam_screening', 'exam_screening.id', '=', 'exam_result.exam_screening_id')
            ->leftJoin('exam', 'exam.id', '=', 'exam_screening.exam_id')
            ->where('exam.id', '=', $examId)
            ->where('exam.status', '<>', 0)
            ->where('subject.id', '=', $subjectId)
            ->select(
                'exam_result.score',
                'exam_result.time'
            )->get();
    }


    /** 获取考下的所有考试科目
     * @author zhouqing
     * @param $examId
     * @param $subjectId
     * @return
     */
    public function getSubjectList($examId)
    {
        $SubjectData = $this->leftJoin('station', 'station.subject_id', '=', 'subject.id')
            ->leftJoin('exam_station', 'exam_station.station_id', '=', 'station.id')
            ->where('exam_id', '=', $examId)
            ->select(
                'subject.title as subject_name',
                'subject.id as id'
            )->get();
        return $SubjectData;

    }

    /**
     * 添加考试项目——病例关系
     * @param $subject_id
     * @param $cases
     * @param $user_id
     * @author Zhoufuxiang 2016-3-31
     * @return bool
     */
    public function addSubjectCases($subject_id, $cases, $user_id, $id = ''){
        if($id == ''){
            foreach ($cases as $case_id) {
                $data = [
                    'subject_id'        => $subject_id,
                    'cases_id'          => $case_id,
                    'created_user_id'   => $user_id,
                ];
                if(!SubjectCases::create($data)){
                    return false;
                }
            }
        }else{
            // 存在$id 为编辑
            $result = SubjectCases::where('subject_id','=',$id)->get();
            $original = $result->pluck('cases_id')->toArray();

            $caseDels  = array_diff($original, $cases);     //多余的，删除
            $caseAdds  = array_diff($cases, $original);     //新添的，增加
            if(!empty($caseDels)){
                foreach ($caseDels as $caseDel) {
                    if(!SubjectCases::where('cases_id','=',$caseDel)->where('subject_id','=',$id)->delete()){
                        return false;
                    }
                }
            }
            if(!empty($caseAdds)){
                foreach ($caseAdds as $caseAdd) {
                    $data = [
                        'subject_id'        => $subject_id,
                        'cases_id'          => $caseAdd,
                        'created_user_id'   => $user_id,
                    ];
                    if(!SubjectCases::create($data)){
                        return false;
                    }
                }
            }

        }

        return true;
    }

    /**
     * 添加考试项目——用物关系
     * @param $subject_id
     * @param $goods
     * @param $user_id
     * @author Zhoufuxiang 2016-3-31
     * @return bool
     */
    public function addSubjectGoods($subject_id, $goods, $user_id){
        foreach ($goods as $good) {
            //查询是否有对应的用物
            $supplies = Supplies::where('name','=',$good['name'])->first();
            if($supplies){
                $supplies_id = $supplies->id;

            }else{

                //未查询到对应的用物，则创建
                $suppliesData = [
                    'name'           => $good['name'],
                    'create_user_id' => $user_id
                ];
                if(!$supplies = Supplies::create($suppliesData)){
                    return false;
                }
                $supplies_id = $supplies->id;
            }

            //添加考试项目——用物关系
            $data = [
                'subject_id'        => $subject_id,
                'supplies_id'       => $supplies_id,
                'num'               => $good['number'],
                'created_user_id'   => $user_id,
            ];
            if(!SubjectSupplies::create($data)){
                return false;
            }
        }

        return true;
    }


    /**
     * 编辑考试项目——用物关系
     * @param $subject_id
     * @param $goods
     * @param $user_id
     * @author Zhoufuxiang 2016-3-31
     * @return bool
     */
    public function editSubjectGoods($subject_id, $goods, $user_id){
        $result = SubjectSupplies::where('subject_id','=',$subject_id)->get();

        //删除原有的
        if(count($result)>0){
            foreach ($result as $item) {
                if(!$item->delete()){
                    return false;
                }
            }
        }
        //重新添加
        if(!empty($goods)){
            if(!$this->addSubjectGoods($subject_id, $goods, $user_id)){
                return false;
            }
        }

        return true;
    }

}