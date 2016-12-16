<?php
/**
 * Created by PhpStorm.
 * User: fandian
 * Date: 2016/1/11 0011
 * Time: 10:03
 */
namespace Modules\Osce\Entities;
class InformTrain extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'inform_training';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected   $fillable 	=	[ 'name', 'address','begin_dt','end_dt','teacher','content','attachments','status','create_user_id','clicks'];
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
    // ��÷�ҳ�б�
    public function  getInformList(){

             $builder=$this->select([
                $this->table.'.id as id',
                $this->table.'.name as name',
                $this->table.'.address as address',
                $this->table.'.begin_dt as begin_dt',
                $this->table.'.end_dt as end_dt',
                $this->table.'.content as content',
                $this->table.'.attachments as attachments',
                $this->table.'.status as status',
            ])->orderBy($this->table.'.begin_dt','desc')->paginate(config('osce.page_size'));
            return $builder;
        }

}