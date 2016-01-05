<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:50
 */

namespace Modules\Osce\Entities;


class Teacher extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'type', 'case_id', 'create_user_id'];
    private $excludeId = [];

    /**
     * SP老师的查询
     * @param $caseId
     * @param $spteacherId
     * @param $teacherType
     * @return mixed
     * @throws \Exception
     */
    public function showTeacherData($caseId, array $spteacherId, $teacherType)
    {
        try {
            //将传入的$spteacherId插进数组中
            if ($spteacherId !== null) {
                $this->excludeId = $spteacherId;
            }

            if ($caseId === null && $teacherType ===null) {
                throw new \Exception('系统发生了错误，请重试！');
            }

            $builder = $this->where('type' , '=' , $teacherType); //查询教师类型为指定类型的教师

            //如果$excludeId不为null，就说明需要排查这个id
            $excludeId = $this->excludeId;
            if (count($excludeId) !== 0) {
                $builder = $builder->whereNotIn('id', $excludeId);
            }

            return $builder->select([
                'id',
                'name'
            ])->get();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}