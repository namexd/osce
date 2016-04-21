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
use Auth;
use Modules\Osce\Repositories\Common;

class Subject extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'subject';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'title', 'mins', 'score', 'sort', 'status', 'description',
        'goods', 'stem', 'equipments', 'created_user_id', 'archived'
    ];
    public $search = [];

    public function user()
    {
        return $this->hasOne('App\Entities\User', 'created_user_id', 'id');
    }

    public function standards()
    {
        return $this->belongsToMany('Modules\Osce\Entities\Standard', 'subject_standard', 'subject_id', 'standard_id', 'id');
    }

    public function cases()
    {
        return $this->belongsToMany('Modules\Osce\Entities\CaseModel','subject_cases','subject_id','case_id','id');
    }
    public function supplys()
    {
        return $this->belongsToMany('Modules\Osce\Entities\Supply','subject_supply','subject_id','supply_id','id');
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
    public function addSubject($data, $points, $cases, $goods, $user_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();

        try {
            if ($subject = $this->create($data)) {          //创建考试项目

                //TODO:Zhoufuxiang 2016-4-12
                if (!$this->addStandard($subject, $points)){
                    throw new \Exception('保存评分标准失败');
                }

                //添加考试项目——病例关系
                if(!$this->addSubjectCases($subject->id, $cases, $user_id)){
                    throw new \Exception('创建考试项目——病例关系失败');
                }
                //添加考试项目——用物关系
                if(!empty($goods)){
                    if(!$this->addSubjectGoods($subject->id, $goods, $user_id)){
                        throw new \Exception('创建考试项目——用物关系失败');
                    }
                }

            } else {
                throw new \Exception('新增考核标准失败');
            }
            $connection->commit();
//            $a=$this->where('id','=',$subject->id)->with('cases')->with('supplys')->with(['standards'=>function($q){
//                $q->with('standardItem');
//            }])->first();
//            dd($a);
            return $subject;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 添加评分标准
     * @access public
     *
     * @param array $subject
     * @param array $points
     * @return object
     * @throws \Exception @version 3.4
     *
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-04-12 18:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function addStandard($subject, $points){

        $subjectStandard = new SubjectStandard();
        //1、创建评分标准；2、创建考试项目与评分标准间的关系  （3、返回对应的考核标准信息）
        $standard = $subjectStandard->getStandard($subject, $subject->title);
        //添加考试项目对应的考核内容
        $this->addPoint($standard, $points);
        return $standard;
    }

    /**
     * 修改评分标准
     * @access public
     *
     * @param array $subject
     * @param array $points
     * @return object
     * @throws \Exception @version 3.4
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     * @date 2016-04-12 19:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function editStandard($subject, $points){

        $subjectStandard = new SubjectStandard();
        //获取对应的 评分标准
        $standard = $subjectStandard->getStandard($subject, $subject->title);
        //修改考试项目对应的考核内容
        $this->editPoint($standard, $points);
        return $standard;
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
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            //修改考试项目对应的基本信息
            foreach ($data as $field => $value) {
                $subject->$field = $value;
            }
            if ($subject->save()) {

                //修改评分标准     TODO:Zhoufuxiang 2016-4-12
                if (!$this->editStandard($subject, $points)){
                    throw new \Exception('保存评分标准失败');
                }

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
    protected function addPoint($standard, array $points)
    {
        $StandardItem = new StandardItem();
        try {
            $rePoints =   [];
            foreach ($points as $point) {
                $rePoints[]   =   $StandardItem->addItem($standard, $point);
            }
            return collect($rePoints);
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
    protected function editPoint($standard, array $points)
    {
        $standardItem = new StandardItem();
        try {

            $standardItem->delItemBySubject($standard);

            foreach ($points as $point) {
                $standardItem->addItem($standard, $point);
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除考试项目
     *
     * @param $subject
     *
     * @author Zhoufuxiang  2016-04-13 10:55
     * @return bool
     * @throws \Exception
     */
    public function delSubject($subject)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();

        try {
            $TeacherSubject = new TeacherSubject();

            //获取当前正在考试的考试对应的所有老师考试项目关系数据
            if(!$TeacherSubject->getTeacherSubjects()->isEmpty()){

                throw new \Exception('支持该考试项目的老师已被安排考试');

            }else{
                //删除考试项目、老师的关联关系数据
                $TeacherSubject->delTeacherSubjects($subject);
            }

            //删除与考试项目相关联的关系数据
            Common::delRelation($subject, 'cases',     '删除与病例的关联失败', -600);
            Common::delRelation($subject, 'supplys',   '删除与用物的关联失败', -601);
            Common::delRelation($subject, 'standards', '删除与评分标准的关联失败', -602);
            //删除考试项目对应的评分标准
            $Standard = new Standard();
            $Standard ->delStandard($subject);

            //删除考试项目
            if (!$subject->delete()) {
                throw new \Exception('删除考试项目失败');
            }

            $connection->commit();
            return true;

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
     * 删除考试项目 对应的 评分标准
     *
     * @param $subject
     *
     * @author Zhoufuxiang  2016-04-13 10:55
     * @return bool
     * @throws \Exception
     */
    public function delStandard($subject)
    {
        if(!$subject->standards->isEmpty())
        {
            $standardItem = new StandardItem();
            foreach ($subject->standards as $standard)
            {
                $standardItem->delItemBySubject($standard);
            }
        }
        return true;
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
                    'case_id'          => $case_id,
                ];
                $result = SubjectCases::insert($data);
                if(!$result){
                    return false;
                }
            }
        }else{
            // 存在$id 为编辑
            $result = SubjectCases::where('subject_id','=',$id)->get();
            $original = $result->pluck('case_id')->toArray();

            $caseDels  = array_diff($original, $cases);     //多余的，删除
            $caseAdds  = array_diff($cases, $original);     //新添的，增加
            if(!empty($caseDels)){
                foreach ($caseDels as $caseDel) {
                    if(!SubjectCases::where('case_id','=',$caseDel)->where('subject_id','=',$id)->delete()){
                        return false;
                    }
                }
            }
            if(!empty($caseAdds)){
                foreach ($caseAdds as $caseAdd) {
                    $data = [
                        'subject_id'        => $subject_id,
                        'case_id'          => $caseAdd,
                    ];
                    $result = SubjectCases::insert($data);
                    if(!$result){
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
            $supply = Supply::where('name','=',$good['name'])->first();
            if($supply){
                $supply_id = $supply->id;

            }else{

                //未查询到对应的用物，则创建
                $supplyData = [
                    'name'           => $good['name'],
                    'create_user_id' => $user_id
                ];
                if(!$supply = Supply::create($supplyData)){
                    return false;
                }
                $supply_id = $supply->id;
            }

            //添加考试项目——用物关系
            $data = [
                'subject_id'        => $subject_id,
                'supply_id'         => $supply_id,
                'num'               => $good['number'],
            ];
            if(!SubjectSupply::create($data)){
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
        $result = SubjectSupply::where('subject_id','=',$subject_id)->get();

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