<?php
/**
 * Created by PhpStorm.
 * @author tangjun <tangjun@misrobot.com>
 * @date 2016��3��9��11:02:12
 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
 */
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;
use DB;

/**��������ʱ����ʽ�Ծ�ģ��
 * Class Answer
 * @package Modules\Osce\Entities\QuestionBankEntities
 */
class Answer extends Model
{
    protected $connection = 'osce_mis';
    protected $table = 'exam_paper_formal';//��ʽ���Ծ��
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id', 'status', 'exam_paper_id','length','name','total_score','created_user_id','created_at','updated_at'];

    /**��ʽ�Ծ���Ϣ�б�
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getFormalPaper()
    {
        $DB = \DB::connection('osce_mis');
        $builder = $this;
        $builder = $builder->leftJoin('exam_category_formal', function ($join) { //��ʽ��������
            $join->on('exam_paper_formal.id', '=', 'exam_category_formal.exam_paper_formal_id');

        })->leftJoin('exam_question_type',function($join){ //��Ŀ���ͱ�
            $join->on('exam_category_formal.exam_question_type_id', '=', 'exam_question_type.id');

        })->leftJoin('exam_question_formal',function($join){ //��ʽ�������
            $join->on('exam_category_formal.id', '=', 'exam_question_formal.exam_category_formal_id');

        })->select([
            'exam_paper_formal.name',//�Ծ�����
            'exam_paper_formal.length',//����ʱ��
            'exam_paper_formal.total_score as totalScore',//�Ծ��ܷ�
            'exam_category_formal.exam_question_type_id as examQuestionTypeId',//��������id
            'exam_category_formal.name as typeName',//������������
            'exam_category_formal.score',//���������ֵ
            'exam_question_formal.name as questionName',//��������
            'exam_question_formal.content',//��������
            'exam_question_formal.answer',//�����
        ]);
        return $builder->get();
    }




























}