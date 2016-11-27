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
use Modules\Osce\Entities\Drawlots\DrawlotsRepository;

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
     * @author wt <wangtao@163.com>
     * @date 2016-5-3
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
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

    //改变考站的准备状态

    public function  getStationStatus($examId,$stationId,$exam_screening_id,$type=1)
    {
        if($type == 1){
            $StationStatus =$this->where('exam_id',$examId)
                ->where('exam_screening_id',$exam_screening_id)
                ->where('station_id','=',$stationId)
                ->first();
            if(is_null($StationStatus)){
                throw new \Exception('没有找到对应的准备考站信息');
            }else{
                $StationStatus ->status = 1;
                if(!$StationStatus->save()){
                    throw new \Exception('改变考站准备信息失败');
                }
            }
        }else{
            $StationStatus =$this->where('exam_id',$examId)
                ->where('exam_screening_id',$exam_screening_id)
                ->whereIn('station_id',$stationId)
                ->get();
            if(!$StationStatus->isEmpty()){
                foreach ($StationStatus as $val){
                    $val->status =2;
                    if(!$val->save()){
                        throw new \Exception('改变考站准备信息失败');
                    }
                }
            }
        }
    }

    /**
     * 查询当前老师对应考站准备完成信息，判断是否准备完成
     * @param $examId
     * @param $examScreeningId
     * @param $stationId
     * @return mixed
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @date   2016-06-14 10:00
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function getStationReadyStatus($examId, $examScreeningId, $stationId)
    {
        try{
            $stationStatus = $this->where('exam_id', '=', $examId)
                ->where('exam_screening_id', '=', $examScreeningId)
                ->where('station_id', '=', $stationId)
                ->first();
            //判断是否准备完成
            if (is_null($stationStatus)) {
                throw new \Exception('未查询到当前考站是否准备完成信息', -1);
            }else{
                if($stationStatus->status !=2){
                    $stationStatus->status =1;
                    if(!$stationStatus->save()){
                        throw new \Exception('老师准备失败', -1);
                    }
                }

            }

            return $stationStatus;

        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 查询exam_station_status表（考试-场次-考站状态表）中该考试该考场下status是否全为1，如果是，修改其状态值为2
     * @param $examId
     * @param $roomId
     * @param $examScreeningId
     * @param DrawlotsRepository $draw
     * @return bool
     * @throws \Exception
     *
     * @author Zhoufuxiang <zhoufuxiang@163.com>
     * @date   2016-06-14 15:00
     * @copyright 2013-2015 MIS 163.com Inc. All Rights Reserved
     */
    public function modifyExamStationStatus($examId, $roomId, $examScreeningId, DrawlotsRepository $draw)
    {
        try{
            //查询该考试 该考场下的所有考站信息
            $stationArr = $draw->getStationNum($examId, $roomId, $examScreeningId);
            if(!$stationArr->isEmpty()) {
                //查询exam_station_status表（考试-场次-考站状态表）中该考试该考场下status是否全为1，如果是，修改其状态值为2

                //如果已经有状态为2了，那么就让他为2
//                $examStationStatus = $this->where('exam_id', $examId)
////                    ->where('status', '=', 2)
//                    ->whereIn('station_id', $stationArr)
//                    ->get();
//                    if (!is_null($examStationStatus)) {
//                        $examStationStatus->status = 2;
//                    } else {
//                        $examStationStatus->status = 1;
//                    }
//
//                    if (!$examStationStatus->save()) {
//                        //TODO 与安卓商量如果报错，就不刷新页面
//                        throw new \Exception('网络故障', -112);
//                    }
//
               

                /***开始********腕表推送********结束***/

                //查询对应考场下的考站，已经准备好了的 个数
                $examStationStatusData = $this->where('exam_id', $examId)
                    ->where('status', '=', 1)
                    ->whereIn('station_id', $stationArr)
                    ->get();
                //考场下 所有考站都已经准备好了，就将所有考站的准备状态改为2
                if (count($examStationStatusData) == $stationArr->count()) {
                    foreach ($examStationStatusData as $item){
                        $item->status = 2;
                        if(!$item ->save()){
                            throw new \Exception('修改老师全部准备好失败',-100);
                        }
                    }
//                    $this->where('exam_id', $examId)->whereIn('station_id', $stationArr)->update(['status' => 2]);
                }
                \Log::info('老师准备',[$stationArr]);
            }

            return true;

        }catch (\Exception $ex)
        {
            throw $ex;
        }
    }
}