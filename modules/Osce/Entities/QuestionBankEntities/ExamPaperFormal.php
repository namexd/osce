<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016��3��9��11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use Modules\Osce\Entities\CommonModel;
class ExamPaperFormal extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_formal';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'exam_paper_id', 'length','name','total_score'];

    /**
     * ����ʽ��������Ĺ�ϵ
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author tangjun <tangjun@misrobot.com>
     * @date    2016��3��9��10:38:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function ExamCategoryFormal(){
        return $this->hasMany('Modules\Osce\Entities\QuestionBankEntities\ExamCategoryFormal','exam_paper_formal_id','id');
    }

}