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
    *רҵ�б�
    * @method GET
    * @url /msc/admin/profession/profession-list
    * @access public
    *
    * @param Request $request get����<br><br>
    * <b>post�����ֶΣ�</b>
    * * string        keyword       רҵ����
    * * int           status        רҵ״̬(1��������2��ͣ��)
    * @return  view
    *
    * @version 1.0
    * @author zhouqiang <zhouqiang@sulida.com>
    * @date ${DATE} ${TIME}
    * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     * ����רҵ
     *
     * @method post
     * @url /msc/admin/profession/profession-add
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string        name       רҵ����(�����)
     * * int            code       רҵ����(�����)
     * * int            status       ״̬(�����)
     * @return   json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
            return redirect()->back()->withInput()->withErrors('�����ɹ�');
        }

            return redirect()->back()->withInput()->withErrors('����ʧ��');
    }


    /**
     * �༭����רҵ
     * @method GET
     * @url /msc/admin/profession/profession-edit
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>post�����ֶΣ�</b>
     * * int    id      (�����)
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     *�ύ�༭רҵ
     * @method post
     * @url /msc/admin/profession/profession-save
     * @access public
     *
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * *int   ID    (�����)
     * *string        name       רҵ����(�����)
     * * int          code       רҵ����(�����)
     * * int         status       ״̬(�����)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
            return redirect()->back()->withInput()->withErrors('�޸ĳɹ�');

        }
        return redirect()->back()->withInput()->withErrors('�޸�ʧ��');
    }



    /**
     *�ı�רҵ��״̬
     * @method get
     * @url /msc/admin/profession/profession-status/{id}
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>post�����ֶΣ�</b>
     * *int   ID    (�����)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     *רҵɾ��
     * @method get
     * @url /msc/admin/profession/profession-deletion/{id}
     * @access public
     *
     * @param Request $request get����<br><br>
     * <b>post�����ֶΣ�</b>
     * *int   ID    (�����)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date ${DATE} ${TIME}
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
     * ����רҵ��
     * @api post /msc/admin/profession/profession-import
     * @access public
     * @param Request $request post����<br><br>
     * <b>post�����ֶΣ�</b>
     * * string       training       �γ��ļ���excl(�����)
     * @return object
     * @version 0.8
     * @author zhouqiang <zhouqiang@sulida.com>
     * @date 2015-11-27 10:24
     * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
     *
     */

    public function postProfessionImport(Request $request){
        try{
            $data = Common::getExclData($request, 'training');
            $professionInfo = array_shift($data);
            //������ͷת�������Ӣ��
            $professionInfo = Common::arrayChTOEn($professionInfo, 'msc.importForCnToEn.profession_group');
            //�Ѿ����ڵ�����
            $dataHaven = [];
            //���ʧ�ܵ�����
            $dataFalse = [];
            //�ж��Ƿ�������רҵ
            foreach ($professionInfo as $professionData) {
                //����״̬status
                switch ( $professionData['status']){
                    case "����":
                        $professionData['status'] = 1;
                        break;
                    case "ͣ��":
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