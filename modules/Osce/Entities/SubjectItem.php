<?php
/**
 * 考核项目详情
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/12/31
 * Time: 15:10
 */

namespace Modules\Osce\Entities;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class SubjectItem extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'standard';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['subject_id', 'content', 'sort', 'score', 'pid', 'level', 'created_user_id', 'answer'];
    public $search = [];

    //创建人用户关联
    public function user(){
        return $this->hasOne('App\Entities\User','created_user_id','id');
    }

    //父级考核点
    public function parent(){
        return $this->hasOne('Modules\Osce\Entities\SubjectItem','id','pid');
    }

    /**
     * 新增 考核标准详情
     * @access public
     *
     * * @param $subject        考核标准（考核课题数据对象）
     * * @param array $point    考核标准详情
     * * @param string $parent  父级考核点
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-03 17:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addItem($subject,array $point,$parent=''){
        $user   =   Auth::user();
        if(array_key_exists('child',$point))
        {
            $data   =   [
                'subject_id'        =>  $subject->id,
                'content'           =>  $point['content'],
                'sort'             =>  $point['sort'],
                'score'             =>  $point['score'],
               // 'answer'             =>  $point['answer'],
                'created_user_id'   =>  $user->id,
                'pid'               =>  0,
                'level'             =>  1,
            ];
            $item    =   $this   ->  create($data);
            if(!$item)
            {
                throw new \Exception('新增考核点失败');
            }
            foreach($point['child'] as $children)
            {
                $this->addItem($subject,$children,$item);
            }
        }
        else
        {
            $level  =   $parent->level+1;
            $data   =   [
                'subject_id'        =>  $subject->id,
                'content'           =>  $point['content'],
                'sort'             =>  $point['sort'],
                'score'             =>  $point['score'],
                'answer'             =>  $point['answer'],
                'created_user_id'   =>  $user->id,
                'pid'               =>  $parent->id,
                'level'             =>  $level,
            ];
            $item    =   $this   ->  create($data);
            if(!$item)
            {
                throw new \Exception('新增考核项失败');
            }
        }
        return $item;
    }

    /**
     *
     * @access public
     *
     * *
     * @param array $content
     * @param array $content
     * * string        title        考核点名称
     * * string        *-*          考核项名称
     * * * @param array $score
     * <b>get 请求字段：</b>
     * * string        total        考核项名称
     * * string        *-*          考核项分数
     *
     * @param $answer
     * @return array

    @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-03 17:57
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    static public function builderItemData($content,$score,$answer){
        $data   =   [];

        foreach($content as $prointIndex => $item)
        {
            if(empty($item['title'])){
                throw new \Exception('考核点内容不能为空！');
            }else{
                $child  =   [];
                $itemScore  =   $score[$prointIndex];
//                dd($prointIndex, $answer, $score);
                $itemAnswer =   array_key_exists($prointIndex,$answer)? $answer[$prointIndex]:[];
                $itemScore  =   array_key_exists($prointIndex,$score)?   $score[$prointIndex]:[];
                foreach($item as $contentIndex  =>  $content){
                    if($contentIndex=='title'){
                        continue;
                    }
                    $contentData    =   [
                        'content'   =>  $content,
                        'score'     =>  array_key_exists($contentIndex,$itemScore)? $itemScore[$contentIndex]:0,
                        'sort'      =>  $contentIndex,
                        'answer'    =>  array_key_exists($contentIndex,$itemAnswer)? $itemAnswer[$contentIndex]:''
                    ];
                    $child[]=$contentData;
                }
                $item   =   [
                    'content'   =>  $item['title'],
                    'score'     =>  $itemScore['total'],
                    'sort'      =>  $prointIndex,
                    'child'     =>  $child
                ];
                $data[]=$item;
            }
        }

        return $data;
    }

    public function delItemBySubject($subject){
        $list   =   $this   ->  where('subject_id','=',$subject->id)->get();
        try{
            foreach($list as $item)
            {
                if(!$item->delete())
                {
                    throw new \Exception('清空旧的考核标准记录失败');
                }
            }
        }
        catch(\Exception $ex)
        {
            if(empty($item))
            {
                throw $ex;
            }
            if($ex->getCode()==23000)
            {
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
     * 根据考试标准获取其被调用的考试集合
     * @access public
     *
     * @param object $used 考核标准数据实例
     *
     * @return mixed
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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
}