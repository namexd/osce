<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:21
 */

namespace Modules\Osce\Entities\ExamArrange;


use Illuminate\Database\Eloquent\Collection;
use Modules\Osce\Entities\ExamArrange\Traits\SqlTraits;
use Modules\Osce\Entities\ExamArrange\Traits\SundryTraits;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamDraftFlow;

class ExamArrangeRepository extends AbstractExamArrange
{
    use SqlTraits, SundryTraits;

    /**
     * 返回实体类的类名，带命名空间
     * @method GET
     * @url /msc/admin/resources-manager/路径名/getModel
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getModel()
    {
        return 'Modules\Osce\Entities\ExamArrange\ExamArrange';
    }

    /**
     * 检查传入的数据的合理性
     * @author Jiangzhiheng
     * @time 2016-04-14 17:27
     */
    function checkData($examId, $field = 'station_id')
    {
        if ($field == 'station_id') {
            //获取该场考试的数据
            $data = $this->checkExamArrange($examId);
            //打包数据，用考试阶段来打包
            $result = $data->groupBy('exam_gradation_id');

            //遍历之，查看其中是否有相同的考站
            $this->checkSameEntity($result, $field);

            return $data;
        } elseif ($field == 'room_id') {
            //打包数据，用考试阶段来打包
            $data = $this->checkExamArrange($examId);
            $result = $data->groupBy('exam_draft_flow_order');

            //遍历之，查看其中是否有相同的考场
            $this->checkSameEntity($result, $field);
            $this->checkSameRoom($result);
            
            return $data;
        } else {
            throw new \Exception('系统异常');
        }
    }


    /**
     * 清空考试安排
     * @author zhouqiang
     * @time 2016-04-14
     */
    public function getExamManner($exam_id)
    {
        if ($this->model->getEmptyExamArrange($exam_id)) {
            if ($this->model->getTeacherArrange($exam_id)) {
                if ($this->model->getTeacherInvite($exam_id)) {
                    //清除智能排考
                    $this->model->resetSmartArrange($exam_id);
                }
            }
        }
        return true;
    }


    public  function resetSmartArrange($exam_id){
        try{
            if($this->model->resetSmartArrange($exam_id)){
                return true;
            }
           
        }catch (\Exception $ex){
            throw $ex;
        }



    }
    

    //判断考场安排数据是否修改

    public function getInquireExamArrange($exam_id){

        $ExamDraftFlowData = ExamDraftFlow::where('exam_id','=',$exam_id)->get();
        $FlowId = $ExamDraftFlowData->pluck('id');
        $ExamDraftFlowData= $ExamDraftFlowData->toArray();
        $ExamDraft =  ExamDraft::whereIn('exam_draft_flow_id',$FlowId)->get()->toArray();
        return [ 'FlowData'=>$ExamDraftFlowData ,'DraftData'=>$ExamDraft];
    }

    //考试安排数据的差异
    public  function getDataDifference($exam_id,$FrontArrangeData,$LaterArrangeData){
        $result = '';

        if(empty($FrontArrangeData['FlowData'])){
            //说明是刚新增就就不弹框直接保存
            $result = false;
        }else{

            //比较小站数据条数是否一致
            if(count($FrontArrangeData['DraftData'])!=count($LaterArrangeData['DraftData'])){
                $result = true;
            }else{
                //比较小站里的数据是否一致
                foreach ($FrontArrangeData['DraftData'] as $key=>$item){
                    foreach ($LaterArrangeData['DraftData'] as $keys=>$value){
                        if($key == $keys && $item['subject_id'] != $value['subject_id']){

//
//                            dd($FrontArrangeData['DraftData'],$LaterArrangeData['DraftData']);
//
//                            dd($item['subject_id'],$value['subject_id']);
                            $result = true;
                        }elseif($key == $keys && $item['station_id'] != $value['station_id']){

                            $result = true;
                        }elseif($key == $keys && $item['room_id'] != $value['room_id']){

                            $result = true;
                        }
                    }
                }

                if(count($FrontArrangeData['FlowData'])!=count($LaterArrangeData['FlowData'])){

                    $result = true;
                }else{
                    //比较阶段是否一致
                    foreach ($FrontArrangeData['FlowData'] as $key=>$item){
                        foreach ($LaterArrangeData['FlowData'] as $keys=>$value){
                            if($key == $keys && $item['exam_gradation_id'] != $value['exam_gradation_id']){
                                $result = true;
                            }
                        }
                    }

                }


            }
        }
        return $result;

    }


}