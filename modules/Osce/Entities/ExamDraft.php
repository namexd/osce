<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/7 0007
 * Time: 15:36
 */

namespace Modules\Osce\Entities;


use Modules\Osce\Entities\QuestionBankEntities\ExamPaperExamStation;
use Modules\Osce\Repositories\Common;

class ExamDraft extends CommonModel
{
    protected $connection   = 'osce_mis';
    protected $table        = 'exam_draft';
    public    $timestamps   = true;
    protected $primaryKey   = 'id';
    public    $incrementing = true;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = ['station_id', 'room_id', 'subject_id',  'exam_draft_flow_id', 'status', 'effected'];

    protected $ctrl_type    = [
        1   => '简单新增',
        2   => '简单更新',
        3   => '新增后更新',
        4   => '新增后，小表新增',
        5   => '删除',
        6   => '大表新增后，小表新增后更新',
    ];

    public function subject(){
        return $this->hasOne('Modules\Osce\Entities\Subject','id','subject_id');
    }

    public function station(){
        return $this->hasOne('Modules\Osce\Entities\Station','id','station_id');
    }

    public function paper()
    {
        return $this->hasMany('Modules\Osce\Entities\ExamPaperStation', 'station_id', 'station_id');
    }

	public function room()
    {
        return $this->hasOne('\Modules\Osce\Entities\Room', 'id', 'room_id');
    }

    public function scopeScreening($query)
    {
        return $query->join('exam_draft_flow', 'exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id')
            ->join('exam_gradation', 'exam_gradation.id', '=', 'exam_gradation_id')
            ->join('exam_screening', 'exam_screening.gradation_order', '=', 'exam_gradation.order');
    }
    
    /**
     * 获取 不为null的值
     * @param $object
     * @param $value
     * @param $item
     *
     * @author fandian 2016-04-14
     * @return object
     */
    private function getNotNullValue($object, $value, $item){
        return Common::getNotNullValue($object, $value, $item);
    }

    /**
     * 判断 值 是否为空
     * @param $value
     * @param $code
     * @param $message
     *
     * @author fandian 2016-04-14
     * @return bool
     * @throws \Exception
     */
    private function judgeValueIfNull($value, $code, $message){
        return Common::valueIsNull($value, $code, $message);
    }



    public function getExamDraftData($ExamDraftFlowId){
        $examDraftList=$this->leftJoin('station', function($join){
            $join -> on('exam_draft.station_id', '=', 'station.id');
        })->leftJoin('room', function($join){
            $join -> on('exam_draft.room_id', '=', 'room.id');
        })->leftJoin('subject', function($join){
            $join -> on('exam_draft.subject_id', '=', 'subject.id');
        })->leftJoin('exam_draft_flow', function($join){
            $join -> on('exam_draft.exam_draft_flow_id', '=', 'exam_draft_flow.id');
        })
            ->whereIn('exam_draft_flow_id',$ExamDraftFlowId)
            ->select([
                'exam_draft.id as id',
                'exam_draft.station_id as station_id',
                'station.type as station_type',
                'station.name as station_name',
                'exam_draft.room_id as room_id',
                'room.name as room_name',
                'exam_draft.subject_id as subject_id',
                'subject.title as subject_name',
                'exam_draft.exam_draft_flow_id as exam_draft_flow_id',
                'exam_draft.effected as effected',
                'exam_draft_flow.optional',
                'exam_draft_flow.number',
            ])
            ->get()
            ->toArray();

        return $examDraftList;
    }

    /**
     * 处理小表（站下面的项）数据
     * @param $data
     * @return \Exception
     */
    public function handleSmallData($data){
        try{
            $value = $data['item']->ctrl_type;
            switch ($value){
                case 1 : $this->smallOne($data);            //简单新增
                    break;
                case 2 : $this->smallTwo($data);            //简单更新
                    break;
                case 3 : $this->smallThree($data);          //新增后更新
                    break;
                case 5 : $this->smallFive($data);           //删除
                    break;
                case 4 :
                case 6 : $this->smallFour($data);            //大表新增后，小表新增后更新
                    break;
                default: throw new \Exception('操作有误！');
            }
            return $data;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 简单新增
     * @param $data
     * @return \Exception|int
     */
    public function smallOne($data){
        try{
            $item      = $data['item'];
            $draftData = [
                'station_id'        => $item->station_id,
                'room_id'           => $item->room_id,
                'exam_draft_flow_id'=> $item->old_draft_flow_id,
                'subject_id'        => $item->subject_id,
                'effected'          => $item->effected,
            ];

            $result = ExamDraft::create($draftData);
            if(!$result)
            {
                throw new \Exception('添加站下的考项失败，请重试！');
            }
            //将保存后的ID，存入临时数据中
            $item->old_draft_id = $result->id;
            if(!$item->save())
            {
                throw new \Exception('添加对应站ID失败，请重试！');
            }

            return $item;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 简单更新(已经保存过后了的)
     * @param $data
     * @return \Exception|int
     */
    public function smallTwo($data){
        try{
            $item      = $data['item'];
            $draft_id  = $item->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            $this->judgeValueIfNull($examDraft, -201, '数据有误，请重试！');       //判断值是否为null

            //获取 不为null的值
            $examDraft = $this->getNotNullValue($examDraft, 'station_id', $item);
            $examDraft = $this->getNotNullValue($examDraft, 'room_id',    $item);
            $examDraft = $this->getNotNullValue($examDraft, 'effected',   $item);
            $examDraft = $this->getNotNullValue($examDraft, 'subject_id', $item);
            //理论考站，考试项目 另作处理
            if (!is_null($examDraft->station_id)){
                $Station = Station::where('id','=',$examDraft->station_id)->first();
                if (!is_null($Station) && $Station->type == 3){

                    $examDraft->subject_id = $item->subject_id;
                }
            }
            
            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return $examDraft;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 新增后更新(已经保存过后了的)
     * @param $data
     * @return \Exception|int
     */
    public function smallThree($data){
        try{
            $item      = $data['item'];
            $draftTemp = ExamDraftTemp::where('id','=',$item->id)->first();
            $this->judgeValueIfNull($draftTemp, -202, '数据有误，请重试！');       //判断值是否为null

            $draft_id  = $draftTemp->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            $this->judgeValueIfNull($examDraft, -203, '数据有误，请重试！');       //判断值是否为null

            //获取 不为null的值
            $examDraft = $this->getNotNullValue($examDraft, 'station_id', $item);
            $examDraft = $this->getNotNullValue($examDraft, 'room_id',    $item);
            $examDraft = $this->getNotNullValue($examDraft, 'effected',   $item);
            $examDraft = $this->getNotNullValue($examDraft, 'subject_id', $item);
            //理论考站，考试项目 另作处理
            if (!is_null($examDraft->station_id)){
                $Station = Station::where('id','=',$examDraft->station_id)->first();
                if (!is_null($Station) && $Station->type == 3){

                    $examDraft->subject_id = $item->subject_id;
                }
            }

            if(!$examDraft->save())
            {
                throw new \Exception('更新站下的考项失败，请重试！');
            }
            return $examDraft;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 新增后，小表新增（未保存）
     * @param $data
     * @return \Exception|int
     */
    public function smallFour($data){
        try{
            $item   = $data['item'];
            $draft_flow_temp_id = $item->old_draft_flow_id;
            $exam_draft_flow_id = ExamDraftFlowTemp::where('id','=',$draft_flow_temp_id)->first();
            $this->judgeValueIfNull($exam_draft_flow_id, -204, '数据有误，请重试！');       //判断值是否为null
            $this->judgeValueIfNull($exam_draft_flow_id->exam_draft_flow_id, -205, '排序有误，请重试！');       //判断值是否为null

            $draftData = [
                'station_id'        => $item->station_id,
                'room_id'           => $item->room_id,
                'exam_draft_flow_id'=> $exam_draft_flow_id->exam_draft_flow_id,
                'subject_id'        => $item->subject_id,
                'effected'          => $item->effected,
            ];

            $result = ExamDraft::create($draftData);
            if(!$result)
            {
                throw new \Exception('添加站下的考项失败，请重试！');
            }
            return $result;

        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 删除
     * @param $data
     * @return \Exception|int
     */
    public function smallFive($data){
        try{
            $item      = $data['item'];
            //重新查找对应的这条数据（再赋给$item）
            $newItem   = ExamDraftTemp::where('id','=',$item->id)->first();
            //再获取对应的正式表的id
            $draft_id  = $newItem->old_draft_id;
            $examDraft = ExamDraft::where('id','=',$draft_id)->first();
            $this->judgeValueIfNull($examDraft, -206, '数据有误，请重试！');       //判断值是否为null

            //再删除正式表中对应ID的那条数据
            $examDraft->status = 1;         //软删除
            if(!$examDraft->save()){
                throw new \Exception('删除失败，请重试！');
            }

        } catch (\Exception $ex){
            throw $ex;
        }
    }


    /**
     * 查询考场安排数据
     * @param $exam_id
     * @return mixed
     */
    public function getDraftFlowData($exam_id){
        $data = $this->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', $this->table.'.exam_draft_flow_id')
                ->leftJoin('station', 'station.id', '=', $this->table.'.station_id')
                ->leftJoin('subject', 'subject.id', '=', $this->table.'.subject_id')
                ->leftJoin('subject_standard', 'subject.id', '=','subject_standard.subject_id')
//                ->leftJoin('station_teacher', 'station_teacher.station_id', '=', $this->table.'.station_id')
                ->where('exam_draft_flow.exam_id','=',$exam_id)
                ->select([
                    'exam_draft.id', 'exam_draft.subject_id',
                    'subject.title as subject_title', 'subject.id as subject_id',
                    'station.id as station_id', 'station.name as station_name', 'station.type as station_type',
                    'exam_draft_flow.exam_gradation_id as exam_gradation_id',
                    'exam_draft_flow.exam_screening_id as exam_screening_id',
                    'subject_standard.standard_id as standard_id',
                ])
//              ->groupBy('subject.id')
                ->get();

        return $data;
    }

    /**
     * 理论考站，处理考试、试卷、考站关系
     * @param $item
     *
     * @author fandian 2016-04-18
     * @return array|static
     * @throws \Exception
     */
    public function handleExamPaperStation($exam_id)
    {
        //查询考试对应理论考站、试卷数据
        $result = ExamPaperExamStation::where('exam_id','=',$exam_id)->select('station_id')
                ->get()->pluck('station_id')->toArray();

        $stations = $this->leftJoin('station','station.id','=','exam_draft.station_id')
                    ->leftJoin('exam_draft_flow','exam_draft_flow.id','=','exam_draft.exam_draft_flow_id')
                    ->where('exam_draft_flow.exam_id','=',$exam_id)
                    ->where('station.type','=',3)
                    ->select(['exam_draft.station_id'])
                    ->get()->pluck('station_id')->toArray();

        $delStations = array_diff($result, $stations);     //原来有，现在不具有（需删除）
        $addStations = array_diff($stations, $result);     //现在有，原来不具有（需添加）

        //删除考试、试卷、考站关系
        if (!empty($delStations)){
            $delPaperStiations = ExamPaperExamStation::where('exam_id','=',$exam_id)->whereIn('station_id',$delStations)->get();
            if (!$delPaperStiations->isEmpty()){
                foreach ($delPaperStiations as $item) {
                    if (!$item->delete()){
                        throw new \Exception('删除考试试卷考站关系失败，请重试！');
                    }
                }
            }
        }

        //添加考试、试卷、考站关系
        if (!empty($addStations)){
            foreach ($addStations as $station_id) {
                $Station = Station::where('id','=',$station_id)->first();
                $paper_exam_station = [
                    'exam_id'       => $exam_id,
                    'exam_paper_id' => $Station->paper_id,
                    'station_id'    => $Station->id,
                ];
                $result = ExamPaperExamStation::create($paper_exam_station);
                if (!$result){
                    throw new \Exception('添加考试试卷考站关系失败，请重试！');
                }
            }
        }

        return $result;
    }

    /**
     * 当前考试信息，  //对应考试中room_id下所有考站
     * @method GET
     * @access public
     * @param examId 考试id
     * @param stationId 考试对应的考站id
     * @param $screenId 考试对应场次id
     * @author wt <wangtao@sulida.com>
     * @date 2016-5-3
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamMsg($exam_id, $room_id, $screenId)
    {
        //对应考试中room_id下所有考站 TODO: fandian 2016-06-12 加入缓存，一天
        $key = 'exam_'.$exam_id.'_roomId_'.$room_id.'_screenId_'.$screenId;

        \Cache::remember($key, 1440, function () use($exam_id, $room_id, $screenId)
        {
            return $this->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->leftJoin('exam_gradation', 'exam_gradation.id', '=', 'exam_draft_flow.exam_gradation_id')
                ->leftJoin('exam_screening', 'exam_screening.gradation_order', '=', 'exam_gradation.order')

                ->where('exam_draft_flow.exam_id', '=', $exam_id)
                ->where('exam_gradation.exam_id', '=', $exam_id)
                ->where('exam_screening.exam_id', '=', $exam_id)
                ->where('exam_screening.id', '=', $screenId)
                ->where('exam_draft.room_id', '=', $room_id)
                ->select(['exam_draft_flow.name','exam_screening.id','exam_draft.station_id','exam_draft.subject_id'])
                ->get();
        });
        return \Cache::get($key);
    }

    static public  function  getExamRoom($examId,$examscreeningId ,$stationId)
    {
        //拿到实例
        $examscreening = ExamScreening::find($examscreeningId);
        //拿到阶段id
        $examgradationId = ExamGradation::where('exam_id','=',$examId)->where('order','=',$examscreening->gradation_order)->first();
        //拿到阶段考站id
        $stationGradationId = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_gradation_id','=',$examgradationId->id)
            ->get()
            ->pluck('station_id')->toArray();
         //拿到老师支持的考站和阶段考站id的交集
        $station_id = array_intersect($stationId, $stationGradationId);
         
         $roomId =ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
             ->where('exam_draft_flow.exam_gradation_id','=',$examgradationId->id)
             ->whereIn('exam_draft.station_id',$station_id)
             ->first();
         if(is_null($roomId)){
             throw  new \Exception('获取老师所对应的考场失败');
         }
             
        return $roomId;
    }

    /**
     * 获取考站组
     * @param string $exam_id
     * @return array
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-04-14 21:12
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getExamAllStations($exam_id = '')
    {
        $stations = [];
        //拿到考试下所有的考站
        $stationIds = $this->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id');
        //根据考试ID筛选
        if(!empty($exam_id)){
            $stationIds = $stationIds->where('exam_draft_flow.exam_id', '=', $exam_id);
        }
        $stationIds = $stationIds->select('exam_draft.station_id')->groupBy('exam_draft.station_id')->get();

        if(!$stationIds->isEmpty())
        {
            foreach($stationIds as $station){
                $stations[] = $station->station;
            }
        }

        return $stations;
    }

    /**
     * 获取老师在当前场次下支持的考站，考站所在考场，考场、场次下所有考站
     * @param $user_id
     * @param $exam_id
     * @param $screening_id
     * @return array（$stationId, $roomStations）
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-02 15:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStationAndRoom($user_id, $exam_id, $screening_id)
    {
        $StationTeacher = new StationTeacher();
        //获取老师、在当前场次下支持的考站($stationId)，以及 当前场次下的所有考站($screenStations)
        list($stationId, $screenStations) = $StationTeacher->getTeacherStation($screening_id, $exam_id, $user_id);
        \Log::info('获取到老师当前场次支持的考站',[$stationId,$screenStations]);
        //根据考站 获取其所在 考场(唯一)
        $room = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->where('exam_draft.station_id',   '=', $stationId)
                ->where('exam_draft_flow.exam_id', '=', $exam_id)
                ->first();

        //根据考场 获取考场下、对应场次下的 所有考站
        $roomStations = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                    ->where('exam_draft.room_id', '=', $room->room_id)
                    ->whereIn('exam_draft.station_id', $screenStations)
                    ->where('exam_draft_flow.exam_id', '=', $exam_id)
                    ->get();

        return array($stationId, $roomStations);
    }

    /**
     * 获取对应考试的 对应场次的 所有考场
     * @param $exam_id
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-13 14:00
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getRoomByExam($exam_id, $screening_id)
    {
        $rooms = $this->leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                      ->leftJoin('exam_gradation', 'exam_gradation.id', '=', 'exam_draft_flow.exam_gradation_id')
                      ->leftJoin('exam_screening', 'exam_screening.gradation_order', '=', 'exam_gradation.order')
                      ->where('exam_screening.id', '=', $screening_id)
                      ->where('exam_draft_flow.exam_id', '=', $exam_id)
                      ->groupBy('exam_draft.room_id')
                      ->select(['exam_draft.room_id'])->get();
        if($rooms->isEmpty()){
            throw new \Exception('该考试没有安排考场');
        }
        return $rooms;
    }

    /**
     * 获取对应考试的 所有科目
     * @param $exam_id
     * @return
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date   2016-06-16
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public  function  getExamSubject($exam_id){

        $examSubject= ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft_flow.exam_id', '=', $exam_id)
            ->select([
                'exam_draft.subject_id as subject_id',
            ])
            ->groupBy('exam_draft.station_id')
            ->get();
        if($examSubject->isEmpty()){
            throw new \Exception('获取考试下的科目安排失败');
        }
        return $examSubject;
    }

    /**
     * 根据 考试项目、考试 获取对应考站数组
     * @param $subjectid
     * @param $elderExam_ids
     * @return mixed
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-06-29 14:13
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getStationBySubjectExam($subjectid, $elderExam_ids)
    {
        $result = ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                ->whereIn('exam_draft_flow.exam_id', $elderExam_ids)
                ->select('exam_draft.station_id');
        if(is_array($subjectid)){
            $result = $result->whereIn('exam_draft.subject_id', $subjectid);
        }
        elseif(!empty($subjectid)){
            $result = $result->where('exam_draft.subject_id', '=', $subjectid);
        }

        return $result->get()->pluck('station_id')->toArray();
    }
}