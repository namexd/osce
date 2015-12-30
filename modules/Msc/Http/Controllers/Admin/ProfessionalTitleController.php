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
use Pingpong\Modules\Routing\Controller;
use URL;
use DB;
class ProfessionalTitleController extends Controller
{

    /**
     *ְ���б�
     * @method GET
     * @url /msc/admin/professionaltitle/job-title-index
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        keyword       רҵ����
     * * int           status        רҵ״̬(1��������2��ͣ��)
     * @return  view
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function  getJobTitleIndex(Request $request)
    {
        $this->validate($request, [
            'keyword ' => 'sometimes',
            'status' => 'sometimes|in:1,2',
        ]);
        $keyword = urldecode(e($request->input('keyword ')));
        $status = (int)$request->input('status');

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
            'list'         =>       $list,
        ]);
    }


    /**
     * ����ְ��
     *
     * @method post
     * @url /msc/admin/professionaltitle/holder-add
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        name       �豸��(�����)
     * *string         detail     ְ��˵��(�����))
     * * int            status       ״̬(�����)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postHolderAdd(Request $request){
        $this->validate($request,[
            'name'   => 'required|max:20',
            'description'   =>  'required|max:50',
            'status' =>   'required|in:1,2'
        ]);
        $data=[
            'name'=>Input::get('name'),
            'description'=>Input::get('description'),
            'status'=>Input::get('status'),
        ];
        $ResourcesAdd= ProfessionalTitle::create($data);
        if($ResourcesAdd != false){
            return redirect()->back()->withInput()->withErrors('��ӳɹ�');
        }else{
            return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
        }
    }

    /**
     * �༭����ְ��
     *
     * @method post
     * @url /msc/admin/professionaltitle/holder-edit
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        name       �豸��(�����)
     * *string         detail     ְ��˵��(�����))
     * * int            status       ״̬(�����)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
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

    /**
     * Created by PhpStorm.
     * User: zhouqiang
     * Date: 2015/12/30 0028
     * Time: 13:01
     * �޸�ְ��
     */

    public  function postHolderSave(Request $request){
        $this->validate($request,[
            'name'   => 'required|max:20',
            'detail'   =>  'required|max:20',
            'status' =>   'required|in:1,2'
        ]);
        $data=[
            'name'=>Input::get('name'),
            'detail'=>Input::get('detail'),
            'status'=>Input::get('status'),
        ];
        $Save = DB::connection('msc_mis')->table('professional_title')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
        if( $Save != false){
            return redirect()->back()->withInput()->withErrors('�޸ĳɹ�');
        }else{
            return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
        }
    }
    /**
     * Created by PhpStorm.
     * User: zhouqiang
     * Date: 2015/12/30 0028
     * Time: 13:01
     * �޸�ְ��״̬
     */

    public function getHolderStatus($id){
        $holderId = intval($id);

        $holderModel = new ProfessionalTitle();

        $result =  $holderModel->changeStatus($holderId);
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
     *ְ��ɾ��
     * @method get
     * @url /msc/admin/professionaltitle/holder-remove
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>post�����ֶΣ�</b>
     * *int   ID    (�����)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public  function getHolderRemove(){
//        echo'22222';exit;
        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('professional_title')->where('id','=',$id)->delete();
            if($data != false){
                return redirect()->back()->withInput()->withErrors('ɾ���ɹ�');
            }else{
                return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
            }
        }else{
            return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
        }
    }




}