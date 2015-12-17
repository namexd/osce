<?php
    /**
     * Created by PhpStorm.
     * User: 梧桐雨间的枫叶
     * Date: 2015/11/29
     * Time: 18:29
     */

    namespace Modules\Msc\Entities;


    use Modules\Msc\Entities\CommonModel;
    use DB;

class ResourcesClassroomApply extends  CommonModel {
    protected $connection	=	'msc_mis';
    protected $table 		= 	'resources_lab_apply';
    public $timestamps	=	true;
    protected $primaryKey	=	'id';
    public $incrementing	=	true;
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];

    //包含查询条件的数据库对象（唐俊）
    // public $builder         =   'id,resources_lab_id,detail,status';

        /**
         * 申请的教室
         */
        public function classroom () {
            return $this->belongsTo ('Modules\Msc\Entities\ResourcesClassroom', 'resources_lab_id', 'id');
        }

        /**
         * 申请人
         */
        public function applyer () {
            return $this->belongsTo ('App\Entities\User', 'apply_uid', 'id');
        }

        //根据条件查询 相关课程信息列表（唐俊）
        public function getClassroomApplyList ($orderBy = 'id', $pageNum = 20) {
            return $this->builder->with ('classroom', 'applyer')->orderBy ($orderBy)->paginate ($pageNum);
        }

        //根据条件查询 信息（唐俊）
        public function getClassroomApplyInfo () {
            return $this->builder->with ('classroom', 'applyer')->get ()->first ();
        }

        public function labApplyGroups () {
            return $this->hasMany ('Modules\Msc\Entities\ResourcesClassroomApplyGroup', 'resources_lab_apply_id', 'id');
            //            return $this->hasManyThrough('Modules\Msc\Entities\Groups','Modules\Msc\Entities\ResourcesClassroomApplyGroup','student_group_id','id');
        }

    /**
     * 根据教室名和申请的使用日期筛选未审核申请
     * @param $courseName
     * @param $date
     * @param $order
     * @return
     */
    public function getWaitExamineList ($courseName, $date, $order) {
        $connection =   DB::connection('msc_mis');
        $connection ->enableQueryLog();

        $builder = $this->leftJoin (
            'resources_lab',
            function ($join) {
                $join->on ('resources_lab_apply.resources_lab_id', '=', 'resources_lab.id');
            }
        )->leftJoin (
            'student',
            function ($join) {
                $join->on ('resources_lab_apply.apply_uid', '=', 'student.id');
            }
        )->leftJoin (
            'teacher',
            function ($join) {
                $join->on ('resources_lab_apply.apply_uid', '=', 'teacher.id');
            }
        )->where ('resources_lab_apply.status', '=', '0');
        $builder->whereRaw ('unix_timestamp(resources_lab_apply.original_begin_datetime)>= ? ', [strtotime ($date)]);
        if ($courseName) {
            $builder = $builder->where ('resources_lab.name', 'like', '%'.$courseName.'%');
        }

        $builder->select (
            [
                'resources_lab.name as name',
                'resources_lab_apply.original_begin_datetime as original_begin_datetime',
                'resources_lab_apply.original_end_datetime as original_end_datetime',
                'resources_lab.code as code',
                'student.name as student_name',
                'teacher.name as teacher_name',
                'resources_lab_apply.detail as detail',
                'resources_lab_apply.status as status',
                'resources_lab_apply.id as id',
                'resources_lab_apply.apply_uid as apply_uid',
                'resources_lab_apply.apply_user_type as apply_user_type',
            ]
        );
//        dd($order[0][1]);
        $builder->orderBy ($order[0][0], $order[1])->orderBy($order[0][1],$order[1])->paginate (config ('msc.page_size'));
        $c=$connection ->getQueryLog();
        dd($c);
        return $builder->orderBy ($order[0][0], $order[1])->orderBy($order[0][1],$order[1])->paginate (config ('msc.page_size'));
    }


    /**
     * 已审核申请列表
     * @param string $courseName
     * @param string $date
     * @param array $order
     * @return mixed
     */
    public function getExaminedList ($courseName, $date, $order) {
        $builder = $this->leftJoin (
            'resources_lab',
            function ($join) {
                $join->on ('resources_lab_apply.resources_lab_id', '=', 'resources_lab.id');
            }
        )->leftJoin (
            'student',
            function ($join) {
                $join->on ('resources_lab_apply.apply_uid', '=', 'student.id');
            }
        )->leftJoin (
            'teacher',
            function ($join) {
                $join->on ('resources_lab_apply.apply_uid', '=', 'teacher.id');
            }
        )->where ('resources_lab_apply.status', '<>', '0');
        if ($courseName) {
            $builder = $builder->where ('resources_lab.name', 'like', '%'.$courseName.'%');
        }
        if ($date) {
            $builder->whereRaw ('unix_timestamp(resources_lab_apply.original_begin_datetime)>= ? ', [strtotime ($date)]);
        }
        $builder->select (
            [
                'resources_lab.name as name',
                'resources_lab_apply.original_begin_datetime as original_begin_datetime',
                'resources_lab_apply.original_end_datetime as original_end_datetime',
                'resources_lab_apply.original_end_datetime as original_end_datetime',
                'resources_lab.code as code',
                'student.name as student_name',
                'teacher.name as teacher_name' ,
                'resources_lab_apply.detail as detail',
                'resources_lab.status as status',
                'resources_lab_apply.id as id',
                'resources_lab_apply.apply_uid as apply_uid',
                'resources_lab_apply.apply_user_type as apply_user_type'
            ]
        );

        if($order[0]=='created_at')
        {
            $order[0]   =   $this->table.'.created_at';
        }

        return $builder->orderBy ($order[0][0], $order[1])->orderBy($order[0][1],$order[1])->paginate (config ('msc.page_size'));

    }

        //审核通过或拒绝一个申请
        public function dealApply ($id, $status, $desc, $type) {
            $connection = DB::connection ('msc_mis');
            $connection->beginTransaction ();
            try {
                $apply = $this->find ($id);
                $apply->status = $status;
                if ($status == 2) {
                    $apply->reject = $desc;
                } else {
                    $newPlanData = [
                        'course_id' => 0,
                        'resources_lab_id' => $apply->resources_lab_id,
                        'begintime' => date ('H:i', strtotime ($apply->begin_datetime)),
                        'endtime' => date ('H:i', strtotime ($apply->end_datetime)),
                        'currentdate' => date ('Y-m-d', strtotime ($apply->begin_datetime)),
                        'type' => $type,
                        'groups' => $apply->labApplyGroups
                    ];
                    $this->createApplyPlan ($newPlanData);
                }
                $result = $apply->save ();
                if ($result) {
                    $connection->commit ();
                } else {
                    throw new \Exception('状态变更失败');
                }
                return $id;
            } catch (\Exception $ex) {
                $connection->rollback ();
                throw $ex;
            }
        }

        //新建一个课程计划
        public function createApplyPlan ($newPlanData) {
            try {
                $ResourcesClassroomPlanModel = new ResourcesClassroomPlan();
                $ResourcesClassroomPlanModel->createApplyPlan ($newPlanData);
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        public function getStatusAttribute ($value) {
            switch ($value) {
                //0=待审核 1=已通过 2=不通过
                case 0:
                    $name = '不可预约';
                    break;
                case 1:
                    $name = '正常';
                    break;
                case 2:
                    $name = '已预约';
                    break;
                default:
                    $name = '-';
            }
            return $name;
        }

        public function cancelOldPlan ($id, $notice) {
            $connection = DB::connection ('msc_mis');

            $connection->beginTransaction ();
            try {
                $resourcesClassroomPlanModel = new ResourcesClassroomPlan();
                $apply = ResourcesClassroomApply::find ($id);

                $teacherList = $resourcesClassroomPlanModel->cancelOldPlan (
                    $apply->resources_lab_id,
                    $apply->original_begin_datetime,
                    $apply->original_end_datetime
                );
                $teacherData = [];
                foreach ($teacherList as $teacher) {
                    $id = $teacher->userInfo->openid;
                    if ($id) {
                        $this->sendMsg ($notice, $id);
                    }
                }
                $connection->commit ();
                return true;
            } catch (\Exception $ex) {
                $connection->rollback ();
                throw $ex;
            }
        }



        //像微信用户发送普通文本消息
        private function sendMsg ($msg, $openid) {
            if (empty($openid)) {
                throw new \Exception('没有找到用户的微信OpenID');
            }
            $userService = new \Overtrue\Wechat\Staff(config ('wechat.app_id'), config ('wechat.secret'));
            return $userService->send ($msg)->to ($openid);
        }

        public function refund ($id, $reject) {
            $apply = $this->find ($id);
            if ($apply) {
                $apply->status = 2;
                $apply->reject = $reject;
                if ($apply->save ()) {
                    return true;
                } else {
                    throw new \Exception('拒绝失败');
                }
            } else {
                throw new \Exception('没有找到该申请');
            }
        }

    

    
    public function dealOpenLabToolsApply($id,$status,$desc,$type)
    {
        $connection = DB::connection('msc_mis');
        $connection->beginTransaction();
        try {
            $apply = $this->find($id);
            $apply->status = $status;
            if ($status == 2) {
                $apply->reject = $desc;
            } else {
                $newPlanData = [
                    'course_id' => 0,
                    'resources_lab_id' => $apply->resources_lab_id,
                    'begintime' => date('H:i', strtotime($apply->begin_datetime)),
                    'endtime' => date('H:i', strtotime($apply->end_datetime)),
                    'currentdate' => date('Y-m-d', strtotime($apply->begin_datetime)),
                    'type' => $type,
                    'groups' => $apply->groups
                ];
                //$this->createApplyPlan($newPlanData);
            }

            $result = $apply->save();
            if ($result) {
                $connection->commit();
            } else {
                throw new \Exception('状态变更失败');
            }
            return $id;
        } catch (\Exception $ex) {
            $connection->rollback();
            throw $ex;
        }
    }


   
}

