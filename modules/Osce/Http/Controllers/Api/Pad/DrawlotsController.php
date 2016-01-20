<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/20
 * Time: 10:25
 */

namespace Modules\Osce\Http\Controllers\Api\Pad;


use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

class DrawlotsController extends CommonController
{
    public function getRoomId()
    {
        try {
            //首先得到登陆者信息
            $user = Auth::user();

            //获取登陆者id，也就是教师id
            $teacher_id = $user->id;
            //通过教师id去寻找对应的考场,返回考场对象
            $room = StationTeacher::where('user_id', $teacher_id)->first()->station->room;
            if ($room->isEmpty()) {
                throw new \Exception('未能查到该老师对应的考场！');
            }
            return response()->json($this->success_data($room->first()));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}