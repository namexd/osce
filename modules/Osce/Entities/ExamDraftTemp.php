<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/6 0006
 * Time: 16:31
 */
namespace Modules\Osce\Entities;
class ExamDraftTemp extends CommonModel{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft_temp';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'station_id', 'exam_id', 'room_id', 'subject_id',  'ctrl_type', 'old_draft_id',
        'old_draft_flow_id', 'used', 'user_id', 'add_time'
    ];
    public    $search       = [];


    /**
     * 清空考场安排临时表数据
     * @param $id
     * @return bool
     */
    public function getTempData($id){
        $delExamDraftTemp = $this->where('exam_id','=',$id)->delete();
        if($delExamDraftTemp || $delExamDraftTemp == 0){

            $ExamDraftFlow = ExamDraftFlowTemp::where('exam_id','=',$id)->delete();

            if($ExamDraftFlow || $ExamDraftFlow==0){
                 return true;
            }
        }
        return false;
    }

    /**
     * 处理 待删除 数据
     * @param $exam_id
     * @author Zhoufuxiang 2016-4-11
     * @throws \Exception
     */
    public function handleDelDatas($exam_id){
        try{
            //1、 清空临时表数据
            $tempData = $this->getTempData($exam_id);
            if(!$tempData){
                throw new \Exception('清空数据失败');
            }
            //2、删除正式表中 待删除数据
            $examDrafts = ExamDraft::where('status','=',1)->get();
            if (count($examDrafts)>0){
                foreach ($examDrafts as $examDraft) {
                    if (!$examDraft->delete()){
                        throw new \Exception('删除小表待删除数据失败');
                    }
                }
            }
            $draftFlows = ExamDraftFlow::where('status','=',1)->get();
            if ($draftFlows){
                foreach ($draftFlows as $draftFlow) {
                    if (!$draftFlow->delete()){
                        throw new \Exception('删除大表待删除数据失败');
                    }
                }
            }
            return true;

        } catch (\Exception $ex){
            throw $ex;
        }
    }
}