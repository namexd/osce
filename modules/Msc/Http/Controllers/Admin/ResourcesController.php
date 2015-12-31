<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/30 0030
 * Time: 10:01
 */

namespace Modules\Msc\Http\Controllers\Admin;


use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Modules\Msc\Entities\Devices;
use Modules\Msc\Http\Controllers\MscController;
use URL;
use DB;
class ResourcesController extends MscController
{
    /**
     *��Դ�б�
     * @method GET
     * @url /msc/admin/resources/resources-index
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

    public function  getResourcesIndex(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'sometimes',
            'status' => 'sometimes|in:1,2',
            'devices_cate_id' => 'sometimes|in:1,2,3,4'
        ]);
        $keyword = urldecode(e($request->input('keyword')));
        $status = (int)$request->input('status');
        $devices_cate_id = (int)$request->input('devices_cate_id');
        $devices = new Devices();
        $pagination = $devices->getDevicesList($keyword, $status, $devices_cate_id);
//        dd($pagination);


        $list = [];
        foreach ($pagination as $itme) {
            $list[] = [
                'id' => $itme->id,
                'name' => $itme->name,
                'detail' => $itme->detail,
                'catename'=>$itme->catename,
                'devices_cate_id'=>$itme->devices_cate_id,
                'status' => is_null($itme->status) ? '-' : $itme->status,
            ];
        }
//        dd($list);
        return view('msc::admin.systemtable.resource_table',[
            'list'         =>       $list,
            'keyword'=>$request->input('keyword')?$request->input('keyword'):'',
            'status'=>$request->input('status')?$request->input('status'):'',
        ]);
    }


    /**
     * ������Դ
     *
     * @method post
     * @url /msc/admin/resources/resources-add
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        name       �豸��(�����)
     * *string         detail     �豸˵��(�����)
     * * int            devices_cate_id   ��Դ���� (�����)
     * * int            status       ״̬(�����)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function postResourcesAdd(Request $request){
//        dd(111111111111111);
        $this->validate($request,[
            'name'   => 'required|max:20',
            'devices_cate_id'=>'required',
            'detail'   =>  'required|max:20',
            'status' =>   'required|in:1,2'
        ]);
         $data=[
             'name'=>Input::get('name'),
             'devices_cate_id'=>Input::get('devices_cate_id'),
             'detail'=>Input::get('detail'),
             'status'=>Input::get('status'),
         ];
        $ResourcesAdd= Devices::create($data);
        if($ResourcesAdd != false){
            return redirect()->back()->withInput()->withErrors('��ӳɹ�');
        }else{
            return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
        }
    }

    /**
     * �༭������Դ
     *
     * @method post
     * @url /msc/admin/resources/resources-edit
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * *int       id   ��ԴID������ģ�
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

       public  function getResourcesEdit($id){
           $ResourcesId = intval($id);
           $Resources= Devices::findOrFail($ResourcesId);
           $data=[
               'name'   =>   $Resources['name'],
               'detail'   =>   $Resources['detail'],
               'devices_cate_id'   => $Resources['devices_cate_id'],
               'status' =>    $Resources['status']
           ];
           die(json_encode($data));
}
    /**
     * Created by PhpStorm.
     * User: zhouqiang
     * Date: 2015/12/30 0028
     * Time: 13:01
     * �޸���Դ
     */
    public  function postResourcesSave(Request $request){
        $this->validate($request,[
            'name'   => 'required|max:20',
            'devices_cate_id'=>'required',
            'detail'   =>  'required|max:20',
            'status' =>   'required|in:1,2'
        ]);
        $data=[
            'name'=>Input::get('name'),
            'devices_cate_id'=>Input::get('devices_cate_id'),
            'detail'=>Input::get('detail'),
            'status'=>Input::get('status'),
        ];
        $Save = DB::connection('msc_mis')->table('devices')->where('id','=',urlencode(e(Input::get('id'))))->update($data);
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
     * �޸�״̬
     */

      public function getResourcesStatus(Devices $devices)
      {
          $id = urlencode(e(Input::get('id')));
          if ($id) {
              $data = $devices->where('id', '=', $id)->update(['status' => Input::get('type')]);
              if ($data != false) {
                  return redirect()->back()->withInput()->withErrors('ͣ�óɹ�');
              } else {
                  return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
              }
          } else {
              return redirect()->back()->withInput()->withErrors('ϵͳ�쳣');
          }
      }

    /**
     *רҵɾ��
     * @method get
     * @url /msc/admin/resources/resources-remove/{id}
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
    public  function getResourcesRemove(){

        $id = urlencode(e(Input::get('id')));
        if($id){
            $data = DB::connection('msc_mis')->table('devices')->where('id','=',$id)->delete();
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


