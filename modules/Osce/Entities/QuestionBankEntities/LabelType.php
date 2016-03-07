<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7 0007
 * Time: 15:06
 */

namespace Modules\Osce\Entities\QuestionBankEntities;

use Illuminate\Database\Eloquent\Model;
class LabelType extends  Model
{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'label_type';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    protected $fillable 	=	[ 'name','status'];

}