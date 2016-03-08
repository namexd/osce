<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 15:03
 */

namespace Modules\Osce\Entities\QuestionBankEntities;

use Illuminate\Database\Eloquent\Model;

use Modules\Osce\Entities\QuestionBankEntities\LabelType;

class ExamQuestionLabel extends  Model
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_question_label';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['label_type_id', 'name','describe','status'];

    //标签类型
    public function ExamQuestionLabelType(){
        return $this->hasOne('Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType','id','label_type_id');
    }

    /**
     * 标签类型和标签关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function LabelTypeAndLabel()
    {
        return $this    ->  hasMany('\Modules\Osce\Entities\QuestionBankEntities\ExamQuestionLabelType','id','label_type_id');
    }


    /**
     * 获取标签页
     * @method  GET
     * @url
     * @access public
     * @param
     * @author yangshaolin <yangshaolin@misrobot.com>
     * @date    2016年3月7日18:11:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFilteredPaginateList ($where)
    {
        $builder = $this;

        if ($where['keyword'])
        {
            $builder = $builder->where('name','like','%'.$where['keyword'].'%');
        }
        if ($where['id'])
        {
            $builder = $builder->where('label_type_id','=',$where['id']);
        }
        return $builder->orderBy('id')->paginate(config('msc.page_size',10));
    }
}