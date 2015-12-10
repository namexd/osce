@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
    .gn_txt{height: auto;}
</style>
@stop

@section('only_head_js')
<script type="application/javascript">
    function agree(){
        var url =   $(this).data('url');
        window.location.href    =   url;
    }
    $(function(){
        $('.href').one('click',agree);
    })
</script>
@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        突发事件管理
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
<form action="#">
    <div class="add_main mart_5">
        <div class="form-group">
            <label for="">教室</label>
            <div class="txt">
                {{@$Classroom->name}}
            </div>
        </div>
        <div class="form-group">
            <label for="">预约人</label>
            <div class="txt">
                {{@$username->name}}
            </div>
        </div>
        <div class="form-group">
            <label for="">时间段</label>
            <div class="txt">
                {{@$OpenLabPlan->begintime}}~{{@$OpenLabPlan->endtime}}
            </div>
        </div>
        <div class="form-group">
            <label for="">地点</label>
            <div class="txt">
                {{@$Classroom->location}}
            </div>
        </div>
        <div class="form-group">
            <label for="">内容</label>
            <div class="txt">
                {{@$Courses->detail}}
            </div>
        </div>
        <div class="form-group">
            <label for="">{{$type}}</label>
            <div class="txt">
                {{$studentType}}
            </div>
        </div>
        <div class="form-group">
            <label for="">理由</label>
            <div class="txt">
                {{@$OpenLabApply->detail}}
            </div>
        </div>
    </div>
    <div></div>
   <div class="w_94 ">
       <div class="w_45 left marb_5 ">
           <input type="button" class="btn5 href"  data-url="{{route('msc.wechat.lab.refundEmergencyApply',['id'=>$OpenLabApply->id])}}"  value="审核不通过"/>
       </div>
       <div class="w_45 right marb_5 ">
           <input type="button" class="btn2 href" data-url="{{route('msc.wechat.lab.agreeEmergencyApply',['id'=>$OpenLabApply->id])}}"  value="审核通过"/>
       </div>
   </div>
</form>

@stop
