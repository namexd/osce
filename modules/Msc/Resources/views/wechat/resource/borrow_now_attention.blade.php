@extends('msc::wechat.layouts.admin')
@section('only_head_css')

    <link href="{{asset('msc/wechat/personalcenter/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
   <script>

       $(document).ready(function () {
           $("#Reason").change( function(){

               if($(this).val()=="other") {
                   $(".Reason").show();
               }else{
                   $(".Reason").hide();
               }
           })//隐藏input框
       });

   </script>
@stop
@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        提醒归还
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
    <div id="info_list">
        <div id="Equipment_info" class="w_94" style="">
            <form name="form"   id="frmTeacher" action="{{action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddBorrowApply')}}" method="post" >
                <p  class="mart_3">提醒归还理由</p>
                <div>

                    <select class="form-control"  name="resources_tool_item_id"  id="Reason" placeholder="请选择理由" style="width:100%;">
                        <option value="1">理由12121</option>
                        <option value="other">自定义理由</option>
                        <option>选项 3</option>
                        <option>选项 4</option>
                    </select>
                </div>

                <div class="Reason mart_10"  style="display: none">
                    <textarea id="Reason_detail" name="detail" type="" placeholder="请输入自定义理由"/></textarea>
                </div>
                <div class="mart_10">
                    <input class="btn2" type="submit"  value="确认" />
                </div>

            </form>
        </div>

    </div>

@stop