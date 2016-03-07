<?php
namespace Modules\Osce\Entities\QuestionBankEntities;
use Illuminate\Database\Eloquent\Model;


/**标签类型模型
 * Class LabelType
 * @package Modules\Osce\Entities\QuestionBankEntities
 */

class ExamQuestionLabelType extends  Model

{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'exam_question_label_type';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	['id', 'name','status'];
    /**鑾峰彇鏍囩绫诲瀷鍒楄〃
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function labelTypeList(){
        $data = $this->select('id','name')->orderBy('created_at','desc')->get();
        return $data;
    }

    /**鑾峰彇鏍囩绫诲瀷鍜屾爣绛剧殑鐩稿叧鏁版嵁
     * @method
     * @url /osce/
     * @access public
     * @author xumin <xumin@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getLabAndType(){
        $builder = $this;

    }
}


