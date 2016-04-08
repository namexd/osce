@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        #myModalLabel{color: #16BEB0;}
        /*强调颜色*/
        .state{color: #ed5565;}
        /*标题展示区域*/
        .success-element:hover {cursor: default!important;}
        .titleBackground{background-color: #E9EDEF!important;}
        .messageColor{color: #999A9E}
        .list_box{width:25%;height:150px;padding:15px;float:left}
        .list_box a{color:#666;}
        .list_box a:link:hover {text-decoration: none;}
        .list_con{height:100%;border-radius: 5px;background-color: #16beb0;position: relative;}
        .list_con_info{position: absolute;right: 50%;bottom:50%;margin-right:-60px;margin-bottom:-9px;font-weight:bold;color:#fff;font-size: 1.5rem}
        .list_timers{text-align: center;}
    </style>
@stop

@section('only_js')

@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'monitor_check'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">视频回看</h5>
            </div>
            <a href="javascript:history.go(-1)" class="btn btn-primary right" role="btn">返回</a>
        </div>
        <div class="container-fluid ibox-content">
                    <p class="font20 fontb">{{$data->exam_name}}-<span>{{$data->name}}</span>({{$data->exam_sequence}})</p>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="list_all">
                    @if(!empty($list)&& count($list)>0)
                        @foreach($list as $key=>$val )
                    <div class="list_box">
                        @if($val->time==0)
                            <a href="javascript:void(0)">
                        @else
                            <a href="{{route('osce.admin.course.getResultVideo')}}?exam_id={{$data->exam_id}}&student_id={{$data->student_id}}&station_id={{$val->station_id}}">
                        @endif

                            <div class="list_con">
                                <span class="list_con_info">{{$val->name}}({{$val->type}})</span>
                            </div>
                            @if($val->time==0)
                                <p class="list_timers">00:00:00</p>
                            @else
                            <p class="list_timers">{{$val->time}}</p>
                            @endif
                        </a>
                    </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
