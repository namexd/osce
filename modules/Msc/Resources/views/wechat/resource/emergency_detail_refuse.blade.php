@extends('msc::wechat.layouts.admin')
@section('only_head_css')

    <link href="{{asset('msc/wechat/personalcenter/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
   <script>

       $(document).ready(function () {
           $("#Reason").change( function(){
               var txt  =$(this).find(':selected').val();
               if($(this).val()=="other") {
                   $(".Reason").show();
               }else{
                   $(".Reason").hide();
               }
               $("#Reason_detail").text(txt);
           })//隐藏input框
           //拒绝确定
           $("#refuse").click(function(){
               var reject=$("#Reason_detail").text();
               console.log($reject)
               $.ajax({
                   url:"{{url('/msc/wechat/lab/refund-open-lab-apply')}}",
                   type:"get",
                   dataType:"json",
                   data:{
                       id:2,
                       reject:$reject,
                       status:2
                   },
                   success: function(result) {
                       console.log(result);
                   }
               });
           })
       });

   </script>
@stop
@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        审核不通过
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
    <div id="info_list">
        <div id="Equipment_info" class="w_94" style="">
            <form name="form"   id="frmTeacher" action="{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postRefundEmergencyApply')}}" method="post" >
                <p  class="mart_3">不通过原因</p>
                <div>
                    <input type="hidden" name="id" value="{{$data->id}}" />
                    <select class="form-control"  name="resources_tool_item_id"  id="Reason" placeholder="请选择理由" style="width:100%;"  >
                        <option value="教室无法调开">教室无法调开</option>
                        <option value="调课率已超出">调课率已超出</option>
                        <option value="任务临近，无法安排">任务临近，无法安排</option>
                        <option value="" selected>自定义理由</option>
                    </select>
                </div>

                <div class="Reason mart_10"  style="display: none">
                    <textarea id="Reason_detail" name="reject" type="" placeholder="请输入自定义理由"/></textarea>
                </div>
                <div class="mart_10">
                    <input class="btn2" type="submit"  value="确认" id="refuse"/>
                </div>

            </form>
        </div>

    </div>

@stop