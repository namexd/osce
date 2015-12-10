<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 15:09
 */

namespace Modules\Msc\Entities;

use App\Entities\User;
use Illuminate\Database\Eloquent\Model;

class ResourcesLabHistory extends Model
{
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_openlab_history';
    protected $fillable 	=	['id', 'resources_lab_id', 'begin_datetime', 'end_datetime', 'group_id', 'teacher_uid', 'opertion_uid', 'resources_lab_device_id', 'result_poweroff', 'result_init', 'resources_openlab_apply_id'];
    public $search          =   ['description'];

    /**
     * 教师信息
     * @return \App\Entities\User
     */
    public function TeacherInfo()
    {
        return $this->belongsTo('App\Entities\User','teacher_uid');
    }

    // 获得微信端开放实验室使用历史记录列表
    public function getWechatList ($where=null)
    {
        $searchDate = empty($where['date']) ? null : $where['date'];

        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        })->leftJoin('resources_openlab_apply', function ($join){
            $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
        })->select([
            'resources_openlab_history.id as id', // 开放实验室使用历史编号
            'resources_lab.name as name', // 开放实验室名称
            'resources_openlab_history.begin_datetime as begin_datetime', // 开始时间
            'resources_openlab_history.end_datetime as end_datetime', // 结束时间
            'resources_openlab_apply.apply_uid as apply_uid', // 申请人id
        ])->where('resources_lab.opened', 1); // 预约整个实验室

        if(!empty($searchDate))
        {
            $builder = $builder->whereRaw(
                'unix_timestamp(resources_openlab_history.begin_datetime)>? and unix_timestamp(resources_openlab_history.begin_datetime)<=? ', // 默认所有的使用都是在一天完成
                [strtotime($searchDate), (strtotime($searchDate)+86400)]
            );
        }

        $pagination =  $builder->orderBy('resources_openlab_history.id')->paginate(config('msc.page_size',10));		

        if ($pagination) // 预约人可能是老师或者学生
        {
            foreach ($pagination as $key => $item)
            {            	    	
                $user = User::findOrFail($item->apply_uid);
                $pagination[$key]['user'] = $user->name;
            }
        }

        return $pagination;
    }

    // 获得微信端开放实验室使用历史记录详情
    public function getWechatItem ($id)
    {
        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        })->leftJoin('resources_openlab_apply', function ($join){
            $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
        })->select([
            'resources_openlab_history.id as id',
            'resources_lab.name as name',
            'resources_openlab_history.begin_datetime as begin_datetime',
            'resources_openlab_history.end_datetime as end_datetime',
            'resources_lab.code as code',
            'resources_openlab_history.teacher_uid as teacher_uid',
            'resources_openlab_apply.detail as detail',
            'resources_openlab_history.group_id as group_id',
            'resources_lab.location as location',
        ]);
        $labHis = $builder->where('resources_openlab_history.id', $id)->firstOrFail();

        // 查询参与学生名单
        $group = Groups::findOrFail($labHis->group_id);

        $data = [
            'name'            => $labHis->name,  // 开放实验室名称
            'beginTime'       => $labHis->begin_datetime, // 开始时间
            'endTime'         => $labHis->end_datetime, // 结束时间
            'code'            => $labHis->code, // 编码
            'location'        => $labHis->location, // 地址
            'teacher'         => is_null($labHis->TeacherInfo) ? '-' : $labHis->TeacherInfo->name, // 老师名字
            'detail'          => $labHis->detail, // 使用理由
            'students'        => $group->students, // 学生名单-数组
        ];

        return $data;
    }

    // 获得pc端开放实验室使用历史记录列表
    public function getPcList ($where, $order)
    {
        $searchDate = empty($where['date']) ? null : $where['date'];
        $keyword    = empty($where['keyword']) ? null : $where['keyword'];

        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        })->leftJoin('resources_openlab_apply', function ($join){
            $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
        })->select([
            'resources_openlab_history.id as id', // 开放实验室使用历史编号
            'resources_lab.name as name', // 开放实验室名称
            'resources_openlab_history.begin_datetime as begin_datetime', // 开始时间
            'resources_openlab_history.end_datetime as end_datetime', // 结束时间
            'resources_lab.code as code', // 编号
            'resources_openlab_apply.apply_uid as apply_uid', // 预约人id
            'resources_openlab_apply.detail as detail', // 预约理由
            'resources_openlab_history.result_poweroff as result_poweroff', // 是否按时关机
            'resources_openlab_history.result_init as result_init', // 教室复位状态自检
        ])->where('resources_lab.opened', 1); // 预约整个实验室

        // 是否进行了日期筛选
        if(!empty($searchDate))
        {
            $builder = $builder->whereRaw(
                'unix_timestamp(resources_openlab_history.begin_datetime)>? and unix_timestamp(resources_openlab_history.begin_datetime)<=? ', // 默认所有的使用都是在一天完成
                [strtotime($searchDate), (strtotime($searchDate)+86400)]
            );
        }

        // 是否有关键字筛选
        if(!empty($keyword))
        {
            $builder = $builder->where(function ($query) use($keyword) {
                $query->where('resources_lab.name', 'like', '%'.$keyword.'%') // 筛选实验室名字和预约理由 预约人暂时无法实现筛选 user表在另一个数据库 可能分布式部署
                ->orWhere('resources_openlab_apply.detail', 'like', '%'.$keyword.'%');
            });
        }

        $pagination = $builder->orderBy($order[0], $order[1])->paginate(config('msc.page_size',10));

        foreach ($pagination as $key => $item)
        {
            if ($item->apply_uid)
            {
                $user = User::findOrFail($item->apply_uid);
                $pagination[$key]['user'] = $user->name; // 预约人名字
            }
            else
            {
                $pagination[$key]['user'] = null;
            }
        }

        return $pagination;
    }

    // 获得pc端开放实验室使用历史记录详情
    public function getPcItem ($id)
    {
        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        })->leftJoin('resources_openlab_apply', function ($join){
            $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
        })->select([
            'resources_openlab_history.id as id',
            'resources_lab.name as name',
            'resources_openlab_history.begin_datetime as begin_datetime',
            'resources_openlab_history.end_datetime as end_datetime',
            'resources_lab.code as code',
            'resources_openlab_history.teacher_uid as teacher_uid',
            'resources_openlab_apply.detail as detail',
            'resources_openlab_history.result_poweroff as result_poweroff',
            'resources_openlab_history.result_init as result_init',
            'resources_openlab_history.group_id as group_id',
        ]);
        $labHis = $builder->where('resources_openlab_history.id', $id)->firstOrFail();

        // 查询参与学生名单
        $group = Groups::findOrFail($labHis->group_id);

        $data = [
            'name'            => $labHis->name,  // 开放实验室名称
            'beginTime'       => $labHis->begin_datetime, // 开始时间
            'endTime'         => $labHis->end_datetime, // 结束时间
            'code'            => $labHis->code, // 编码
            'teacher'         => is_null($labHis->TeacherInfo) ? '-' : $labHis->TeacherInfo->name, // 老师名字
            'detail'          => $labHis->detail, // 使用理由
            'result_init'     => $labHis->result_init, // 教室恢复状态自检
            'result_poweroff' => $labHis->result_poweroff, // 是否按时关机
            'students'        => $group->students, // 学生名单-数组
        ];

        return $data;
    }

    // 获得pc端开放实验室使用历史记录分析数据
    public function getPcAnalyze ($where)
    {
        $searchDate  = empty($where['date']) ? null : $where['date'];
        $grade       = empty($where['grade']) ? null : $where['grade'];
        $profession  = empty($where['profession']) ? null : $where['profession'];
        $result_init = empty($where['result_init']) ? null : $where['result_init'];

        // 筛选所有符合条件的开放实验室
        $builder = $this->leftJoin('resources_lab',function($join){
            $join->on('resources_lab.id','=','resources_openlab_history.resources_lab_id');
        });

        // 是否进行了日期筛选
        if($searchDate)
        {
            $builder = $builder->whereRaw(
                'unix_timestamp(resources_openlab_history.begin_datetime)>? and unix_timestamp(resources_openlab_history.begin_datetime)<=? ', // 默认所有的使用都是在一天完成
                [strtotime($searchDate), (strtotime($searchDate)+86400)]
            );
        }

        // 是否筛选了年级或专业
        if ($grade || $profession)
        {
            $builder = $builder->leftJoin('resources_openlab_apply',function($join){
                $join->on('resources_openlab_apply.id','=','resources_openlab_history.resources_openlab_apply_id');
            })->leftJoin('student',function($join){
                $join->on('student.id','=','resources_openlab_apply.apply_uid');
            });
        }

        // 是否筛选了年级
        if ($grade)
        {
            $builder = $builder->where('student.grade', $grade);
        }

        // 是否筛选了专业
        if ($profession)
        {
            $builder = $builder->where('student.professional', $profession);
        }

        // 是否筛选了复位状态
        if ($result_init)
        {
            $builder = $builder->where('resources_openlab_history.result_init', $result_init);
        }

        // 进行筛选
        $temp = $builder->select([
            'resources_lab.name as name',
        ])->get();
		
		$data = [];
		foreach ($temp as $item)
		{
			$data[] = $item->name;
		}
			

        return array_count_values(array_values($data));
    }

}