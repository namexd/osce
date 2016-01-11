<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/5
 * Time: 16:50
 */

namespace Modules\Osce\Entities;


use App\Entities\User;
use DB;
use Modules\Osce\Repositories\Common;

class Teacher extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'teacher';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['id','name', 'code', 'type', 'case_id', 'create_user_id','status'];
    private $excludeId = [];

    protected $type_values  =   [
        '1' =>  '监考老师',
        '2' =>  'SP病人',
        '3' =>  '巡考老师',
    ];

    /**
     * 用户关联
     */
    public function userInfo(){
        return $this    ->  hasOne('\App\Entities\User','id','id');
    }
    /**
     * 获取是否为SP老师的值
     * @access public
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 16:56
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getIsSpValues(){
        return $this    ->  type_values;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function station()
    {
        return $this->belongsToMany('\Modules\Osce\Entities\station','station_sp','user_id','station_id');
    }




    //邀请老师的数据
    public  function invitationContent($teacher_id){
        $builder=$this;
        try{
            if ($teacher_id !== null) {
                $this->excludeId = $teacher_id;
            }
            $excludeId = $this->excludeId;
            $excludeIds[] = $excludeId;

            if (count($excludeId) !== 0) {
                $builder = $builder->leftJoin('cases',function($join){
                    $join    ->  on('cases.id','=', 'teacher.case_id');
                })->whereIn($this->table.'.id', $excludeIds);
            }
            $data=$builder->select('teacher.name','teacher.id','cases.name as cname','cases.id as caseId')->get()->toArray();
            $list=[];
            foreach($data as $v){
                $list['teacher_id']=$v['id'];
                $list['teacher_name']=$v['name'];
                $list['case_name']=$v['cname'];
                $list['case_id']=$v['caseId'];
            }


//             $openId= $this->where('id', '=', $list['teacher_id'])->with('userInfo')->first()->toArray();
            $openId= Teacher::find($list['teacher_id'])->userInfo->toArray();
            $list['openid']=$openId['openid'];
//            dd($list);

            return $list;

        }catch (\Exception $ex) {
            throw $ex;
        }

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




    /**
     * 获取sp老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 10:52
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getSpInvigilatorList(){
        return  $this   ->  where('type','=',2)
            ->  paginate(config('osce.page_size'));
    }

    /**
     * 获取非SP监考老师列表
     * @access public
     *
     * @param
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return pagination
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 16:58
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getInvigilatorList(){
        return  $this   ->  whereIn('type',[1,3])
            ->  paginate(config('osce.page_size'));
    }

    /**
     * 新增监考人
     * @access public
     *
     * @param array $data
     * * string        name             用户姓名(必须的)
     * * string        mobile           用户手机号(必须的)
     * * string        code             用户工号(必须的)
     * * string        type             用户类型(必须的)
     * * string        case_id          病例ID(必须的)
     * * string        status           用户状态(必须的)
     * * string        create_user_id   创建人ID(必须的)
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-08 10:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function addInvigilator($data){
        $mobile =   $data['mobile'];
        $user   =   User::where('username','=',$mobile)->first();

        if(!$user)
        {
            $password   =   Common::getRandStr(6);
            $user       =   $this   ->  registerUser($data,$password);
            $this       ->  sendRegisterEms($mobile,$password);
        }
        $teacher    =   $this   ->  find($user  ->  id);
        if($teacher)
        {
            //TODO:蒋志恒2016.1.10修改，去掉错误抛出，改为重写teacher
            $teacher->name = $data['name'];
            $teacher = $teacher->save();
            if (!$teacher) {
                throw new \Exception('保存老师名字失败，请重试！');
            } else {
                return $teacher;
            }
//            throw new \Exception('该教职员工已经存在');
        }
        else
        {
            $data['id'] =   $user    ->  id;
            if($teacher =   $this   ->  create($data))
            {
                return $teacher;
            }
            else
            {
                throw new \Exception('教职员工创建失败');
            }
        }
    }

    public function registerUser($data,$password){
        $form_user=$data;
        $form_user['username']  =   $data['mobile'];
        $form_user['openid']    =   '';
        $form_user['password']  =   bcrypt($password);
        $user=User::create($form_user);
        if($user)
        {
            return $user;
        }
        else
        {
            throw new \Exception('创建用户失败');
        }
    }
    public function sendRegisterEms($mobile,$password){
        //发送短消息
    }

    /**
     * 编辑非SP教务人员
     * @access public
     *
     * @param int       $id     教务人员ID
     * @param string    $name   教务人员姓名
     * @param string    $mobile 教务人员手机号
     *
     * @return object
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function editInvigilator($id,$name,$mobile,$type){
        //教务人员信息变更
        $teacher    =   $this   ->  find($id);
        if(!$teacher)
        {
            throw new   \Exception('没有找到该教务人员');
        }
        if($teacher->name!==$name)
        {
            $teacher    ->  name    =   $name;
        }
        if($teacher->type!==$type)
        {
            $teacher    ->  type    =   $type;
        }
        if(!$teacher->save())
        {
            throw new   \Exception('教务人员名称变更失败');
        }
        //教务人员用户信息变更
        $userInfo   =   $teacher->userInfo;
        if($userInfo->name!==$name)
        {
            $userInfo   ->name  =$name;
        }
        if($userInfo->mobile!==$mobile)
        {
            $userInfo   ->mobile  =$name;
        }
        if(!$userInfo->save())
        {
            throw new   \Exception('教务人员用户信息变更失败');
        }
        return $teacher;
    }

    public function editSpInvigilator($id,$name,$mobile,$caseId){
        //教务人员信息变更
        $teacher    =   $this   ->  find($id);
        if(!$teacher)
        {
            throw new   \Exception('没有找到该教务人员');
        }
        if($teacher->name!==$name)
        {
            $teacher    ->  name    =   $name;
        }
        if($teacher->case_id!==$caseId)
        {
            $teacher    ->  case_id    =   $caseId;
        }
        if(!$teacher->save())
        {
            throw new   \Exception('教务人员名称变更失败');
        }
        //教务人员用户信息变更
        $userInfo   =   $teacher->userInfo;
        if($userInfo->name!==$name)
        {
            $userInfo   ->name  =$name;
        }
        if($userInfo->mobile!==$mobile)
        {
            $userInfo   ->mobile  =$name;
        }
        if(!$userInfo->save())
        {
            throw new   \Exception('教务人员用户信息变更失败');
        }
        return $teacher;
    }

    /**
     * 获取监考老师列表
     * @return mixed
     * @throws \Exception
     */
    public function getTeacherList($formData)
    {
        try{
            $teacher = $this->where(['type' => 1])
                ->select(['id', 'name'])->get();

            return $teacher;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function registerTeacher(){
        //$this   ->  registerUser();
    }
}