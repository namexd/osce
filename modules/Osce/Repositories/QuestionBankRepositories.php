<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016-03-08 11:43
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */

namespace Modules\Osce\Repositories;
use Auth;
use Modules\Osce\Entities\ExamStation;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Repositories\BaseRepository;
use Modules\Osce\Entities\QuestionBankEntities\ExamPaper;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestion;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabel;
use Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelRelation;
use Modules\Osce\Entities\Exam;
use Cache;
class QuestionBankRepositories  extends BaseRepository
{

    /**
     * @method
     * @url /osce/
     * @access public
     * @param ExamPaper $ExamPaper
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日12:11:59
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamPaperPreview($data){
        $ExamQuestion  = new ExamQuestion;
        $data = [
            'ExamPaperName'=>'测试试卷',
            'ExamPaperLength' => 50,
            'mode'=>1,
            'type'=>2,
            'child'=>[
                [
                    'type'=>1,
                    'num'=>5,
                    'score'=>5,
                    'total_score'=>5*5,
                    'exam_question'=>[8,9,10,11]
                ],
                [
                    'type'=>1,
                    'num'=>5,
                    'score'=>5,
                    'total_score'=>5*5,
                    'exam_question'=>[8,9,10,11]
                ]
            ]
        ];
        if($data['mode'] == 1){

        }elseif($data['mode'] == 2){

        }
        return  $data;
    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @param $str
     * @return array|bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日11:05:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function StrToArr($str){
        $NewArr = [];
        $ExamQuestionLabel = new ExamQuestionLabel;
        if(!empty($str)){
            $arr = explode('@',$str);
            if(!empty($arr[1])&&!empty($arr[2])&&!empty($arr[3])){
                $AdditionalInfo = explode(',',$arr[1]);
                $NewArr['type'] = $AdditionalInfo['0'];
                $NewArr['num'] = $AdditionalInfo['1'];
                $NewArr['score'] = $AdditionalInfo['2'];
                $NewArr['total_score'] = $AdditionalInfo['1']*$AdditionalInfo['2'];
                if(@$arr['4']){
                    $NewArr['structureid'] = $arr['4'];
                }
                //dd();
                //dd($NewArr);
                $lableType = explode(',',$arr[2]);
                $lableArr = explode(',',$arr[3]);
                $ExamQuestionLabelData = $ExamQuestionLabel->whereIn('id',$lableArr)->get();
                $data = [];
                foreach($ExamQuestionLabelData as $k => $v){
                    if(!empty($v->ExamQuestionLabelType['id'])){
                        foreach($lableType as $key => $val){
                            $arr = explode('-',$val);
                            if($arr[0] == $v->ExamQuestionLabelType['id']){
                                $data[$k]['label_type_id'] = $v->ExamQuestionLabelType['id'];
                                $data[$k]['exam_question_label_id'] = $v['id'];
                                $data[$k]['relation'] = $arr[1];
                            }
                        }
                    }
                }
                $NewArr['structure_label'] = $data;
            }else{
                return  false;
            }
        }
        return  $NewArr;
    }

    /**
     * @method
     * @url /osce/
     * @access public
     * @param $str
     * @return array|bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日11:05:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ArrToStr($data){
        $ExamQuestionLabel = new ExamQuestionLabel;
        //dd($request->all());
        $ExamQuestionLabelData = $ExamQuestionLabel->whereIn('id',$data['tag'])->get();
        $idArr = [];
        $LabelNameStr = '';
        foreach($ExamQuestionLabelData as $k => $v){
            if($v->ExamQuestionLabelType){
                if(!in_array($v->ExamQuestionLabelType['id'],$idArr)){
                    $idArr[] = $v->ExamQuestionLabelType['id'];
                    if(!empty($LabelNameStr)){
                        $LabelNameStr .= ','.$v['name'];
                    }else{
                        $LabelNameStr .= $v['name'];
                    }
                }
            }
        }

        $LabelTypeStr = '';
        foreach($data as $key => $val){
            if(preg_match('/^label-{1,3}/',$key)){
                $arr = explode('-',$key);
                if(!empty($LabelTypeStr)){
                    $LabelTypeStr .= ','.$arr[1].'-'.$val;
                }else{
                    $LabelTypeStr .= $arr[1].'-'.$val;
                }
            }
        }
        $data = [
            '0'=>$LabelNameStr,
            '1'=>implode(',',
                [
                    0=>empty($data['question-type'])?0:$data['question-type'],
                    1=>empty($data['questionNumber'])?0:$data['questionNumber'],
                    2=>empty($data['questionScore'])?0:$data['questionScore']
                ]
            ),
            '2'=>$LabelTypeStr,
            '3'=>$ExamQuestionLabelStr = implode(',',$data['tag'])
        ];
        return implode('@',$data);
    }
    /**
     * @method
     * @url /osce/
     * @access public
     * @param $structureArr
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日18:33:54
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function StructureExamQuestionArr($structureArr)
    {
        $structureArr = $this->HandlePaperPreviewArr($structureArr);
        $ExamQuestionLabelRelation = new ExamQuestionLabelRelation;
        $ExamQuestion = new ExamQuestion;
        $arr = [];
        if (!empty($structureArr)) {
            foreach ($structureArr as $key => $val) {
                //\DB::connection('osce_mis')->enableQueryLog();
                //建立一个查询包含关系的对象
                $orBuilder = $ExamQuestionLabelRelation->leftJoin('exam_question', function ($join) {
                    $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');
                })
                    ->groupBy('exam_question.id')
                    ->select(
                        'exam_question.id as id'
                    );
                //建立一个查询等于关系的对象
                $andBuilder = $ExamQuestion->leftJoin('exam_question_label_relation',function($join){
                    $join->on('exam_question.id', '=', 'exam_question_label_relation.exam_question_id');
                })
                    ->groupBy('exam_question.id')
                    ->select(
                        'exam_question.id as id'
                    );
                //存放包含关系的标签 id分组
                $orIdArr = [];
                //存放等于关系的标签 id分组
                $andIdArr = [];
                if (!empty($val['child'])) {
                    foreach ($val['child'] as $k => $v) {
                        $labelIdArr = collect($v)->pluck('exam_question_label_id');
                        //（1.包含，2.等于）
                        if ($v['0']['relation'] == 1) {
                           $orBuilder->orWhere(function ($query) use ($labelIdArr,$val) {
                                foreach ($labelIdArr as $item) {
                                    $query->orWhere(function ($query) use ($item,$val) {
                                        $query
                                            ->where('exam_question_label_id', '=', $item)
                                            ->where('exam_question.exam_question_type_id', '=', $val['question_type']);
                                    });
                                }
                            });
                            $orIdArr[] = $labelIdArr;

                        } elseif ($v['0']['relation'] == 2) {
                            $andBuilder->orWhere(function ($query) use ($labelIdArr,$val) {
                                $query->whereIn('exam_question_label_id', $labelIdArr)
                                    ->where('exam_question.exam_question_type_id', '=', $val['question_type']);
                            });
                            $andIdArr[] = $labelIdArr;
                        }
                    }
                    $orQuestionList = [];

                    $andQuestionList = [];


                    //如果有相关包含关系的标签id 表示需要查询
                    if(count($orIdArr)>0){
                        $orQuestionList = $orBuilder->get();
                    }

                    $orQuestionId = [];

                    //计算同时满足多个条件的数据
                    if(count($orQuestionList)>0){
                        $orQuestionIdArr = $orQuestionList->pluck('id');
                        $orExamQuestionList = $ExamQuestion->whereIn('id',$orQuestionIdArr)->with('ExamQuestionLabelRelation')->get();
                        foreach($orExamQuestionList as $k => $v){
                            $flag = false;
                            if(count($v->ExamQuestionLabelRelation) > 0){
                                $labelId = $v->ExamQuestionLabelRelation->pluck('exam_question_label_id');
                                foreach($orIdArr as $key => $vel){
                                    if(!$this->IsContainTwo($vel,$labelId->toArray())){
                                        $flag = false;
                                        break;
                                    }
                                    $flag = true;
                                }
                            }
                            if($flag){
                                $orQuestionId[] = $v['id'];
                            }
                        }
                    }


                    //$sql = \DB::connection('osce_mis')->getQueryLog();
                    //dd($orQuestionId);

                    //如果有相关等于关系的标签id 表示需要查询
                    if(count($andIdArr)>0){
                        $andQuestionList = $andBuilder->get();
                    }

                    $andQuestionId = [];

                    //计算同时满足多个条件的数据
                    if(count($andQuestionList)>0){
                        $andQuestionIdArr = $andQuestionList->pluck('id');
                        $andExamQuestionList = $ExamQuestion->whereIn('id',$andQuestionIdArr)->with('ExamQuestionLabelRelation')->get();
                        foreach($andExamQuestionList as $k => $v){
                            $flag = false;
                            if(count($v->ExamQuestionLabelRelation) > 0){
                                $labelId = $v->ExamQuestionLabelRelation->pluck('exam_question_label_id');
                                foreach($andIdArr as $key => $vel){
                                    if(!$this->IsContain($vel,$labelId->toArray())){
                                        $flag = false;
                                        break;
                                    }
                                    $flag = true;
                                }
                            }
                            if($flag){
                                $andQuestionId[] = $v['id'];
                            }
                        }
                    }

                    //$q = \DB::connection('osce_mis')->getQueryLog();

                    //合并包含 与 等于关系 查询出来的 试题id
                    if(count($orIdArr)>0 && count($andIdArr)>0){
                        $QuestionId = array_intersect($orQuestionId,$andQuestionId);
                    }else{
                        $QuestionId = array_merge($orQuestionId,$andQuestionId);
                    }

                    $questionIdArr = [];
                    if(count($QuestionId)>0){
                        $questionIdArr = $this->RandQuestionId($QuestionId,$val['question_num']);
                    }
                    
                    $arr[$key]['type'] = $val['question_type'];
                    $arr[$key]['num'] = $val['question_num'];
                    $arr[$key]['score'] = $val['question_score'];
                    $arr[$key]['total_score'] = $val['question_total_score'];
                    $arr[$key]['child'] = $questionIdArr;
                }
            }
        }
        return  $arr;
    }

    /**
     * 处理构造试题的数组
     * @method
     * @url /osce/
     * @access public
     * @param $PaperPreviewArrItem
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月11日18:32:08
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function HandlePaperPreviewArr($PaperPreviewArrItem){
        $data = [];
        if(!empty($PaperPreviewArrItem)){
            foreach($PaperPreviewArrItem as $k => $v){
                if(!empty($v['structure_label'])){
                    foreach($v['structure_label'] as $key => $val){
                        $data[$k]['child'][$val['label_type_id']][] = $val;
                    }
                }
                $data[$k]['question_type'] = !empty($v['type'])?$v['type']:$v['exam_question_type_id'];
                $data[$k]['question_num'] = $v['num'];
                $data[$k]['question_score'] = $v['score'];
                $data[$k]['question_total_score'] = $v['total_score'];
                $data[$k]['id'] = @$v['id'];
            }
        }
        return $data;
    }

    /**
     * 随机取出制定数量的试题id
     * @method
     * @url /osce/
     * @access public
     * @param $questionList
     * @param $questionNum
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date   2016年3月11日18:29:14
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function RandQuestionId($questionList,$questionNum){
        $length = count($questionList);
        $key = [];
        $questionIdArr = [];
        if($length<$questionNum){
            return  $questionList;
        }else{
            while (count($key)<$questionNum)
            {
                $k = rand(0,$length-1);
                if(in_array($k,$key)){
                    continue;
                }else{
                    $key[] = $k;
                }
            }
            foreach($key as $k => $v){
                $questionIdArr[] = $questionList[$v];
            }
            return  collect($questionIdArr);
        }
    }

    /**
     * 生成试卷
     * @method
     * @url /osce/
     * @access public
     * @param $ExamPaperId
     * @return array
     * @author tangjun <tangjun@misrobot.com>
     * @date   2016年3月14日14:27:03
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GenerateExamPaper($ExamPaperId,$Mark=0){
        $ExamPaper = new ExamPaper;
        $ExamPaperInfo = $ExamPaper->where('id','=',$ExamPaperId)->first();

        if(count($ExamPaperInfo)>0){
            //随机试卷处理方法
            if($ExamPaperInfo->type == 1){
                if(count($ExamPaperInfo->ExamPaperStructure)>0){
                    foreach($ExamPaperInfo->ExamPaperStructure as $k => $v){
                        if(count($v->ExamPaperStructureLabel)){
                            $ExamPaperInfo->ExamPaperStructure[$k]['structure_label'] = $v->ExamPaperStructureLabel;
                        }
                    }
                }
                $ExamPaperInfo['item'] = ($this->StructureExamQuestionArr($ExamPaperInfo->ExamPaperStructure));
                //统一试卷处理方法
            }elseif($ExamPaperInfo->type == 2){
                $item = [];
                if($Mark && $ExamPaperInfo->mode == 1){

                    if(count($ExamPaperInfo->ExamPaperStructure)>0){
                        foreach($ExamPaperInfo->ExamPaperStructure as $k => $v){
                            if(count($v->ExamPaperStructureLabel)){
                                $ExamPaperInfo->ExamPaperStructure[$k]['structure_label'] = $v->ExamPaperStructureLabel;
                            }
                        }
                    }
                    $ExamPaperInfo['item'] = ($this->StructureExamQuestionArr($ExamPaperInfo->ExamPaperStructure));

                }else{
                    if(count($ExamPaperInfo->ExamPaperStructure)>0){
                        foreach($ExamPaperInfo->ExamPaperStructure as $k => $v){
                            $arr = [];
                            if(count($v->ExamPaperStructureQuestion)){
                                //dd($v->ExamPaperStructureQuestion);
                                $arr['id'] = $v['id'];
                                $arr['type'] = $v['exam_question_type_id'];
                                $arr['num'] = $v['num'];
                                $arr['score'] = $v['score'];
                                $arr['total_score'] = $v['total_score'];
                                $arr['child'] = $v->ExamPaperStructureQuestion->pluck('exam_question_id');
                            }
                            if(count($arr)>0){
                                $item[] = $arr;
                            }
                        }
                    }
                    $ExamPaperInfo['item'] = $item;
                }

            }
        }
        return   $ExamPaperInfo;
    }

    /**
     * 检验用户是否是监考老师
     * @method
     * @url /osce/
     * @access public
     * @return bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月16日10:03:13
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function LoginAuth(){
        $user = Auth::user();
        if(count($user->roles)>0){
            $roles = $user
                ->roles
                ->pluck('id')
                ->toArray();
        } else {
            $roles = [];
        }
        //监考老师 目前的角色id为1
        if(in_array(config('osce.invigilatorRoleId'), $roles)){
            return  $user->id;
        }else{
            return  false;
        }
    }

    /**
     * 根据监考老师id获取相关信息
     * @method
     * @url /osce/
     * @access public
     * @return $this
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月17日10:05:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetExamInfo($userId){
        $Exam = new Exam;
        //获取本次考试的id
        $ExamInfo = $Exam->where('status','=',1)->select('id')->first();
        if(empty($ExamInfo->id)){
            throw new \Exception(' 没有在进行的考试');
        }
        //根据监考老师的id和考试id，获取对应的考站id
        try{
            $Exam = new Exam;
            //获取本次考试的id
            $ExamInfo = $Exam->where('status','=',1)->select('id')->first();
            if(empty($ExamInfo->id)){
                throw new \Exception(' 没有在进行的考试');
            }
            //根据监考老师的id和考试id，获取对应的考站id
            $builder = $Exam->leftJoin('station_teacher', function($join){
                $join -> on('station_teacher.exam_id', '=', 'exam.id');
            })->groupBy('station_teacher.user_id')
                ->where('exam.id','=',$ExamInfo->id)
                ->where('station_teacher.user_id','=',$userId->id)
                ->select('station_teacher.station_id');
            $station_id = $builder->pluck('station_id');
            if(empty($station_id)){
                throw new \Exception('你没有相关需要监考的考站');
            }
            return  ['StationId'=>$station_id,'ExamId'=>$ExamInfo->id];
        }catch (\Exception $ex){
            return $ex->getMessage();
        }

    }

    /**根据考试id和考站id查询对应的考试信息
     * @method
     * @url /osce/
     * @access public
     * @param $ExamInfo
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamData($ExamInfo){
        $stationTeacher = new StationTeacher();
        $data = $stationTeacher->leftJoin('exam', function ($join) { //考试
            $join->on('station_teacher.exam_id', '=', 'exam.id');
        })->leftJoin('station', function ($join) { //考站
            $join->on('station_teacher.station_id', '=', 'station.id');

        })->where('exam.id','=',$ExamInfo['ExamId'])->where('station.id','=',$ExamInfo['StationId'])
           ->select([
            'exam.name',//考试名称
            'station.mins',//标准考试时间(分钟)'
        ])->first();
        return $data;
    }

    /**
     * 判断 $array 是否包含 $arr
     * @method
     * @url /osce/
     * @access public
     * @param $arr
     * @param $array
     * @return bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月23日10:23:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function IsContain($arr,$array){
        foreach($arr as $v){
            if(!in_array($v,$array)){
                return  false;
            }
        }
        return  true;
    }

    /**
     * 判断 $array 是否包含 $arr 其中一个元素
     * @method
     * @url /osce/
     * @access public
     * @param $arr
     * @param $array
     * @return bool
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月23日10:23:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function IsContainTwo($arr,$array){
        foreach($arr as $v){
            if(in_array($v,$array)){
                return  true;
            }
        }
        return  false;
    }

    /**
     * 获取试题标签id
     * @url       GET /osce/admin/exampaper/examp-questions
     * @access    public
     * @param Request $request get请求<br><br>
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function GetExamQuestionLabelId($obj){
        $IdArr = [];
        if(count($obj)>0){

            foreach($obj as $k => $v){
                $IdArr = array_merge($IdArr,collect($v)->pluck('exam_question_label_id')->toArray());
            }

            return  $IdArr;
        }else{
            return  $IdArr;
        }

    }



}