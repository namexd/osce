@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        ul{
            margin: 0;
            padding: 0;
        }
        .points>li{
            padding: 5px 0;
            cursor: pointer;
            margin: 5px 0;
        }
        .points>li:hover{
            background-color: #eee;
        }
        .video{
            padding-left: 50px;
            position: relative;
        }
        .progress{
            cursor: pointer;
            margin-bottom: 50px;
        }
    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/testMonitor/monitor_test_video.js')}}" ></script>
@stop

@section('content')
    @if($data->count() != 0)
    <input type="hidden" id="parameter" value="{'ip':'{{$data[0]['ip']}}',
    'port':'{{$data[0]['port']}}','username':'{{$data[0]['username']}}',
    'password':'{{$data[0]['password']}}','channel':'{{$data[0]['channel']}}','download':'{{route('osce.admin.course.getDownloadComponents')}}'}" />
    @else
   <input type="hidden" id="parameter" value="{'ip':'',
    'port':'','username':'','password':'','channel':''}" />
    @endif
    <div class="wrapper-content">
        <div class="container-fluid ibox-content">
            <div class="video">
                <div id="divPlugin" class="plugin"></div>
                <div id="progress" class="progress" style="width: 600px;">
                    <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
