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

       $connection->beginTransaction();

       $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'grade'=>$data['grade'],'professional'=>$data['professional'],'student_type'=>$data['student_type']);

       $result=$connection->table('student')->update($item);

        if($result==false){
            $connection->rollBack();
       }

       $connection=\DB::connection('sys_mis');


       $users=array('id'=>$data['id'],'gender'=>$data['gender'],'moblie'=>$data['moblie'],'idcard_type'=>$data['idcard_type'],'idcard'=>$data['idcard'],'status'=>$data['status']);

       $result=$connection->table('users')->update($users);

        if($result==false){
            $connection->rollBack();
        }

        $connection->commit();
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


        $item=array('id'=>$data['id'],'name'=>$data['name'],'code'=>$data['code'],'grade'=>$data['grade'],'professional'=>$data['professional'],'student_type'=>$data['student_type']);

        $id=$connection->table('student')->insertGetId($item);

        if(!$id){
            return false;
        }

        $connection=\DB::connection('sys_mis');

        $users=array('id'=>$id,'gender'=>$data['gender'],'moblie'=>$data['moblie'],'idcard_type'=>$data['idcard_type'],'idcard'=>$data['idcard']);

        $result=$connection->table('users')->insert($users);

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
}
