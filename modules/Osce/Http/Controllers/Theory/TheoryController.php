<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 19:50
 */

namespace Modules\Osce\Http\Controllers\Theory;


use Illuminate\Http\Request;

class TheoryController extends Controller
{
    /**
     * 着陆页
     */
    public function getIndex(Request $request)
    {
        try {
            //$data = $billboardRepository->getData($request->input('exam_id'));
            return view('osce::billboard.index');

        } catch (\Exception $ex) {
            return \Redirect::back()->withErrors($ex->getMessage());
        }
    }

    /**
     * 获取学生数据，以及该学生应去的下一考场
     * @url osce/billboard/student
     * @access public
     * @param Request $request
     * @param BillboardRepository $billboardRepository
     * @请求字段：
     * @return mixed
     * @version
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-05
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getStudent(Request $request, BillboardRepository $billboardRepository)
    {
        $this->validate($request, [
            'exam_id'       => 'required|integer',
            'station_id'    => 'required|integer'
        ]);
        $examId    = $request->input('exam_id');
        $stationId = $request->input('station_id');
        try {
            $studentData = $billboardRepository->getStudent($examId, $stationId);
            $student     = Student::find($studentData->student_id);
            $studentId   = $studentData->student_id;

            //返回学生信息和考场名字
            $data = $billboardRepository->getRoomData($examId, $studentId, $studentData->room_id);

            //若数据为空
            if(is_null($data))
            {
                $data  = $studentData;
                $data->student_name = $student->name;
                $data->room_name    = '';
            }
            return \Response::json($this->success_data($data));

        } catch (\Exception $ex) {
            return \Response::json($this->fail($ex));
        }
    }
    
}