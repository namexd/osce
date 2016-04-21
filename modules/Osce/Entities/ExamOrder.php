<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/22 0022
 * Time: 13:05
 */
namespace Modules\Osce\Entities;
class ExamOrder extends  CommonModel{
    protected $connection = 'osce_mis';
    protected $table = 'exam_order';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['exam_id', 'exam_screening_id', 'student_id',  'begin_dt',  'status', 'created_user_id'];
    public $search = [];
    protected $statuValues = [
        0 => '未绑定',
        1 => '绑定腕表',
        2 => '已解绑',
        3 => '缺考',
        4 => '跳过',
    ];

    public function userInfo()
    {
        return $this->hasOne('\App\Entities\User', 'id', 'user_id');
    }
}