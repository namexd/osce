<?php

namespace Modules\Msc\Entities;

use Modules\Msc\Entities\CommonModel;
use App\Entities\User;

class Teacher extends CommonModel {

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'teacher';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name','code','teacher_dept','validated','id','professionalTitle'];


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

    public function role(){
        
    }

    //用户管理员
    public function user(){

        return $this->hasOne('App\Entities\User','id','id');
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
    public function getFilteredPaginateList ($kwd='', $status='', $teacherDept='')
    {
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $teacherDb    = config('database.connections.msc_mis.database');
        $teacherTable = $teacherDb.'.teacher';

        $builder = $this->leftJoin($userTable, function($join) use($userTable, $teacherTable) {
            $join->on($userTable.'.id', '=', $teacherTable.'.id');
        });

        if ($kwd)
        {
            $builder = $builder->whereRaw(
                'locate(?, '.$teacherTable.'.name)>0 or locate(?, '.$teacherTable.'.code)>0 or locate(?, '.$userTable.'.mobile)>0 ',
                [$kwd, $kwd, $kwd]
            );
        }

        if ($status)
        {
            $builder = $builder->where($userTable.'.status', '=', $status);
        }

        if ($teacherDept)
        {
            $builder = $builder->where($teacherTable.'.teacher_dept', '=', $teacherDept);
        }

        return $builder->select([
            $teacherTable.'.id as id',
            $teacherTable.'.name as name',
            $teacherTable.'.code as code',
            $teacherTable.'.teacher_dept as teacher_dept',
        ])->orderBy($teacherTable.'.id')->paginate(config('msc.page_size',10));
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

//        dd($data);
        $dept=$connection->table('teacher_dept')->where('name',$data['dept_name'])->select()->first();
//        dd($dept);
        if(!$dept){
            $dept_id=$connection->table('teacher_dept')->insertGetId(['name'=>$data['dept_name']]);
        }else{
            $dept_id=$dept->id;
        }

        $item=array(
            'name'=>$data['name'],
            'code'=>$data['code'],
            'teacher_dept'=>$dept_id
        );


        $result=$connection->table('teacher')->where('id',$data['id'])->update($item);

        if($result===false){
            return false;
        }

        $connection=\DB::connection('sys_mis');

        $result=$connection->table('users')->find($data['id']);


        if(!$result){
            $users=array(
                'name'=>$data['name'],
                'gender'=>$data['gender'],
                'mobile'=>$data['mobile']
            );

            return $connection->table('users')->where('id',$data['id'])->insert($users);
        }else{

            $users_mobile=$connection->table('users')->where('id',$data['id'])->select('mobile')->first();

            $users_mobile=$users_mobile->mobile;

            if($data['mobile']==$users_mobile){
                $users=array(
                    'name'=>$data['name'],
                    'gender'=>$data['gender'],
                    'mobile'=>$data['mobile']
                );
            } else{
                $users=array('name'=>$data['name'],'gender'=>$data['gender']);
            }

            $result=$connection->table('users')->where('id',$data['id'])->update($users);

            if($result===false){
                return false;
            }

            return $result;
        }

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

        $connection=\DB::connection('sys_mis');
//        dd($data);
        $users=array(
            'name'=>$data['name'],
            'gender'=>$data['gender'],
            'mobile'=>$data['mobile'],
            'status'=>$data['status']
        );

        $users_mobile=$connection->table('users')->where('mobile',$data['mobile'])->select('mobile')->first();


        if($users_mobile){
            $users_mobile=$users_mobile->mobile;

              if($data['mobile']==$users_mobile){
                return false;
              }
        }



        $id=$connection->table('users')->insertGetId($users);

        if(!$id){
            return false;
        }

        $connection=\DB::connection('msc_mis');

        $dept=$connection->table('teacher_dept')->where('name',$data['dept_name'])->first();

        if(!$dept){
            $dept_id=$connection->table('teacher_dept')->insertGetId(['name'=>$data['dept_name']]);
        }else{
            $dept_id=$dept->id;
        }

        $item=array(
            'id'=>$id,
            'name'=>$data['name'],
            'code'=>$data['code'],
            'teacher_dept'=>$dept_id
        );

        $result=$connection->table('teacher')->insert($item);

        return $result;
    }

    /**
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

        return $connection->table('users')->where('id',$id)->update(['status'=>3]);

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

        return $connection->table('users')->where('id',$id)->update(['status'=>3-$status]);

    }

    //导入教师数据存入数据库
    public function AddTeacher($teacherData){

        $connection=\DB::connection('sys_mis');
        $item=array(
            'name' =>$teacherData['name'] ,
            'mobile' => $teacherData['mobile'],
            'gender'=>$teacherData['gender'],
            'status'=>$teacherData['status'],
//          'role'=>$teacherData['role'],
        );
        $id=$connection->table('users')->insertGetId( $item);
        if(!$id){
            return false;
        }

        $teacher=array(
            'id'=>$id,
            'name' =>$teacherData['name'] ,
            'code' => $teacherData['code'],
            'teacher_dept'=>$teacherData['teacher_dept'],
            );
        $result=$this->create($teacher);
        return $result;
    }

    //user表关联
    public function aboutUser(){
        return $this->hasOne('App\Entities\User','id','id');
    }
    ////获取和老师管理的用户数据
    public  function getdata(){
        $builder = $this;
        $builder = $builder->with('aboutUser')->get();
        return $builder;
    }
}
