<?php

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;

class Teacher extends CommonModel {

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'teacher';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['code','teacher_dept','validated','id'];


    /**
     * 关联用户信息
     * @return \App\Entities\User
     */
    public function userInfo()
    {
        return $this->hasOne('App\Entities\User','id','id');
    }

    public function dept(){
        return $this->belongsTo('\Modules\Msc\Entities\TeacherDept','teacher_dept');
    }

    /**
     * 格式化用户证件类型
     * @return mixed
     */
    public function getIdCardType(){
        $value=config('msc.idcard_type');
        if( is_array($value) && $this->userInfo!=null ){
            if(array_key_exists($this->userInfo->idcard_type,$value)){
                return $value[$this->userInfo->idcard_type];
            }
        }
    }

    // 获得分页列表
    public function getFilteredPaginateList ($kwd='', $order=['id', 'desc'])
    {
        $builder = $this;

        if ($kwd)
        {
            $builder = $builder->whereRaw(
                'locate(?, teacher.name)>0 or locate(?, teacher.code)>0 ',
                [$kwd, $kwd]
            );
        }

        return $builder->orderBy($order['0'], $order['1'])->paginate(config('msc.page_size',10));
    }

    /**
     *
     * @method POST
     * @url /msc/admin/user/teacher-save/saveEditTeacher
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * array       $data        控制器传递参数
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhoucong@misrobot.com>
     * @date 2015-12-16 14:50
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function saveEditTeacher($data){
        $connection=\DB::connection('msc_mis');
        $connection->beginTransaction();

        $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'teacher_dept'=>$data['teacher_dept']);

        $result=$connection->table('teacher')->update($item);
        if($result==false){
            $connection->rollBack();
        }

        $connection=\DB::connection('sys_mis');
        $users=array('id'=>$data['id'],'gender'=>$data['gender'],'moblie'=>$data['moblie']);
        $result=$connection->table('users')->update($users);
        if($result==false){
            $connection->rollBack();
        }

        $connection->commit();
    }


    /**
     *
     * @method POST
     * @url /msc/admin/user/teacher-add/postAddTeacher
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * array        $data        控制器传递参数
     *
     * @return blooean
     *
     * @version 1.0
     * @author zhouchong <zhoucong@misrobot.com>
     * @date 2015-12-16 14:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postAddTeacher($data){
        $connection=\DB::connection('msc_mis');


        $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'teacher_dept'=>$data['teacher_dept']);

        $id=$connection->table('teacher')->insertGetId($item);

        if(!$id){
            return false;
        }

        $connection=\DB::connection('sys_mis');
        $users=array('id'=>$id,'gender'=>$data['gender'],'moblie'=>$data['moblie'],'status'=>$data['status']);

        $result=$connection->table('users')->insert($users);

        return $result;
    }

    /**
     *
     * @method GET
     * @url /msc/admin/user/teacher-trashed/{id}/SoftTrashed
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        主键id
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhoucong@misrobot.com>
     * @date 2015-12-16  14:45
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function SoftTrashed($id){
        $connection=\DB::connection('sys_mis');

        return $connection->table('users')->where('id',$id)->update(['status'=>2]);

    }

    /**
     *
     * @method GET
     * @url /msc/admin/user/teacher-status/{id}/changeStatus
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        主键id
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouchong <zhoucong@misrobot.com>
     * @date 2015-12-16 14:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function changeStatus($id){
        $connection=\DB::connection('sys_mis');

        $data=$connection->table('users')->where('id',$id)->select('status')->first();

        foreach($data as $tmp){
           $status=$tmp;
        };

        return $connection->table('users')->where('id',$id)->update(['status'=>1-$status]);

    }
}
