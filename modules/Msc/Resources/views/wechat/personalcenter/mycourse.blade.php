@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/personalcenter/css/course_date.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    *{margin:0;padding:0px;}
    #calendar{margin-top:100px;padding:0px 5px;width:250px;height:30px;line-height:30px;border-radius:3px;border:1px solid #ccc;cursor:pointer;text-align:center;}
</style>


@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/personalcenter/js/zlDate.js')}}"></script>
    <script>

        $(document).ready(function(){
           AjaxTime();
        });
        function AjaxTime(){
            $.get("{{asset('msc/wechat/personalcenter/js/date.php')}}",function(data) {
                pickerEvent.setPriceArr(eval("("+data+")"));
                pickerEvent.Init("calendar");
            });
        }
    </script>

@stop


@section('content')

    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        我的课程
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
@stop