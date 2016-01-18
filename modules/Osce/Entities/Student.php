<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7 0007
 * Time: 10:11
 */

namespace Modules\Osce\Entities;

use App\User;
use Modules\Osce\Repositories\Common;
use App\Entities\User;
use Auth;
use DB;
class Student extends CommonModel
{

    protected $connection = 'osce_mis';
    protected $table = 'student';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'exam_id', 'user_id', 'idcard', 'mobile', 'code', 'avator', 'create_user_id'];

    public function userInfo(){
        return $this->hasOne('\App\Entities\User','id','user_id');
    }
    /**
     * 展示考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function showStudentList()
    {
        try {
            $student = $this->select([
                'id',
                'name',
                'idcard',
                'exam_id'
            ]);

            return $student->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 展示 考试 对应的考生列表的方法
     * @return mixed
     * @throws \Exception
     */
    public function selectExamStudent($exam_id, $keyword)
    {
        try {
            $result = $this->where('exam_id', '=', $exam_id);

            //如果keyword不为空，那么就进行模糊查询
            if ($keyword['keyword'] !== null) {
                $result = $result->where($this->table . '.name', 'like', '%' . $keyword['keyword'] . '%')
                    ->orWhere($this->table . '.idcard', 'like', '%' . $keyword['keyword'] . '%')
                    ->orWhere($this->table . '.mobile', 'like', '%' . $keyword['keyword'] . '%')
                    ->orWhere($this->table . '.code', 'like', '%' . $keyword['keyword'] . '%');
            }

            return $result->paginate(10);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除考生的方法
     * @param $id
     * @return bool
     */
    public function deleteData($student_id)
    {
        $connection = DB::connection($this->connection);
        $connection->beginTransaction();
        try {

            if (!$result = $this->where($this->table.'.id', '=', $student_id)->delete())
            {
                throw new \Exception('删除考生失败，请重试！');
            }

            $connection->commit();
            return true;

        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }

    /**
     * 单个添加考生
     * @return mixed
     * @throws \Exception
     */
    public function addExaminee($exam_id, $examineeData)
    {
        $connection = DB::connection($this->connection);
        $connection ->beginTransaction();
        try{
            $operator   =   Auth::user();
            if(empty($operator)){
                throw new \Exception('未找到当前操作人信息');
            }

            //根据条件：查找用户是否有账号和密码
            $user = User::where(['username' => $examineeData['mobile']])->first();
            //如果查找到了，对用户信息 进行编辑处理
            if(count($user) != 0){
                $user -> name   = $examineeData['name'];    //姓名
                $user -> gender = $examineeData['gender'];  //性别
                $user -> mobile = $examineeData['mobile'];  //手机号
                $user -> avatar = $examineeData['avator'];  //头像
                $user -> idcard = $examineeData['idcard'];  //身份证号
                $user -> email  = $examineeData['email'];   //邮箱
                if(!($user->save())){      //跟新用户
                    throw new \Exception('新增考生失败！');
                }

            }else{      //如果没找到，新增处理,   如果新增成功，发短信通知用户
                $password   =   '123456';
                $user       =   $this   ->  registerUser($examineeData,$password);
                $this       ->  sendRegisterEms($examineeData['mobile'],$password);
            }

            //根据用户ID和考试号查找考生
            $student = $this->where('user_id', '=', $user->id)
                ->where('exam_id', '=', $exam_id)->first();

            //存在考生信息,则更新数据, 否则新增
            if($student){
                //跟新考生数据
                $student->name    = $examineeData['name'];
                $student->exam_id = $exam_id;
                $student->idcard  = $examineeData['idcard'];
                $student->mobile  = $examineeData['mobile'];
                $student->code    = $examineeData['code'];
                $student->avator  = $examineeData['avator'];
                if (!($student->save())) {
                    throw new \Exception('新增考生失败！');
                }
            }else{
                $examineeData['exam_id'] = $exam_id;
                $examineeData['user_id'] = $user->id;
                $examineeData['create_user_id'] = $operator->id;
                if(!$result = $this->create($examineeData)){
                    throw new \Exception('新增考生失败！');
                }
            }

            $connection->commit();
            return true;

        } catch(\Exception $ex) {
            $connection->rollBack();
            throw $ex;
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
     * 考生身份验证
     * @param $watch_id
     * @return bool
     */

    public  function studentList($teacher_id){
        return Student::leftjoin('exam_queue',function($join){
            $join ->on('student.id','=','exam_queue.student_id');
        })->leftjoin('station_teacher',function($join){
            $join ->on('exam_queue.station_id','=','station_teacher.station_id');
        })->where('station_teacher.user_id','=',$teacher_id)
            ->where('exam_queue.status','=',1)
            ->select([
                'student.name as name',
                'student.code as code',
                'student.idcard as idcard',
                'student.mobile as mobile',
                'exam_queue.status as status'
            ])
            ->get();
    }
}