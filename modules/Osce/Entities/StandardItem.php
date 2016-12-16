<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/12 0012
 * Time: 12:48
 */

namespace Modules\Osce\Entities;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class StandardItem extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'standard_item';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['standard_id', 'content', 'sort', 'score', 'pid', 'level', 'created_user_id', 'answer', 'coefficient'];
    public    $search       = [];


    //获取考核项
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\StandardItem','id','pid');
    }

    public function childrens(){
        return $this->hasMany('Modules\Osce\Entities\StandardItem','pid','id');
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
     * @date 2016-04-12 12:55
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function addItem($standard, array $point, $parent = '')
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $user   =   Auth::user();

            if(array_key_exists('child', $point))
            {
                $data   =   [
                    'standard_id'       =>  $standard->id,
                    'content'           =>  $point['content'],
                    'sort'              =>  $point['sort'],
                    'score'             =>  $point['score'],
                    'created_user_id'   =>  $user->id,
                    'pid'               =>  0,
                    'level'             =>  1,
                    'coefficient'       =>  $point['coefficient']
                ];
                $item    =   $this->create($data);
                if(!$item){
                    throw new \Exception('新增考核点失败');
                }
                foreach($point['child'] as $children)
                {
                    $this->addItem($standard, $children, $item);
                }

            } else{
                $level  =   $parent->level+1;
                $data   =   [
                    'standard_id'       =>  $standard->id,
                    'content'           =>  $point['content'],
                    'sort'              =>  $point['sort'],
                    'score'             =>  $point['score'],
                    'answer'            =>  $point['answer'],
                    'created_user_id'   =>  $user->id,
                    'pid'               =>  $parent->id,
                    'level'             =>  $level,
                    'coefficient'       =>  $point['coefficient']
                ];
                $item   =   $this->create($data);
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
     * 查询考核点的平均分
     * @author fandian <fandian@sulida.com>
     */
    public function getCheckPointAvg($pid, $subjectId)
    {
        $builder = $this-> leftJoin('exam_score', function($join){
                $join -> on('standard_item.id', '=', 'exam_score.standard_item_id');
            })
            -> leftJoin('exam_result', function($join){
                $join -> on('exam_result.id', '=', 'exam_score.exam_result_id');
            });
        $builder = $builder ->where('standard_item.pid', $pid)
                    ->where('exam_score.subject_id', $subjectId)
                    ->groupBy('exam_score.exam_result_id');

        $builder = $builder->select(\DB::raw(implode(',', ["SUM(exam_score.score) as total_score"])))
                    ->get();
        $avg = 0;
        if(count($builder)){
            $totalScore = 0;
            foreach ($builder as $item) {
                $totalScore += $item->total_score;
            }
            $avg = round($totalScore/count($builder),1);
        }

        return $avg;
    }

    /**
     * 删除考试项目 对应的 评分标准
     * @access public
     * @param $subject_id
     * @return mixed
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-04-13 14:13
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getSubjectStandards($subject_id)
    {
        try{

            $standards = [];
            $standardStandards = SubjectStandard::where('subject_id','=',$subject_id)->get();
            if (count($standardStandards)>0){
                //获取评分标准下的所有考核点、考核项
                foreach ($standardStandards as $standard) {
                    //

                    $result    = $this->ItmeList($standard->standard_id,$subject_id);
                    $standards = array_merge($standards, $result);
                }
            }
            return $standards;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param $standard_id
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>  fandian
     * @return array
     */



    public function ItmeList($standard_id,$subject_id){

        try{

            $prointList =   $this->where('standard_id','=',$standard_id)->get();
            if($prointList->isEmpty()){
                throw  new \Exception('获取考试项目失败，请检查考试项目考核点和考核项');
            }
            $data       =   [];
            foreach($prointList as $item)
            {

                $item->subject_id=$subject_id;
                $data[$item->pid][] =   $item;
               // $data[$item->pid][]['subject_id'] =   $subject_id;

            }

            $return =   [];

            foreach($data[0] as $proint)
            {
                $prointData =   $proint;

                $prointData['subject_id'] =   $subject_id;
                if(array_key_exists($proint->id,$data))
                {
                    $prointData['test_term']    =   $data[$proint->id];
                    //$prointData['test_term']['subject_id']    =   $subject_id;
                } else{
                    $prointData['test_term']    =   [];
                }

                $return[] = $prointData;
                foreach($return as $proint){
                    foreach($proint['test_term'] as $str){
                        $str['real']= '0' ;
                        $str['scoreTime']= 0;
                    }
                }
            }

            return $return;

        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 处理 评分标准 数据
     *
     * @param Collection $itemCollect
     *
     * @author fandian  2016-04-13 14:13
     * @return bool
     * @throws \Exception
     */
    static public function builderItemTable(Collection $itemCollect){
        $list   =   [];
        $child   =   [];
        foreach($itemCollect as $item)
        {
            if($item->pid   ==  0)
            {
                $list[] =    $item;
            }
            else
            {
                $child[$item->pid][]=$item;
            }
        }

        $data   =   [];
        foreach($list as $item)
        {
            $data[]     =   $item;

            $childItem  =   array_key_exists($item->id,$child)? $child[$item->id]:[];

            $indexArray =   array_pluck($childItem,'sort');
            array_multisort($indexArray, SORT_ASC, $childItem);

            foreach($childItem as $chilren)
            {
                $data[]     =   $chilren;
            }
        }
        return $data;
    }

    /**
     * 删除对应的以前的评分标准
     * @param $standard
     * @throws \Exception
     */
    public function delItemBySubject($standard)
    {
        $list   =   $this   ->  where('standard_id', '=', $standard->id)->get();
        try{
            //删除对应的以前的评分标准
            foreach($list as $item)
            {
                if(!$item->delete())
                {
                    throw new \Exception('清空旧的考核标准记录失败');
                }
            }

        } catch(\Exception $ex){
            if(empty($item)){
                throw $ex;
            }

            //处理报外键错误
            if($ex->getCode()==23000){
                $usedList   =   ExamScore::where('subject_id','=',$item->id)->get();
                $examList   =   [];
                foreach($usedList as $used)
                {
                    $exam   =   $this->getSubjectUsedInfoByExamScore($used);
                    if(empty($exam))
                    {
                        continue;
                    }
                    $examList[] =   $exam->name;
                }
                throw new \Exception(implode(',',$examList).'已经使用了此标准，不能修改');
            }
        }
    }

    /**
     * 根据考试标准获取其被调用的考试集合
     * @access public
     *
     * @param object $used 考核标准数据实例
     *
     * @return mixed
     *
     * @version 1.0
     * @author Zouyuchao <Zouyuchao@sulida.com>
     * @date 2015-12-29 17:09
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */
    public function getSubjectUsedInfoByExamScore($used){
        $examResult =   $used->examResult;
        if(is_null($examResult))
        {
            return '';
        }
        else
        {
            $examScreening  =   $examResult->examScreening;
            if(is_null($examScreening))
            {
                throw new \Exception('没有找到成绩对应的考试场次，请联系管理员');
            }
            $exam   =   $examScreening  ->  ExamInfo;
            return $exam;
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
        $ids = [];
        foreach ($points as &$point)
        {
            $ids[] = $point['id'];
            //查询考核点信息
            $standard_item = $this->where('id', '=', $point['id'])->first();
            //处理考核项 详细信息
            $id_arr = $this->handleTerm($standard, $standard_item, $point['child']);
            $ids = array_merge($ids, $id_arr);
            unset($id_arr);
            //去掉考核项信息，只保留考核点详细信息
            unset($point['child']);
            //更新考核点信息
            if(!$this->where('id', '=', $point['id'])->update($point)){
                throw new \Exception('更新考核点失败');
            }
        }
        //处理被删除了的考核点、考核项
        if(!empty($ids)){
            $this->whereNotIn('id', $ids)->delete();
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
    public function handleTerm($standard, $standard_item, $terms)
    {
        $ids = [];
        foreach ($terms as $index => $term)
        {
            if(array_key_exists('id', $term))
            {
                $ids[]  = $term['id'];
                $result = $this->where('id', '=', $term['id'])->update($term);
                if(!$result){
                    throw new \Exception('更新考核项失败');
                }

            }else
            {
                $user   =   Auth::user();
                $level  =   $standard_item->level+1;
                $data   =   [
                    'standard_id'       =>  $standard->id,
                    'content'           =>  $terms['content'],
                    'sort'              =>  $terms['sort'],
                    'score'             =>  $terms['score'],
                    'answer'            =>  $terms['answer'],
                    'created_user_id'   =>  $user->id,
                    'pid'               =>  $standard_item->id,
                    'level'             =>  $level,
                    'coefficient'       =>  $term['coefficient']
                ];
                $item   =   $this->create($data);
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