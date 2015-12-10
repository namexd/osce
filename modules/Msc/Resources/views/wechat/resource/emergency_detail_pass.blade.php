@extends('msc::wechat.layouts.admin')
@section('only_head_css')
    <link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
    <style>
    .txt{
      height: 40px;
      margin-left: 27%;
      text-align: center;
    }
    </style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
   <script>

       $(document).ready(function () {
           $("#Reason").change( function(){

               if($(this).val()=="1") {
                   $(".Reason1").show();
                   $(".Reason").hide();
               }else{
                   $(".Reason").show();
                   $(".Reason1").hide();
               }
           })//隐藏input框

           /**
         *下拉选择框
         */
        $("#Reason").change( function(){
            if($(this).val()==1){
                $(".Reason1").show();
                $(".Reason").hide();
            }else{
                $(".Reason").show();
                $(".Reason1").hide();
            }
            //提交理由
            /*var id = (location.href).split('=')[1];
            $('.btn2').click(function(){
                $.ajax({
                    url:"{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postChangeOpenLabApplyStatus')}}",
                    type:"post",
                    dataType:"json",
                    cache:false,
                    data:{id:id,reject:$(".Reason").find('textarea').val(),status:2},
                    success: function(res) {
                        if(res.code != 1){
                            layer.alert((res.message).split(':')[1]);
                            console.log(res.message);
                        }else{
                            //成功的操作
                            layer.alert('预约成功！',function(){
                                location.reload();
                            });
                            console.log('通过！')
                        }
                    }
                });
            });*/ 
        })
        
        //数据id
        $('#item-id').val((location.href).split('=')[1]);


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
        <div class="w_90"><p class="font16 clo0 mart_5">该突发事件申请与以下已预约信息有冲突</p></div>
        @foreach($list as $key=>$item)
        <div class="add_main mart_5">
            <div class="form-group red">
                <label for="">预约{{$key+1}}</label>
                <div class="txt red">
                    {{ is_object($item->teachers)? $item->teachers->first()->teacher->name:'未知教师'}} {{is_null($item->course)? '临时安排':$item->course->name}} <br/>
                    {{date('Y/m/d',strtotime($item->currentdate))}} {{date('H:i',strtotime($item->begintime))}}-{{date('H:i',strtotime($item->endtime))}}
                </div>
            </div>
        </div>
        @endforeach
        <div id="Equipment_info" class="w_94" style="">
            <form name="form"   id="frmTeacher" action="{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postAgreeEmergencyApply')}}" method="post" >
                <p  class="mart_3 font16 clo0">请选择您要执行的操作</p>
                <div class="mart_3">
                    <select class="form-control"  name="resources_tool_item_id"  id="Reason" placeholder="请选择理由" style="width:100%;">
                        <option value="2">取消已预约的课程</option>
                    </select>
                </div>
                <input type="hidden" name="id" value="{{$id}}" />
                <div class="Reason mart_10">
                    <textarea id="Reason_detail" name="notice" type="" placeholder="请输入取消理由"/></textarea>
                </div>
                <div class="mart_10 marb_10">
                    <input class="btn2" type="submit"  value="确认" />
                </div>
            </form>
        </div>
    </div>
@stop