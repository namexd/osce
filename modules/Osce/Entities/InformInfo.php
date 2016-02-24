<?php
/**
 * Created by PhpStorm.
 * User: zhouchong
 * Date: 2016/1/12 0012
 * Time: 19:33
 */
namespace Modules\Osce\Entities;


class InformInfo extends CommonModel{
    protected $connection	=	'osce_mis';
    protected $table 		= 	'inform_info';
    public $incrementing	=	true;
    public $timestamps	    =	true;
    protected $fillable 	=   ['exam_id','name','accept','content','attachments','status','create_user_id'];

    public function exam(){
        return $this->hasOne('\Modules\Osce\Entities\Exam','id','exam_id');
    }
    public function receivers(){
        return $this->hasManyThrough('App\Entities\User','Modules\Osce\Entities\NoticeTo','id','notice_id','id');
    }
    public function addNotice(array $data,array $to){
        $connection     =   DB::connection($this->connection);
        $connection     ->  beginTransaction();
        try{
            if($notice  =   $this   -> create($data))
            {
                //������Ϣ�����û�����Ϣ
                $this   ->  makeNoticeUserRelative($notice,$to);
                //֪ͨ�û�
                $this   ->  sendMsg($notice,array_pluck($to,'opendid'));
                $connection ->commit();
                return $notice;
            }
            else
            {
                throw new \Exception('����֪ͨʧ��');
            }
        }
        catch(\Exception $ex)
        {
            $connection ->rollBack();
            throw $ex;
        }
    }

    public function editNotice($id,$title,$content){
        try{
            if($notice  =   $this   -> find($id))
            {
                $notice ->  title   =   $title;
                $notice ->  content =   $content;
                if(!$notice  ->save())
                {
                    throw new \Exception('�޸�֪ͨʧ��');
                }
                //������Ϣ�����û�����Ϣ
                $to     =   $this   ->  getNoticeToOpendIds($notice);
                //֪ͨ�û�
                $this   ->  sendMsg($notice,array_pluck($to,'opendid'));
                return $notice;
            }
            else
            {
                throw new \Exception('����֪ͨʧ��');
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
    public function makeNoticeUserRelative($notice,array $to){
        $data   =   [];
        foreach($to as $item)
        {
            $data[]   =   [
                'notice_id' =>  $notice ->  id,
                'uid'       =>  $item['id']
            ];
        }
        $noticeToModel  =   new NoticeTo();
        if($noticeToModel   -> addNoticeTo($data))
        {
            return true;
        }
        else
        {
            throw new \Exception('�����ռ���ʧ��');
        }
    }
    public function sendMsg($notice,$to){
        try
        {
            $url    =   route('osce.admin.notice.getMsg',['id'=>$notice->id]);
            $msgData    =   [
                [
                    'title' =>$notice->exam->name.'֪ͨ',
                ],
                [
                    'title' =>  $notice->title,
                    'url'   =>  $url
                ]
            ];
            $message    =   Common::CreateWeiXinMessage($msgData);
            Common::sendWeixinToMany($message,$to);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    /**
     * ����֪ͨ
     * @access public
     *
     * @param $title        ֪ͨ����
     * @param $content      ֪ͨ����
     * @param $exam_id      ֪ͨ��������ID
     * @param $groups       ��֪ͨ����Ⱥ
     *
     * @return
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function sendNotice($title,$content,$exam_id,array $groups){
        $data   =   [
            'title'     =>  $title,
            'content'   =>  $content,
            'exam_id'   =>  $exam_id,
        ];
        try{
            $to     =   $this   ->  getGroupsOpendIds($groups,$exam_id);
            $notice =   $this   ->  addNotice($data,$to);
            return $notice;
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }
    }



    /**
     *  获取资讯列表
     * @author Zhoufuxiang <Zhoufuxiang@misrobot.com>
     */
    public function getList($accept, $exam_ids){
        switch($accept){
            case 1: $where = '1%';
                break;
            case 2: $where = '%2%';
                break;
            case 3: $where = '%3';
                break;
            default: $where = '1,2,3';
        }

        return $this->whereNotNUll('accept')->where('accept', 'like', "$where")
            ->whereIn('exam_id', $exam_ids)->paginate(config('osce.page_size'));
    }

    /**
     * ����Ⱥ���б��ȡopendid�б�
     * @access public
     *
     * @param  array $groups �û�ѡ��Ľ���Ⱥ��
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-06 19:25
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getGroupsOpendIds($groups,$exam_id){
        $data   =   [];
        if(in_array(1,$groups))
        {
            $data   =   $this   ->  getStudentsOpendIds($exam_id,$data);
        }
        if(in_array(2,$groups))
        {
            $data   =   $this   ->  getExamTeachersOpendIds($exam_id,$data);
        }
        if(in_array(3,$groups))
        {
            $data   =   $this   ->  getExamSpTeachersOpendIds($exam_id,$data);
        }
        return $data;
    }
    private function getExamTeachersOpendIds($exam_id,array $data=[]){
        $ExamRoom   =   new ExamRoom();
        $list   =   $ExamRoom   ->  getRoomTeachersByExamId($exam_id);
        dd(123);
        foreach($list as $teacher)
        {
            if(is_null($teacher->userInfo))
            {
                throw new \Exception('û���ҵ�ָ���Ľ�����Ա�û���Ϣ');
            }
            if($teacher->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $teacher->userInfo->id,
                    'openid'=>  $teacher->userInfo->openid,
                ];
            }
        }
        return $data;
    }

    /**
     * ���ݿ���ID��ȡѧ��openid�б�
     * @access public
     *
     * @param int $exam_id ����id
     * @param array $data
     *
     * @return array
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    private function getStudentsOpendIds($exam_id,array $data=[]){
        $list   =   Teacher::where('exam_id','=',$exam_id);
        foreach($list as $teacher)
        {
            if(is_null($teacher->userInfo))
            {
                throw new \Exception('û���ҵ�ָ���Ľ�����Ա�û���Ϣ');
            }
            if($teacher->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $teacher->userInfo->id,
                    'openid'=>  $teacher->userInfo->openid,
                ];
            }
        }
        return $data;
    }
    private function getExamSpTeachersOpendIds($exam_id,array $data=[]){
        $ExamRoom   =   new ExamRoom();
        $list   =   $ExamRoom   ->  getRoomSpTeachersByExamId($exam_id);
        foreach($list as $teacher)
        {
            if(is_null($teacher->userInfo))
            {
                throw new \Exception('û���ҵ�ָ���Ľ�����Ա�û���Ϣ');
            }
            if($teacher->userInfo->openid)
            {
                $data[] =   [
                    'id'    =>  $teacher->userInfo->id,
                    'openid'=>  $teacher->userInfo->openid,
                ];
            }
        }
        return $data;
    }

    public function getNoticeToOpendIds($notice){
        return  $notice->receivers;
    }
}