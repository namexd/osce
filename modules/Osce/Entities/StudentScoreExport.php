<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25 0025
 * Time: 15:32
 */

namespace Modules\Osce\Entities;

use Doctrine\Common\Collections\Collection;
use Maatwebsite\Excel\Files\NewExcelFile;

class StudentScoreExport extends NewExcelFile
{
    /**
     * 返回该文件的文件名
     * @return string
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-25 15:30
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function getFilename()
    {
        return 'StudentScore';
    }

    /**
     * 将对象数据转为数组
     * @param Collection $collection
     * @return array
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-25 15:40
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function objToArray($datas, $exam_id = null)
    {
        if ($datas->isEmpty()) {
            throw new \Exception('当前没有考试成绩！无法导出Excel文件！');
        }

        //获取表头数据
        $ExamResult = new ExamResult();
        $header = $ExamResult->getScoreHeader($exam_id);

        if (empty($header)) {
            throw new \Exception('该考试没有安排！无法导出Excel文件！');
        }

        /*
         * 该数组为返回的具体数据
         * 第一行为行头
         */
        $title  = config('osce.student_score.title');
        $title  = array_merge($title, $header);
        $title[]= '总分';
        //键值对换（用于 从对应的值 获取其序号）
        $trans  = array_flip($title);
        $data[] = $title;  //获取表头数据（做第一条数据）

        //取出班级学年制，作为sheet
        $grade_class = array_unique($datas->pluck('grade_class')->toArray());   //取出班级学年制，转为数组，去重
        sort($grade_class);     //重新排序
        foreach ($datas as $index => $value) {
            //获取站对应的序号
            $order = $trans[$value->flow_name];
            //判断数组中是否已经有该学生的数据
            if (in_array($value->student_id, array_keys($data)))
            {
                $data[$value->student_id][$order]   = round($value->score,2);   //把成绩放在对应的站序号下
            } else
            {
                $data[$value->student_id]           = array_fill(0, 10, '');    //填充数组，（默认值全为空）
                $data[$value->student_id][0]        = $value->grade_class;      //序号0：学年制
                $data[$value->student_id][1]        = $value->code;             //序号1：学号
                $data[$value->student_id][2]        = $value->student_name;     //序号2：学生姓名
                $data[$value->student_id][$order]   = round($value->score,2);   //对应考站下的分数
            }
        }

        //获取成绩科数长度
        $index = count($title)-1;
        //学生成绩求和
        foreach ($data as $key => &$item) {
            //去掉表头
            if($key == 0){
                continue;
            }
            //初始总分为0
            $item[$index] = 0;
            //循环取出各科成绩，求总和
            for ($i = 3; $i<$index; $i++){
                $item[$index] += $item[$i];   //求单个学生成绩总和
            }
        }
        unset($index);  //释放变量

        return [$data, $grade_class];
    }

    /**
     * 将对象数据转为数组《二》
     * @param $datas
     * @param null $exam_id
     * @return array
     * @throws \Exception
     *
     * @author fandian <fandian@sulida.com>
     * @date   2016-05-25 17:40
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public function objToArray2($datas, $exam_id = null)
    {
        //获取表头数据
        $ExamResult = new ExamResult();
        $header = $ExamResult->getScoreHeader($exam_id);

        if (empty($header)) {
            throw new \Exception('当前没有考试成绩！，无法导出xlxs文件！');
        }

        //查询出考试安排以考场分组
        $examRoomList= ExamDraft::leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
                        ->leftJoin('room','exam_draft.room_id','=','room.id')
                        ->leftJoin('station','exam_draft.station_id','=','station.id')
                        ->where('exam_draft_flow.exam_id', '=', $exam_id)
                        ->select([
                            'exam_draft.room_id as room_id',
                            'room.name as room_name',
                            'exam_draft.station_id as station_id',
                            'station.type as station_type',
                        ])
                        ->groupBy('exam_draft.station_id')
                        ->get();
        $title[] = '总分';
        foreach ($examRoomList as $item) {
            if($item->station_type == 2){
                $title[8] = 'sp考场考试';
            }else{
                $title[$item->room_id] = $item->room_name;
            }
        }
//        $title[] = '考试名称';
        $title[] = '学生姓名';
        $title[] = '学号';
        $title[] = '学年制';
        $data[]  = $title;  //获取表头数据（做第一条数据）
        //键值对换
        $trans = array_flip($title);
        foreach ($datas as $index => &$value)
        {
            foreach ($examRoomList as $examRoom){
                if($examRoom->station_type == 2){
                    $examRoom->room_name = 'sp考场考试';
                }
                if($value->station_id == $examRoom->station_id){
                    $value ->room_id = $examRoom->room_id;
                    $value ->room_name = $examRoom->room_name;
                }
            }

            //获取考站对应的序号
            $order = $trans[$value->room_name];
            //判断数组中是否已经有该学生的数据
            if(in_array($value->station_id, $trans))
            {
                $data[$value->student_id][$order] = $value->score;  //把成绩放在对应的考站序号下
            }
            else
            {
                $data[$value->student_id][10]     = $value->code;           //学号
                $data[$value->student_id][9]      = $value->student_name;   //学生姓名
//              $data[$value->student_id][28]     = $value->exam_name;      //考试名称
                $data[$value->student_id][11]     = $value->grade_class;    //学年制
                $data[$value->student_id][$order] = $value->score;  //对应考站下的分数
            }
        }
        foreach ($data as &$item){
            //把键值组装成一个数组
            $keyData = [];
            foreach ($item as  $k =>$examData){
                $keyData []=$k;
            }
            //判断数组中是否有这个键
            $newData = array_diff($trans, $keyData);
            if(!empty($newData)){
                foreach ($newData as $newKeyData){
                    $item[$newKeyData] ='';
                }
            }
            krsort($item);
        }
        return $data;
    }

}