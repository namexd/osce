<?php
    /**
     * Created by PhpStorm.
     * User: wangjiang
     * Date: 2015/12/7 0007
     * Time: 16:18
     */

    namespace Modules\Msc\Entities;

    class ResourcesDeviceHistory extends CommonModel {
        protected $connection = 'msc_mis';
        protected $table = 'resources_device_history';
        public $timestamps = true;
        protected $primaryKey = 'id';
        public $incrementing = true;
        protected $fillable = [
            'id' ,
            'resources_device_apply_id' ,
            'resources_device_plan_id' ,
            'resources_lab_id' ,
            'begin_datetime' ,
            'end_datetime' ,
            'opertion_uid' ,
            'resources_device_id' ,
            'result_poweroff' ,
            'result_init'
        ];

        //获取设备使用历史记录列表
        public function getHistoryList ($data) {
            $thisBuilder = $this->where ('resources_device_id' , '>' , 0);
            if (!empty($data['dateTime'])) {
                $thisBuilder = $thisBuilder->whereRaw (
                    'unix_timestamp(begin_datetime)>? and unix_timestamp(begin_datetime)<=? ' , // 默认所有的使用都是在一天完成
                    [
                        strtotime ($data['dateTime']) ,
                        (strtotime ($data['dateTime']) + 86400)
                    ]
                );
            }
            $result = $thisBuilder->with ('ResourcesDevice' , 'user')->paginate (7);
            return $result;
        }

        //获取设备使用历史详情
        public function getHistoryDetail ($data) {
            $thisBuilder = $this->where ('resources_device_id' , '>' , 0);
            if (!empty($data['id'])) {
                $thisBuilder = $thisBuilder->where ('id' , '=' , $data['id']);
            }

            $result = $thisBuilder->with ([
                'ResourcesDevice' => function ($q) {
                    $q->with ('ResourcesClassroom');
                } ,
                'user' ,
                'ResourcesDeviceApply'
            ])->first ();

            return $result;
        }

        //和设备信息的关联
        public function ResourcesDevice () {
            return $this->hasOne ('Modules\Msc\Entities\ResourcesDevice' , 'id' , 'resources_device_id');
        }

        //和用户信息的关联
        public function user () {
            return $this->hasOne ('App\Entities\User' , 'id' , 'opertion_uid');
        }

        //和预约表的关系
        public function ResourcesDeviceApply () {
            return $this->hasOne ('Modules\Msc\Entities\ResourcesDeviceApply' , 'id' , 'resources_device_apply_id');
        }

        /**
         * 设备预约历史列表
         * @param $keyword
         * @param $date
         * @param $order
         */
        public function getDeviceReserveHistoryList ($keyword , $date , $order) {
            $builder = $this->leftJoin (
                'resources_device' , function ($join) {
                $join->on ('resources_device.id' , '=' , $this->table . '.resources_device_id');
            }
            )->leftJoin (
                'resources_device_apply' , function ($join) {
                $join->on ('resources_device_apply.id' , '=' , $this->table . '.resources_device_apply_id');
            }
            )->leftJoin (
                'student' , function ($join) {
                $join->on ('student.id' , '=' , 'resources_device_apply.apply_uid');
            }
            );

            //日期搜索
            $builder = $builder->whereRaw ('unix_timestamp(resources_device_apply.original_begin_datetime)>= ? ' , [ strtotime (date('Y-m-d',strtotime($date)))]);

            //关键字搜索
            if ($keyword !== '') {
                $builder = $builder->where ('resources_device.name' , 'like' , '%' . $keyword . '%');
            }

            //选择搜索的字段
            $builder->select (
                [
                    'resources_device_history.id as id' ,
                    'resources_device.name as name' ,
                    'resources_device_apply.original_begin_datetime as original_begin_datetime' ,
                    'resources_device_apply.original_end_datetime as original_end_datetime' ,
                    'resources_device.code as code' ,
                    'resources_device_apply.detail as detail' ,
                    'student.name as student_name' ,
                    'resources_device_history.result_health as health' ,
                    'resources_device_history.result_init as reset' ,
                    'resources_device_history.resources_device_apply_id as resources_device_apply_id'
                ]
            );
            //返回对象,顺带设置排序和分页
            return $builder->orderBy ($order[0] , $order[1])->paginate (config ('msc.page_size'));
        }

        /**
         * 查看预约历史信息
         * @param $id
         */
        public function viewDeviceReserveHistoryList ($id) {
            //将预约消息的$id转化为设备的$id
//            dd(123);

            $id = $this->find ($id)->resources_device_id;
            //构建sql语句
            $builder = $this->leftJoin ('resources_device' , function ($join) {
                $join->on ('resources_device.id' , '=' , $this->table . '.resources_device_id');
            })->leftJoin ('resources_device_apply' , function ($join) {
                $join->on ('resources_device_history.resources_device_id' , '=' , 'resources_device_apply.resources_device_id');
            })->leftJoin ('resources_lab' , function ($join) {
                $join->on ('resources_device.resources_lab_id' , '=' , 'resources_lab.id');
            })->leftJoin ('student' , function ($join) {
                $join->on ('student.id' , '=' , $this->table . '.opertion_uid');
            })->where ('resources_device.id' , '=' , $id);
            //选择搜索的字段
            $builder->select (
                [
                    'resources_device_history.id as id' ,
                    'resources_device.name as name' ,
                    'resources_device_apply.original_begin_datetime as original_begin_datetime' ,
                    'resources_device_apply.original_end_datetime as original_end_datetime' ,
                    'resources_device.code as code' ,
                    'resources_device_apply.detail as detail' ,
                    'student.name as student_name' ,
                    'resources_lab.name as address' ,
                    'resources_device_history.result_health as health' ,
                    'resources_device_history.result_init as init' ,
                ]
            );
            //返回对象
            return $builder->first ();
        }

    }