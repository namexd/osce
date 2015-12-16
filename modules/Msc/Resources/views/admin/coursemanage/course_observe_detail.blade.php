@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/css/demo.css')}}">
    <style type="text/css">
        #left-content .form-control{border: 0;}
        .title .col-sm-2 label{width: 100%;}
        .title .col-sm-2 label span{
            float: right;
            margin-right: 10px;
        }
        .title h4 label{font-weight: bolder;}
        .tools-bar{
            height: 30px;
            background: #F5F5F5;
        }
        .nav-bar li{
            list-style: none;
            float: left;
            margin: 2px 5px;
            height: 20px;
            line-height: 28px;
        }
        .nav-bar li a{color: #676a6c!important;}
        .active{font-weight: bolder;}
        .tools-tips{
            float: right;
            margin-top: 5px;
            margin-right: 15px;
        }
        .tools-tips span:first-child{margin-right: 8px;}
        .tools-tips a {color: #676a6c}
        .fa{cursor: pointer;}
    </style>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'course_observe_detail','downloadUrl':'{{route("msc.admin.courses.getDownloadVideoActivx")}}','downloadVideo':'{{route("msc.courses.getVideoCheck")}}'}">
<div class="row">
    <!-- 左侧 -->
    <div class="col-sm-4">
        <div class="row">
            <div class="form-horizontal ibox-content" id="left-content">
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">课程内容</label>
                    <input type="hidden" value="2015-12-16 08:00:00" id="start">
                    <input type="hidden" value="2015-12-16 14:00:00" id="end">
                    <div class="col-sm-10">
                        <div class="form-control">{{$courses_name}}</div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">授课老师</label>
                    <div class="col-sm-10 select_code">
                        <div class="form-control">{{$teacher_name}}</div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">考勤率</label>
                    <div class="col-sm-10" style="height:98px">
                        <div class="form-control">
                            <span>{{round(($unabsence/$total*100),2)}}%</span>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td>应到</td>
                                        <td>实到</td>
                                        <td>缺勤</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$total}}</td>
                                        <td>{{$unabsence}}</td>
                                        <td>{{$total-$unabsence}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" >练习情况</label>
                    <div class="col-sm-10">
                        <div class="form-control">
                            <button class="btn btn-white cancel" type="button"><i class="fa fa-file-text"></i>&nbsp;查看预习题</button>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group" style="height:270px;">
                    <label class="col-sm-2 control-label">错题统计</label>
                    <div class="col-sm-10">
                        <div class="form-control">
                            <div id="main" style="height:260px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 右侧 -->
    <div class="col-sm-8">
        <div class="container ibox-content">
            <div class="title">
                <h4>
                    <div class="row">
                        <div class="col-sm-10">
                            <label class="font-noraml">{{$lab_name}}</label>
                        </div>
                        <div class="col-sm-2">
                            <label class="font-noraml"><b id="nowDay">2015-12-11</b><span id="time">8:00:00</span></label>
                        </div>
                    </div>
                </h4>
            </div>
            <div class="tools-bar">
                <ul class="nav-bar">
                    @foreach($vcrs as $index =>$item)
                        <li value="{{$item['vcr_id']}}"><a href="javascript:void(0)" class="{{$index==0? 'active':''}}">{{$item['vcr_name']}}</a></li>
                    @endforeach
                    <div class="tools-tips">
                        <a href="javascript:void(0)" id="download">下载</a>&nbsp;&nbsp;
                        <a href="{{route('msc.admin.courses.getClassObserve')}}" class="fa fa-arrows-alt"></a>
                        <span class="fa fa-times"></span>
                    </div>
                </ul>
            </div>
            <div class="content" style="height:600px;">
                <div id="divPlugin" class="video"></div>
            </div>
        </div>
    </div>
</div>   
@stop{{-- 内容主体区域 --}}

@section('only_js')
<script src="{{asset('msc/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script src="{{asset('msc/admin/js/webVideoCtrl.js')}}"></script>
<script src="{{asset('msc/admin/coursemanage/js/coursemanage.js')}}"></script>
@stop