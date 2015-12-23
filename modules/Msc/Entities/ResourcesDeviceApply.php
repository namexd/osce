<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2015/12/7 0007
     * Time: 16:41
     */

    namespace Modules\Msc\Entities;

    use Modules\Msc\Entities\CommonModel;
    use DB;

    class ResourcesDeviceApply extends CommonModel {


        protected $connection = 'msc_mis';
        protected $table = 'resources_device_apply';
        public $timestamps = true;
        protected $primaryKey = 'id';
        public $incrementing = true;
        protected $guarded = [ ];
        protected $hidden = [ ];


        public function applyer () {
            return $this->belongsTo('App\Entities\User','apply_uid','id');
        }

        public function history () {
            return $this->hasOne('App\Entities\ResourcesDeviceHistory','id','resources_device_apply_id');
        }

        /**
         * 设备未审核列表
         * @param $keyword
         * @param $date
         * @param $order
         */
        public function getWaitToolsList ($keyword , $date , $order) {
            //连接查询3张表,得到自己想要的结果
            $builder = $this->leftJoin (
                'resources_device' ,
                function ($join) {
                    $join->on ('resources_device_apply.resources_device_id' , '=' , 'resources_device.id');
                }
            )->leftJoin (
                'student' ,
                function ($join) {
                    $join->on ('resources_device_apply.apply_uid' , '=' , 'student.id');
                }
            )->leftJoin (
                'resources_device_history' , function ($join) {
                $join->on ('resources_device_history.resources_device_apply_id' , '=' , 'resources_device_apply.id');
            }
            )->where ('resources_device_apply.status' , '=' , '0');

            $builder->select (
                [
                    'resources_device_apply.id as id' ,
                    'resources_device.name as name' ,
                    'resources_device_apply.original_begin_datetime as original_begin_datetime' ,
                    'resources_device_apply.original_end_datetime as original_end_datetime' ,
                    'resources_device.code as code' ,
                    'student.name as student_name' ,
                    'resources_device_apply.detail as detail' ,
                    'resources_device_history.result_health as health' ,
                    'resources_device_apply.apply_uid as apply_id' ,
                ]
            );
            //根据日期查询,默认是当前日期,如果有传参,就是传参之后的日期
            $builder = $builder->whereRaw ('unix_timestamp(resources_device_apply.original_begin_datetime)>= ? ' , [ strtotime ($date) ]);
            //设置关键字查询
            if ($keyword !== '') {
                $builder = $builder->where ('resources_device.name' , 'like' , '%'.$keyword.'%');
            }

            return $builder->orderBy ($order[0] , $order[1])->paginate (config ('msc.page_size'));

        }

        /**
         * 设备已审核列表
         * @param $keyword
         * @param $date
         * @param $order
         */
        public function getToolsExaminedList ($keyword , $date , $order) {
            //连接查询3张表,得到自己想要的结果
            $builder = $this->leftJoin (
                'resources_device' ,
                function ($join) {
                    $join->on ('resources_device_apply.resources_device_id' , '=' , 'resources_device.id');
                }
            )->leftJoin (
                'student' ,
                function ($join) {
                    $join->on ('resources_device_apply.apply_uid' , '=' , 'student.id');
                }
            )->where ('resources_device_apply.status' , '<>' , '0');

            $builder->select (
                [
                    'resources_device_apply.id as id' ,
                    'resources_device.name as name' ,
                    'resources_device_apply.original_begin_datetime as original_begin_datetime' ,
                    'resources_device_apply.original_end_datetime as original_end_datetime' ,
                    'resources_device.code as code' ,
                    'student.name as student_name' ,
                    'resources_device_apply.detail as detail' ,
                    'resources_device.status as status' ,
                    'resources_device_apply.apply_uid as apply_id' ,
                ]
            );
            //如果有传入参数，就搜索传入参数之后的
            $builder = $builder->whereRaw ('unix_timestamp(resources_device_apply.original_begin_datetime) >= ? ' , [ strtotime ($date) ]);
            //如果有传入参数，就搜索传入参数之后的
            if ($keyword !== '') {
                $builder = $builder->where ('resources_device.name' , 'like' , '%'.$keyword.'%');
            }

            return $builder->orderBy ($order[0] , $order[1])->paginate (config ('msc.page_size'));
        }


        public function cancelOpenDeviceApply ($id) {
            $apply         = $this->find ($id);
            $apply->status = -1;
            if ($apply->save ()) {
                return true;
            } else {
                throw new \Exception('取消失败');
            }

        }

        public function getMyApply ($id) {
            return $this->where ('apply_uid' , '=' , $id)->where ('status' , '=' , 0)->get ();
        }

        public function agreeApply ($id) {
            $connection = DB::connection ('msc_mis');
            $connection->beginTransaction ();
            $apply = $this->find ($id);
            $data  = [
                'opertion_uid'             =>  $apply  ->  apply_uid,
                'resources_device_id' => $apply->resources_device_id ,
                'currentdate' => date ('Y-m-d' , strtotime ($apply->original_begin_datetime)) ,
                'begintime' => date ('H:i' , strtotime ($apply->original_begin_datetime)) ,
                'endtime' => date ('H:i' , strtotime ($apply->original_end_datetime))
            ];
            $total = ResourcesDevicePlan::where ('resources_device_id' , '=' , $data['resources_device_id'])
                ->where ('currentdate' , '=' , $data['currentdate'])
                ->where ('begintime' , '=' , $data['begintime'])
                ->where ('endtime' , '=' , $data['endtime'])
                ->count ('id');
            try {
                if ($total > 0) {
                    $conflictPlan = ResourcesDevicePlan::where ('resources_device_id' , '=' , $data['resources_device_id'])
                        ->where ('currentdate' , '=' , $data['currentdate'])
                        ->where ('begintime' , '=' , $data['begintime'])
                        ->where ('endtime' , '=' , $data['endtime'])
                        ->first ();
                    throw   new \Exception('该设备此时间段已经被"' . $conflictPlan->userInfo->name . '"预约了"');

                } else {
                    $apply->status = 1;
                    if (!$apply->save ()) {
                        throw new \Exception('预约状态变更失败');
                    }
                    $newPlan = ResourcesDevicePlan::create ($data);
                    if ($newPlan) {
                        $connection ->commit();
                        return $newPlan;
                    } else {
                        throw   new \Exception('创建计划失败，审核通过不成功');
                    }
                }
            } catch (\Exception $ex) {
                $connection->rollBack ();
                throw $ex;
            }
        }

        public function refundApply ($id , $reject) {
            try {
                $apply = $this->find ($id);
                if (!$apply) {
                    throw new \Exception('没有找到相关申请');
                }
                $apply->status = 2;
                $apply->reject = $reject;
                if (!$result = $apply->save ()) {
                    throw new \Exception('预约状态变更失败');
                }
                return $result;
            } catch (\Exception $ex) {
                throw $ex;
            }
        }


        public function user () {

            return $this->hasOne ('App\Entities\User' , 'id' , 'apply_uid');

        }

        public function resourcesLabDevices () {

            return $this->hasOne ('Modules\Msc\Entities\ResourcesDevice' , 'id' , 'resources_device_id');
        }

        /**
         * 获取开放设备列表数据
         */
        public function getAjaxAppData ($data) {
            $thisBuilder = $this;
            if ($data) {
                $thisBuilder = $this->where ('resources_device_apply.original_begin_datetime' , 'like' , '%' . $data . '%');
            }
            $thisBuilder = $thisBuilder->where ('resources_device_id' , '!=' , 0);
            $result      = $thisBuilder->with ([
                'user' => function ($q) {
                    $q->where ('status' , '=' , 1);
                } ,
                'resourcesLabDevices'
            ])->paginate (7);
            return $result;
        }

        /**
         * 获取开放设备单条数据
         */
        public function getAppData ($aid) {
            $thisBuilder = $this;
            $thisBuilder = $thisBuilder->where ('resources_device_id' , '!=' , 0)->where('id','=',$aid);
            $result      = $thisBuilder->with ([
                'user' => function ($q) {
                    $q->where ('status' , '=' , 1);
                } ,
                'resourcesLabDevices'
            ])->first ();
            
            return $result;
        }

        // 根据用户id 设备id 使用时间 查询申请记录
        public function getApply ($uid , $deviceId , $currentdate , $begintime , $endtime) {
            return $this->where (function ($query) use ($uid , $deviceId , $currentdate , $begintime , $endtime) {
                $query->where ('apply_uid' , $uid)
                    ->where ('resources_device_id' , $deviceId)
                    ->where ('status' , 1)
                    ->whereRaw (
                        'unix_timestamp(end_datetime)=? ' , // 批准的结束日期
                        [ (strtotime ($currentdate) + strtotime ($endtime)) ]
                    )->whereRaw (
                        'unix_timestamp(begin_datetime)=? ' , // 批准的开始日期
                        [ (strtotime ($currentdate) + strtotime ($begintime)) ]
                    );
            })->firstOrFail ();
        }


    }