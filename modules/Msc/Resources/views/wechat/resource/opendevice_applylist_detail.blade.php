@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
    .gn_txt{height: auto;}
</style>
@stop

@section('only_head_js')

@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        开放实验室A 11/12
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
<form action="#">
    <div class="add_main mart_5">
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                {{@$DeviceApply->resourcesLabDevices->name}}
            </div>
        </div>
        <div class="form-group">
            <label for="">申请人</label>
            <div class="txt">
                {{@$DeviceApply->user->name}}
            </div>
        </div>
        <div class="form-group">
            <label for="">时间段</label>
            <div class="txt">
                {{@$DeviceApply->original_begin_datetime}}~{{@$DeviceApply->original_end_datetime}}
            </div>
        </div>
        <!-- <div class="form-group">
            <label for="">申请人</label>
            <div class="txt">
                李四
            </div>
        </div>
        <div class="form-group">
            <label for="">内容</label>
            <input type="button" class="form-control set_text"  name="" value="{{@$Courses->detail}}" required="">
        </div>
        <div class="form-group">
            <label for="">{{@$type}}</label>
            <input type="button" class="form-control set_text"  name="" value="{{@$studentType}}" required="">
        </div> -->
        <div class="form-group">
            <label for="">理由</label>
            <div class="txt">
                {{@$DeviceApply->detail}}
            </div>
        </div>
    </div>
    <div></div>
   <div class="w_94 ">
        @if($DeviceApply['status'] == 0)
        <div class="w_45 left marb_5 ">
           <input type="button" class="btn5" id="btn5" value="审核不通过"/>
        </div>
        <div class="w_45 right marb_5 ">
           <input type="button" class="btn2" id="btn2" value="审核通过"/>
        </div>
        @elseif($DeviceApply['status'] == 1)
            <input type="button" class="btn5" value="已通过审核"/>
        @else
            <input type="button" class="btn5"value="未通过审核"/>
        @endif
   </div>
</form>
<script type="text/javascript" charset="utf-8">
    var id = "{{@$DeviceApply->id}}";
    $('#btn2').click(function(){
        $.confirm({
                title: '提示：',
                content: '您确定通过？',
                confirmButton: '　　　是　　　 ' ,
                cancelButton: '　　　　否　　',
                confirmButtonClass: 'btn-info',
                cancelButtonClass: 'btn-danger',
                confirm:function(){
                     window.location.href="/msc/wechat/open-laboratory/open-lab-offer-do?id="+id;
                }
            })
    });
    $('#btn5').click(function(){
       
        window.location.href="/msc/wechat/open-laboratory/open-lab-miss?id="+id;
    });
    
</script>
@stop
