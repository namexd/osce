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
            position: relative;
        }

    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/testMonitor/monitor_test_video.js')}}" ></script>
@stop

@section('content')
    @if(!empty($data))
    <input type="hidden" id="parameter" value="{'ip':'{{@$data['ip']}}',
    'port':'{{@$data['port']}}','username':'{{@$data['username']}}',
    'password':'{{@$data['password']}}','channel':'{{@$data['channel']}}','download':'{{route('osce.admin.course.getDownloadComponents')}}'}" />
    @else
   <input type="hidden" id="parameter" value="{'ip':'',
    'port':'','username':'','password':'','channel':''}" />
    @endif
    <div class="wrapper-content">
        <div class="container-fluid ibox-content">
            <div class="video">
                <div id="divPlugin" class="plugin"></div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
