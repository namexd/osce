<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 13:57
 */

namespace Modules\Msc\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\ProfessionalTitle;
use Modules\Msc\Http\Controllers\MscController;
use Pingpong\Modules\Routing\Controller;
use URL;
use DB;
class ProfessionalTitleController extends MscController
{

    /**
     *职称列表
     * @method GET
     * @url /msc/admin/professionaltitle/job-title-index
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        keyword       专业名称
     * * int           status        专业状态(1：正常，2：停用)
     * @return  view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */

    public function  getJobTitleIndex(Request $request)
    {
        $this->validate($request, [
            'status' => 'sometimes|in:1,2,3'
        ]);

        $keyword = urldecode(e($request->input('keyword')));
        $status = (int)$request->input('status',3);
        $tabulate = new ProfessionalTitle();
        $pagination =  $tabulate->getJobTitleList($keyword, $status);
        $list = [];
        foreach ($pagination as $itme) {
            $list[] = [
                'id' => $itme->id,
                'name' => $itme->name,
                'description' => $itme->description,
                'status' => is_null($itme->status) ? '-' : $itme->status,
            ];
        }

//        dd($list);

        return view('msc::admin.systemtable.title_table',[
            'pagination'=>$pagination,
            'list'         =>       $list,
            'keyword'=>$request->input('keyword')?$request->input('keyword'):'',
            'status'=>$request->input('status')?$request->input('status'):'',
            'number'=>$this->getNumber()
        ]);
    }


    /**
     * 新增职称
     *
     * @method post
     * @url /msc/admin/professionaltitle/holder-add
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name       设备名(必须的)
     * *string         detail     职称说明(必须的))
     * * int            status       状态(必须的)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */

    public function postHolderAdd(Request $request){
        $this->validate($request,[
            'name'   => 'required|max:20',
            'description'   =>  'required|max:50',
            'status' =>   'required|in:0,1'
        ]);
        $data=[
            'name'=>Input::get('name'),
            'description'=>Input::get('description'),
            'status'=>Input::get('status'),
        ];
        $ResourcesAdd= ProfessionalTitle::create($data);
        if($ResourcesAdd != false){
            return redirect()->back()->withInput()->withErrors('添加成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }

    /**
     * 编辑回显职称暂时没有用
     *
     * @method post
     * @url /msc/admin/professionaltitle/holder-edit
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        name       设备名(必须的)
     * *string         detail     职称说明(必须的))
     * * int            status       状态(必须的)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public  function getHolderEdit($id){
        $ResourcesId = intval($id);
        $Resources= ProfessionalTitle::findOrFail($ResourcesId);
        $data=[
            'name'   =>   $Resources['name'],
            'detail'   =>   $Resources['detail'],
            'status' =>    $Resources['status']
        ];
        die(json_encode($data));
    }

    /** @url /msc/admin/professionaltitle/holder-save
     * Created by PhpStorm.
     * User: zhouqiang
     * Date: 2015/12/30 0028
     * Time: 13:01
     * 修改职称
     */

    public  function postHolderSave(Request $request){
        $this->validate($request,[
            'id' => 'sometimes|min:0|max:10',
            'name'   => 'required|max:20',
            'description'   =>  'required|max:50',
            'status' =>   'required|in:0,1,2'
        ]);
        $data=[
            'name'=>Input::get('name'),
            'description'=>Input::get('description'),
            'status'=>Input::get('status'),
        ];
        $Save = DB::connection('msc_mis')->table('professional_title')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if( $Save != false){
            return redirect()->back()->withInput()->withErrors('修改成功');
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }
    /** @url /msc/admin/professionaltitle/holder-status
     * Created by PhpStorm.
     * User: zhouqiang
     * Date: 2015/12/30 0028
     * Time: 13:01
     * 修改职称状态
     */

    public function getHolderStatus(ProfessionalTitle $professionalTitle){
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = $professionalTitle->where('id','=',$id)->update(['status'=>Input::get('type')]);
            if($data != false){
                if(Input::get('type') == 1){
                    return redirect()->back()->withInput()->withErrors('启用成功');
                }else{
                    return redirect()->back()->withInput()->withErrors('停用成功');
                }
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }
    /**
     *职称删除
     * @method get
     * @url /msc/admin/professionaltitle/holder-remove
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * *int   ID    (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     */
    public  function getHolderRemove(){
//        echo'22222';exit;
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('professional_title')->where('id','=',$id)->delete();
            if($data != false){
                return redirect()->back()->withInput()->withErrors('删除成功');
            }else{
                return redirect()->back()->withInput()->withErrors('系统异常');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('系统异常');
        }
    }




}