<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/28 0028
 * Time: 17:12
 */

namespace Modules\Msc\Http\Controllers\Admin;


use App\Repositories\Common;
use Modules\Msc\Entities\StdProfessional;
use Modules\Msc\Http\Controllers\MscController;
use Illuminate\Http\Request;

class ProfessionController extends MscController
{
   /**
    *专业列表
    * @method GET
    * @url /msc/admin/profession/profession-list
    * @access public
    *
    * @param Request $request get请求<br><br>
    * <b>post请求字段：</b>
    * * string        keyword       专业名称
    * * int           status        专业状态(1：正常，2：停用)
    * @return  view
    *
    * @version 1.0
    * @author zhouqiang <zhouqiang@misrobot.com>
    * @date ${DATE} ${TIME}
    * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
    */


   public function getProfessionList(Request $request){
      $this->validate($request, [
         'keyword '   =>    'sometimes',
         'status'  =>   'sometimes|in:1,2'
      ]);
      $keyword  =   urldecode(e($request->input('keyword ')));
      $status  = (int)$request->input('status');
      $profession = new StdProfessional();
      $pagination=$profession-> getprofessionList( $keyword,$status);
//       dd($pagination);
      $list=[];
      foreach($pagination as $itme){
         $list[] = [
             'id' => $itme->id,
             'name'  => $itme->name,
             'code'  => $itme->code,
//            'status' =>$itme->status,
              'status'    => is_null($itme->status) ? '-' : $itme->status,
         ];
      }
//       dd($list);
    return view('msc::admin.systemtable.major_table',[
        'list'         =>       $list,
    ]);
   }

    /**
     * 新增专业
     *
     * @method post
     * @url /msc/admin/profession/profession-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name       专业名字(必须的)
     * * int            code       专业代码(必须的)
     * * int            status       状态(必须的)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postProfessionAdd(Request $request){

    $this->validate($request,[
        'name'   => 'required|max:50',
        'code'   =>  'required|max:32',
        'status' =>   'required|in:1,2'
    ]);
        $data = $request->only(['name','code','status']);

        $profession = new StdProfessional();
        $result =$profession->postAddProfession($data);
        if($result){
            return redirect()->back()->withInput()->withErrors('新增成功');
        }

            return redirect()->back()->withInput()->withErrors('新增失败');
    }


    /**
     * 编辑回显专业
     * @method GET
     * @url /msc/admin/profession/profession-edit
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * int    id      (必须的)
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public  function   getProfessionEdit($id){

        $ProfessionId = intval($id);
        $Profession= StdProfessional::findOrFail($ProfessionId);
        $data=[
              'name'   =>   $Profession['name'],
              'code'   =>     $Profession['code'],
              'status' =>    $Profession['status']
        ];
          die(json_encode($data));
    }

    /**
     *提交编辑专业
     * @method post
     * @url /msc/admin/profession/profession-save
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * *int   ID    (必须的)
     * *string        name       专业名字(必须的)
     * * int          code       专业代码(必须的)
     * * int         status       状态(必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function postProfessionSave(Request $request){
        $this->validate($request,[
            'id' => 'sometimes|min:0|max:10',
            'name'   => 'required|max:50',
            'code'   =>  'required|max:32',
            'status' =>   'required|in:1,2'
        ]);
        $data = $request->only(['name','code','status']);
        $profession = new StdProfessional();
        $result =$profession->postSaveProfession($data);
        if ($result) {
            return redirect()->back()->withInput()->withErrors('修改成功');

        }
        return redirect()->back()->withInput()->withErrors('修改失败');
    }



    /**
     *改变专业的状态
     * @method get
     * @url /msc/admin/profession/profession-status/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * *int   ID    (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */


    public  function getProfessionStatus($id){


        $professionId = intval($id);

        $professionModel = new StdProfessional();

        $result = $professionModel->changeStatus($professionId );
        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );

    }

    /**
     *专业删除
     * @method get
     * @url /msc/admin/profession/profession-deletion/{id}
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * *int   ID    (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function  getProfessionDeletion($id){
        $id = intval($id);

        $professionModel = new StdProfessional();

        $result = $professionModel->SoftTrashed($id);

        if ($result) {
            return response()->json(
                ['success' => true]
            );
        }
        return response()->json(
            ['success' => false]
        );
    }

    /**
     * 导入专业表
     * @api post /msc/admin/profession/profession-import
     * @access public
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string       training       课程文件的excl(必须的)
     * @return object
     * @version 0.8
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date 2015-11-27 10:24
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */

    public function postProfessionImport(Request $request){
        try{
            $data = Common::getExclData($request, 'training');
            $professionInfo = array_shift($data);
            //将中文头转换翻译成英文
            $professionInfo = Common::arrayChTOEn($professionInfo, 'msc.importForCnToEn.profession_group');
            //已经存在的数据
            $dataHaven = [];
            //添加失败的数据
            $dataFalse = [];
            //判断是否存在这个专业
            foreach ($professionInfo as $professionData) {
                //处理状态status
                switch ( $professionData['status']){
                    case "正常":
                        $professionData['status'] = 1;
                        break;
                    case "停用":
                        $professionData['status'] = 2;
                        break;
                };
                if ($professionData['code'] && $professionData['name']) {
                    if (StdProfessional::where('code', '=', $professionData['code'])->count() == 0) {
                        $profession = new StdProfessional();
                        $result= $profession->ProfessionImport($professionData);

                        if ( $result== 0) {
                            $dataFalse[] = $professionData;
                        }
                    } else {
                        $dataHaven[] = $professionData;
                    }
                }
            }
            return response()->json(
                $this->success_data(['result' => true, 'dataFalse' => $dataFalse, 'dataHaven' => $dataHaven])
            );
        }
        catch (\Exception $e) {
            return response()->json($this->fail($e));
        }
    }
}