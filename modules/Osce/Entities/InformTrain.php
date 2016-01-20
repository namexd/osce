<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/11 0011
 * Time: 10:03
 */
namespace Modules\Osce\Entities;
class InformTrain extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'inform_training';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'name', 'address','begin_dt','end_dt','end_dt','content','attachments','status','create_user_id'];
    public      $search     =   [];

    public function getPaginate(){
        return $this->paginate(config('msc.page_size'));
    }

    public function getAuthor(){
        return $this->belongsTo('App\Entities\User','create_user_id','id');
    }

    public function getExam(){
        return $this->belongsTo('App\Osce\Entities\Exam','exam_id','id');
    }
    // 获得分页列表
    public function  getInformList(){

            $builder=$this;
            return $builder->select([
                $builder.'.id as id',
                $builder.'.name as name',
                $builder.'.address as address',
                $builder.'.begin_dt as begin_dt',
                $builder.'.end_dt as end_dt',
                $builder.'.content as content',
                $builder.'.attachments as attachments',
                $builder.'.status as status',
            ])->get()->orderBy($builder.'.id')->paginate(config('msc.page_size',10));
        }

}