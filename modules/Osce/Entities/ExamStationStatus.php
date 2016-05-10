<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: j5110
 * Date: 2016/4/14
 * Time: 11:24
=======
 * User: wangjiang
 * Date: 2016/4/6 0006
 * Time: 18:14
>>>>>>> c61231fdd209f9d0eedce3341c26db5fcded099f
 */

namespace Modules\Osce\Entities;

use Modules\Msc\Entities\Teacher;
use Modules\Osce\Entities\CommonModel;
use Auth;
use DB;

class ExamStationStatus extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_station_status';
    public    $incrementing = true;
    public    $timestamps   = true;
    protected $fillable     = ['exam_id', 'exam_screening_id', 'station_id', 'status'];

    /**
     * ��ȡ��վ״̬
     * @method GET �ӿ�
     * @param $stations �����¿�վ����
     * @author wt <wangtao@misrobot.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamMsg($exam_id,$exam_screening_id,$stations){
        if(count($stations)){
            $msg=[];
            foreach ($stations as $v){
               $msg[]=$this->where('exam_id',$exam_id)
                     ->where('exam_screening_id',$exam_screening_id)
                     ->where('station_id',$v->station_id)
                     ->first();
            }
            $flag=false;
            $tag=false;
            if(count($msg)){
                foreach($msg as $val){
                    if($val->status==0){//δ׼��
                        $flag=true;
                    }else{//��׼��
                        $tag=true;
                    }
                }
                if(!$flag&&$tag){//��׼������flag=false $tag=true
                    return 1;
                }elseif($flag&&$tag){//׼����û׼������
                    return 2;
                }else{
                    return 4;
                }


            }
        }


    }

}