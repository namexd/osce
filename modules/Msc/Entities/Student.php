<?php

namespace Modules\Msc\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Msc\Entities\CommonModel;
use App\Entities\User;

class Student extends CommonModel {

    public $timestamps	=	true;
    public $incrementing	=	true;

    protected $connection	=	'msc_mis';
    protected $table 		= 	'student';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $hidden 		= 	[];
    protected $fillable 	=	['name','code','qq','class','grade','professional','student_type','validated','id'];


    /**
     * 关联用户信息
     * @return \App\Entities\User
     */
    public function userInfo()
    {
        return $this->hasOne('App\Entities\User','id','id');
    }

    /**
     * 格式化班级名称
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function className(){
        return $this->belongsTo('Modules\Msc\Entities\StdClass','class');
    }

    /**
     * 格式化专业名称
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professionalName(){
        return $this->belongsTo('Modules\Msc\Entities\StdProfessional','professional');
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

    // 学生类型获取器
    public function getStudentTypeAttribute ($value)
    {
        switch ($value) {
            case 1:
                $type = '本科';
                break;

            case 2:
                $type = '专科';
                break;

            default:
                $type = '-';
        }

        return $type;
    }

    // 获得分页列表
    public function getFilteredPaginateList ($kwd='', $status='', $grade='', $studentType='', $profession='')
    {
        $userDb    = config('database.connections.sys_mis.database');
        $userTable = $userDb.'.users';

        $studentDb    = config('database.connections.msc_mis.database');
        $studentTable = $studentDb.'.student';

        $builder = $this->leftJoin($userTable, function($join) use($userTable, $studentTable) {
            $join->on($userTable.'.id', '=', $studentTable.'.id');
        });

        if ($kwd)
        {
            $builder = $builder->whereRaw(
                'locate(?, '.$studentTable.'.name)>0 or locate(?, '.$studentTable.'.code)>0 or locate(?, '.$userTable.'.mobile)>0 or locate(?, '.$userTable.'.idcard)>0 ',
                [$kwd, $kwd, $kwd, $kwd]
            );
        }

        if ($status)
        {
            $builder = $builder->where($userTable.'.status', '=', $status);
        }

        if ($grade)
        {
            $builder = $builder->where($studentTable.'.grade', '=', $grade);
        }

        if ($studentType)
        {
            $builder = $builder->where($studentTable.'.student_type', '=', $studentType);
        }

        if ($profession)
        {
            $builder = $builder->where($studentTable.'.professional', '=', $profession);
        }

        return $builder->select([
            $studentTable.'.id as id',
            $studentTable.'.name as name',
            $studentTable.'.code as code',
            $studentTable.'.grade as grade',
            $studentTable.'.student_type as student_type',
            $studentTable.'.professional as professional',
            //$userTable.'.mobile as mobile',
            //$userTable.'.idcard as idcard',
            //$userTable.'.gender as gender',
            //$userTable.'.status as status',
        ])->orderBy($studentTable.'.id')->paginate(config('msc.page_size',10));
    }

    // 获取年级列表
    public function getGradeList ()
    {
        return $this->distinct()->lists('grade');
    }

    /**
     *
     * @method POST
     * @url /msc/admin/user/student-save/saveEditStudent
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
    public function saveEditStudent($data){

        $connection=\DB::connection('msc_mis');

        $professional=$connection->table('student_professional')->where('name',$data['professional_name'])->first();

        if(!$professional){

            $professional_id=$connection->table('student_professional')->insertGetId(['name'=>$data['professional_name']]);

        }else{
            $professional_id=$professional->id;
        }


       $item=array(
           'name'=>$data['name'],
           'code'=>$data['code'],
           'grade'=>$data['grade'],
           'professional'=>$professional_id,
           'student_type'=>$data['student_type']
       );

       $result=$connection->table('student')->where('id',$data['id'])->update($item);

        if($result===false){
            return false;
        }

       $connection=\DB::connection('sys_mis');

       $result=$connection->table('users')->find($data['id']);

       if(!$result){
           $users=array(
               'name'=>$data['name'],
               'gender'=>$data['gender'],
               'mobile'=>$data['mobile'],
               'idcard_type'=>$data['idcard_type'],
               'idcard'=>$data['idcard']
           );

           return $connection->table('users')->where('id',$data['id'])->insert($users);
       }else{
           $users_mobile=$connection->table('users')->where('id',$data['id'])->select('mobile')->first();

           $users_mobile=$users_mobile->mobile;

           if($data['mobile']==$users_mobile){
               $users=array(
                   'name'=>$data['name'],
                   'gender'=>$data['gender'],
                   'idcard_type'=>$data['idcard_type'],
                   'idcard'=>$data['idcard']
               );
           } else{
               $users=array(
                   'name'=>$data['name'],
                   'gender'=>$data['gender'],
                   'mobile'=>$data['mobile'],
                   'idcard_type'=>$data['idcard_type'],
                   'idcard'=>$data['idcard']);
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
     * @url /msc/admin/user/student-add/postAddStudent
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

    public function postAddStudent($data){

        $connection=\DB::connection('msc_mis');

        $professional=$connection->table('student_professional')->where('name',$data['profession_name'])->first();

        if(!$professional){

            $professional_id=$connection->table('student_professional')->insertGetId(['name'=>$data['profession_name']]);

        }else{
            $professional_id=$professional->id;
        }

        $connection=\DB::connection('sys_mis');

        $users=array(
            'name'=>$data['name'],
            'gender'=>$data['gender'],
            'mobile'=>$data['mobile'],
            'idcard_type'=>$data['idcard_type'],
            'idcard'=>$data['idcard']
        );

        $users_mobile=$connection->table('users')->where('mobile',$data['mobile'])->select('mobile')->first();
         if($users_mobile){
             $users_mobile=$users_mobile->mobile;

             if($users_mobile==$data['mobile']){
                 return false;
             }
         }



        $id=$connection->table('users')->insertGetId($users);

        if(!$id){
            return false;
        }

        $item=array(
            'id'=>$id,
            'name'=>$data['name'],
            'code'=>$data['code'],
            'grade'=>$data['grade'],
            'professional'=>$professional_id,
            'student_type'=>$data['student_type']
        );

       $result=$this->create($item);

        return $result;
    }

    /**
     *
     * @method GET
     * @url /msc/admin/user/student-trashed/{id}/SoftTrashed
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        主键id
     *
     * @return blooean
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
     * @url /msc/admin/user/student-status/{id}/changeStatus
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>get请求字段：</b>
     * * int        id        主键id
     *
     * @return blooean
     *
     * @version 1.0
     * @author zhouchong <zhoucong@misrobot.com>
     * @date 2015-12-16 14:48
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function changeStatus($id){

         $connection=\DB::connection('sys_mis');


         $data=$connection->table('users')->select('status')->find($id);

         foreach($data as $tmp){
            $status=$tmp;
         }

         return $connection->table('users')->where('id',$id)->update(['status'=>3-$status]);

    }
//
//$item=array(
//'id'=>$id,
//'name'=>$data['name'],
//'code'=>$data['code'],
//'grade'=>$data['grade'],
//'professional'=>$professional_id,
//'student_type'=>$data['student_type']
//);

    //导入学生数据存入数据库
    public function AddStudent($studentData){
//        $id=$connection->table('users')->insertGetId($users);
        $connection=\DB::connection('sys_mis');
        $item=array('name' =>$studentData['name'] ,
            'mobile' => $studentData['mobile'],
            'idcard'=>$studentData['idcard'],
            'gender'=>$studentData['gender'],
            'status'=>$studentData['status'],);
        $id=$connection->table('users')->insertGetId( $item);
        if(!$id){
            return false;
        }
        $student=array(
                'id'=>$id,
                'name' =>$studentData['name'] ,
                'code' => $studentData['code'],
                'grade'=>$studentData['grade'],
                'student_type'=>$studentData['student_type'],
                'professional'=>$studentData['professional']
        );
        $result=$this->create($student);
            return $result;
    }
}
