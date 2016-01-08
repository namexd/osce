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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function station()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\station','station_sp','user_id','station_id');
    }

    /**
     * SP老师的查询
     * @param $caseId
     * @param $spteacherId
     * @param $teacherType
     * @return mixed
     * @throws \Exception
     */
    public function showTeacherData($station_id, array $spteacherId)
    {
        try {
            //将传入的$spteacherId插进数组中
            if ($spteacherId !== null) {
                $this->excludeId = $spteacherId;
            }

            if ($station_id === null) {
                throw new \Exception('系统发生了错误，请重试！');
            }

            //通过传入的$station_id得到病例id
            $case_id = StationCase::where('station_case.station_id', '=', $station_id)
                ->select('case_id')->first()->case_id;

            $builder = $this->where('type' , '=' , 2); //查询教师类型为指定类型的教师
            $builder = $builder->where('case_id' , '=' , $case_id); //查询符合病例的教师

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