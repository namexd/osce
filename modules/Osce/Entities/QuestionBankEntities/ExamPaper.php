<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 13:56
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamPaper extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'name', 'status','mode','type','length','created_user_id'];

    /**
     * 与试题构造表的模型关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016年3月9日10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamPaperStructure(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamPaperStructure','exam_paper_id','id');
    }
    /**
     * 获取试卷列表
     * @access    public
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamPaperlist($keyword){
        $builder = $this;
        if(!empty($keyword)){
            $builder = $builder->where('name','like','%'.$keyword.'%');
        }

        $builder = $builder->leftjoin('exam_paper_structure',function($join){
                $join->on('exam_paper_structure.exam_paper_id','=','exam_paper.id');
            })->select('exam_paper.name','exam_paper.type','exam_paper_structure.num','exam_paper_structure.total_score')
            ->orderBy('exam_paper.id','desc')->paginate(config('osce.page_size'));
        //dd($builder);
        return $builder;
    }



    /**
     * 查找与试卷相关的数据
     * @access    public
     * @param Exam $exam
     * @return view
     * @throws \Exception
     * @version   1.0
     * @author    weihuiguo <weihuiguo@misrobot.com>
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamDatas($exam_id){
        $builder = $this->where('exam_paper.id','=',$exam_id);

        $builder = $builder->leftjoin('exam_paper_structure',function($join){
            $join->on('exam_paper_structure.exam_paper_id','=','exam_paper.id');
        })->select('exam_paper.name','exam_paper.type','exam_paper_structure.num','exam_paper_structure.total_score')
            ->orderBy('exam_paper.id','desc')->paginate(config('msc.page_size'));

        return $builder;
    }
}