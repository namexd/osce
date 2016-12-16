<?php
/**
 * 考核项目
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Modules\Osce\Repositories\Common;
use Modules\Osce\Entities\StandardItem as SItem;

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
        'goods', 'stem', 'equipments', 'created_user_id', 'archived', 'rate_choose','rate_score'
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
    public function specialScores()
    {
        return $this->hasMany('Modules\Osce\Entities\SubjectSpecialScore','subject_id', 'id');
    }
    public function supplys()
    {
        return $this->belongsToMany('Modules\Osce\Entities\Supply','subject_supply','subject_id','supply_id','id');
    }

    public function specials(){
        return $this->hasMany('Modules\Osce\Entities\SubjectSpecialScore','subject_id','id');
    }
    /**
     * 获取课题列表（考核点的盒子的列表）
     * @access public
     *
     * @return pagination
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-02 21:58
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-02 22:08
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function addSubject($data, $points, $cases, $speScore, $goods, $user_id)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();

        try {
            $subject = $this->create($data);          //创建考试项目
            if ($subject)
            {
                //TODO:fandian 2016-4-12
                if (!$this->addStandard($subject, $points))
                {
                    throw new \Exception('保存评分标准失败');
                }

                //添加考试项目——病例关系
                if(!$this->addSubjectCases($subject->id, $cases, $user_id)){
                    throw new \Exception('创建考试项目——病例关系失败');
                }
                $SubjectSpecial = new SubjectSpecialScore();
                //添加考试项目——特殊评分项关系 fandian 2016-07-01
                if(!$this->handleSubjectSpecialScore($subject->id, $speScore, $user_id)){
                    throw new \Exception('创建考试项目——特殊评分项关系失败');
                }
                //添加考试项目——用物关系
                if(!empty($goods)){
                    if(!$this->addSubjectGoods($subject->id, $goods, $user_id)){
                        throw new \Exception('创建考试项目——用物关系失败');
                    }
                }

            }else
            {
                throw new \Exception('新增考核标准失败');
            }

            $connection->commit();
            return $subject;

        } catch (\Exception $ex)
        {
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
     * @author fandian <fandian@sulida.com>
     * @date 2016-04-12 18:43
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     * @author fandian <fandian@sulida.com>
     * @date 2016-04-12 19:43
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function editStandard($subject, $points)
    {
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
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-03 18:43
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function editTopic($id, $data, $points, $cases, $speScore, $goods)
    {
        $subject = $this->findOrFail($id);
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();

        try {
            $user = Auth::user();
            if(empty($user)){
                throw new \Exception('未找到当前操作人信息');
            }
            //修改考试项目对应的基本信息
            foreach ($data as $field => $value) {
                $subject->$field = $value;
            }
            if ($subject->save())
            {
                //修改评分标准     TODO:fandian 2016-4-12
                if (!$this->editStandard($subject, $points)){
                    throw new \Exception('保存评分标准失败');
                }

                //添加考试项目——病例关系
                if(!$this->addSubjectCases($subject->id, $cases, $user->id, $id)){
                    throw new \Exception('编辑考试项目——病例关系失败');
                }
                $SubjectSpecial = new SubjectSpecialScore();
                //添加考试项目——特殊评分项关系 TODO:fandian 2016-05-07、07-01
                if(!$this->handleSubjectSpecialScore($subject->id, $speScore, $user->id)){
                    throw new \Exception('编辑考试项目——特殊评分项关系失败');
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
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-03
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    protected function addPoint($standard, array $points)
    {
        $StandardItem = new StandardItem();
        try {
            $rePoints =   [];
            foreach ($points as $point) {
                $rePoints[]   =   $this->addItem($standard, $point);
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
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2016-01-03 18:37
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    protected function editPoint($standard, array $points)
    {
        $StandardItem = new StandardItem();
        try {
            if(array_key_exists('id', $points[0]))
            {
                $this->handlePoints($standard, $points);
            }else
            {
                $StandardItem->delItemBySubject($standard);

                foreach ($points as $point)
                {
                    $this->addItem($standard, $point, '');
                }
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
     * @author fandian  2016-04-13 10:55
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
            if(!$TeacherSubject->getTeacherSubjects($subject)->isEmpty())
            {
                throw new \Exception('支持该考试项目的老师已被安排考试');

            }else{
                //删除考试项目、老师的关联关系数据
                $TeacherSubject->delTeacherSubjects($subject);
            }

            //删除与考试项目相关联的关系数据
            Common::delRelation($subject, 'cases',     '删除与病例的关联失败', -600);
            Common::delRelation($subject, 'supplys',   '删除与用物的关联失败', -601);
            Common::delRelation($subject, 'standards', '删除与评分标准的关联失败', -602);

            //Common::delRelation($subject, 'standards', '删除与特殊评分项的关联失败', -602);
            //删除考试项目对应的评分标准
            $Standard = new Standard();
            $Standard ->delStandard($subject);
            if(count($subject->specials))
            {
                if(!$subject->specials()->delete())
                {
                    throw new \Exception('删除关联特殊评分项失败');
                }
            }

            //删除考试项目
            if (!$subject->delete()) {
                throw new \Exception('删除考试项目失败',-603);
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {

            $connection->rollBack();
            \Log::debug('删除科目',[$ex]);
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
     * @author fandian  2016-04-13 10:55
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
     * @author ZouYuChao
     * @param $examScreeningId
     * @param $subjectId
     * @param $paperId
     * @return
     */
    public function CourseControllerAvg($examScreeningId, $subjectId,$paperId)
    {
        //todo:GaoDapeng 2016/06/16 增加理论试卷分数的显示
        //todo:GaoDapeng 2016/06/22 使用examScreeningId代替examId来获取所有考试包含子考试信息
        //todo:gaodapeng 2016/06/30 将所有使用station.subject_id查询替换为exam_draft.subject_id,否则查询显示出错
         $info=ExamResult::leftJoin('exam_score','exam_result.id','=','exam_score.exam_result_id')
            ->leftJoin('exam_screening','exam_screening.id','=','exam_result.exam_screening_id')
            ->leftJoin('station', 'exam_result.station_id', '=', 'station.id')
            ->leftJoin('exam_paper', 'station.paper_id', '=', 'exam_paper.id')
            ->leftJoin('subject', 'exam_score.subject_id', '=', 'subject.id')
            ->whereIn('exam_result.exam_screening_id',$examScreeningId)
            ->groupBy('exam_result.student_id');
            if($subjectId!=""){
                $info->where('subject.id', '=', $subjectId);
            }
            if($paperId!=""){
                $info->where('exam_paper.id', '=', $paperId);
            }
           $info=$info->where('exam_result.flag','=',0)//将无效成绩剔除
                    ->select(
                    'exam_result.score',
                    'exam_result.time'
                    )->get();
        return $info;
    }


    /** 获取考下的所有考试科目
     * @author zhouqiang
     * @param $examId
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
     * @author fandian 2016-3-31
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
     * 添加考试项目——特殊评分项关系
     * @param  $subject_id
     * @param  $speScores
     * @param  $user_id
     * @return bool
     *
     * @author fandian <fandian@sulida.com>
     * @data   2016-05-7
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function addSubjectSpecialScore($subject_id, $speScores, $user_id)
    {
        //查询原有的考试项目——特殊评分项（存在则全删除）
        $subjectSpeScores = SubjectSpecialScore::where('subject_id', '=', $subject_id)->get();
        if(!$subjectSpeScores->isEmpty()){
            foreach ($subjectSpeScores as $subjectSpeScore) {
                $subjectSpeScore->delete();
            }
        }
        //再重新添加新的考试项目——特殊评分项
        if(!empty($speScores)){
            foreach ($speScores as $speScore)
            {
                //添加考试项目——特殊评分项关系
                $data = [
                    'subject_id'        => $subject_id,
                    'title'             => $speScore['title'],
                    'score'             => $speScore['score'],
                    'rate'              => $speScore['rate'],
                    'created_user_id'   => $user_id
                ];
                if(!SubjectSpecialScore::create($data)){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 添加考试项目——特殊评分项
     * @param $subject_id
     * @param $speScores
     * @param $user_id
     * @return bool
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @data   2016-07-01  16:15
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function handleSubjectSpecialScore($subject_id, $speScores, $user_id)
    {
        $ids = [];
        //添加、更新 考试项目——特殊评分项
        if(!empty($speScores)){
            foreach ($speScores as $speScore)
            {
                //存在ID，则更新特殊评分项信息
                if(array_key_exists('id', $speScore))
                {
                    $ids[] =  $speScore['id'];
                    $result = SubjectSpecialScore::where('id', '=', $speScore['id'])->update($speScore);
                    if(!$result){
                        throw new \Exception('更新特殊评分项信息失败');
                    }
                }else       //否则添加新的特殊评分项
                {
                    $data = [
                        'subject_id'        => $subject_id,
                        'title'             => $speScore['title'],
                        'score'             => $speScore['score'],
                        'rate'              => $speScore['rate'],
                        'created_user_id'   => $user_id
                    ];
                    \Log::alert('新增特殊评分项', [$speScore, $data]);
                    $result = SubjectSpecialScore::create($data);
                    if(!$result){
                        throw new \Exception('添加新的特殊评分项失败');
                    }
                    $ids[] =  $result->id;
                }
            }
        }

        //根据ID 数组删除多余的特殊评分项
        $subjectSpeScores = SubjectSpecialScore::whereNotIn('id', $ids)->where('subject_id', '=', $subject_id)->get();
        if(!$subjectSpeScores->isEmpty()){
            foreach ($subjectSpeScores as $subjectSpeScore) {
                $subjectSpeScore->delete();
            }
        }

        return true;
    }

    /**
     * 添加考试项目——用物关系
     * @param $subject_id
     * @param $goods
     * @param $user_id
     * @author fandian 2016-3-31
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
     * @author fandian 2016-3-31
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


    /**
     * 新增 考核标准详情
     * @access public
     *
     * @param $subject        考核标准（考核课题数据对象）
     * @param array $point    考核标准详情
     * @param string $parent  父级考核点
     *
     * @return object
     *
     * @version 3.4
     * @author fandian <fandian@sulida.com>
     * @date 2016-04-12 12:55  2016-06-27 18:35
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function addItem($standard, array $point, $parent = '')
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user   = Auth::user();
            //存在child，为考核点
            if(array_key_exists('child', $point))
            {
                $data   =   [
                    'standard_id'       => $standard->id,
                    'content'           => $point['content'],
                    'sort'              => $point['sort'],
                    'score'             => $point['score'],
                    'created_user_id'   => $user->id,
                    'pid'               => 0,
                    'level'             => 1,
                    'coefficient'       => $point['coefficient']
                ];
                $item    = StandardItem::create($data);
                if(!$item){
                    throw new \Exception('新增考核点失败');
                }
                foreach($point['child'] as $children)
                {
                    $this->addItem($standard, $children, $item);
                }

            } else      //不存在child，为考核项、评分标准
            {
                $level  = $parent->level+1;
                $data   = [
                    'standard_id'       => $standard->id,
                    'content'           => $point['content'],
                    'sort'              => $point['sort'],
                    'score'             => $point['score'],
                    'answer'            => $point['answer'],
                    'created_user_id'   => $user->id,
                    'pid'               => $parent->id,
                    'level'             => $level,
                ];
                $item   = StandardItem::create($data);
                if(!$item)
                {
                    throw new \Exception('新增考核项失败');
                }
            }
            $connection->commit();
            return $item;

        } catch (\Exception $ex){
            $connection->rollBack();
            throw $ex;
        }
    }


    /**
     * 处理考核点、考核项信息
     * @param $standard
     * @param $points
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-27 15:43
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function handlePoints($standard, $points)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user   = Auth::user();
            $ids    = [];
            foreach ($points as &$point)
            {
                //存在ID,属于考核点更新
                if(array_key_exists('id', $point))
                {
                    $ids[] = $point['id'];
                    //查询考核点信息
                    $standard_item = StandardItem::where('id', '=', $point['id'])->first();
                    //处理考核项 详细信息
                    $id_arr = $this->handleTerm($standard, $standard_item, $point['child'], $user);
                    $ids = array_merge($ids, $id_arr);
                    unset($id_arr);
                    //去掉考核项信息，只保留考核点详细信息
                    unset($point['child']);
                    //更新考核点信息
                    if(!StandardItem::where('id', '=', $point['id'])->update($point)){
                        throw new \Exception('更新考核点失败');
                    }
                }else       //不存在ID,属于考核点新增
                {
                    //新增考核点
                    $data   =   [
                        'standard_id'       => $standard->id,
                        'content'           => $point['content'],
                        'sort'              => $point['sort'],
                        'score'             => $point['score'],
                        'created_user_id'   => $user->id,
                        'pid'               => 0,
                        'level'             => 1,
                        'coefficient'       =>  $point['coefficient']
                    ];
                    $standard_item = StandardItem::create($data);
                    if(!$standard_item){
                        throw new \Exception('新增考核点失败');
                    }
                    $ids[]  = $standard_item->id;    //将ID存入id数组中（用于删除时剔除）
                    //处理考核项 详细信息
                    $id_arr = $this->handleTerm($standard, $standard_item, $point['child'], $user);
                    $ids    = array_merge($ids, $id_arr);

                }
            }
            //处理被删除了的考核点、考核项
            if(!empty($ids)){
                StandardItem::whereNotIn('id', $ids)->where('standard_id', '=', $standard->id)->delete();
            }
            $connection->commit();
            return true;

        }catch (\Exception $ex)
        {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 处理考核项信息
     * @param $standard
     * @param $standard_item
     * @param $terms
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-27 16:33
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function handleTerm($standard, $standard_item, $terms, $user)
    {
        $ids = [];
        foreach ($terms as $index => $term)
        {
            //存在ID，属于更新考核项
            if(array_key_exists('id', $term))
            {
                $ids[]  = $term['id'];
                $result = StandardItem::where('id', '=', $term['id'])->update($term);
                if(!$result){
                    throw new \Exception('更新考核项失败');
                }

            }else       //不存在ID，属于新增考核项
            {
                $level  = $standard_item->level+1;
                $data   = [
                    'standard_id'       => $standard->id,
                    'content'           => $term['content'],
                    'sort'              => $term['sort'],
                    'score'             => $term['score'],
                    'answer'            => $term['answer'],
                    'created_user_id'   => $user->id,
                    'pid'               => $standard_item->id,
                    'level'             => $level,
                ];
                $item   = StandardItem::create($data);
                if(!$item)
                {
                    throw new \Exception('新增考核项失败');
                }
                $ids[] = $item->id;
            }
        }
        return $ids;
    }


}