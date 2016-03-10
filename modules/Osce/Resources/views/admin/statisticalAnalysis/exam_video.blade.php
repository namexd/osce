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
        .resume,.pause{
            position: absolute;
            bottom:0;
        }
        .pause{
            display: none;
        }
    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/statisticalAnalysis/statistical_analysis.js')}}" ></script>
@stop

@section('content')
    @if($data->count() != 0)
    <input type="hidden" id="parameter" value="{'ip':'{{$data[0]['ip']}}',
    'port':'{{$data[0]['port']}}','username':'{{$data[0]['username']}}',
    'password':'{{$data[0]['password']}}','channel':'{{$data[0]['channel']}}','download':'{{route('osce.admin.course.getDownloadComponents')}}',
    'starttime':'{{$data[0]['begin_dt']}}','endtime':'{{$data[0]['end_dt']}}'}" />

    @else

   <input type="hidden" id="parameter" value="{'ip':'',
    'port':'','username':'','password':'','channel':''}" />

    @endif
    <div class="wrapper-content">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试视频</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="javascript:history.back(-1)" class="btn btn-outline btn-default" style="float: right;">返回</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div>
                <div class="col-sm-3">
                    <h4>标记点</h4>
                    <hr style="margin-top: 10px;margin-bottom: 10px">
                    <ul class="points">
                            @forelse($anchor as $item)
                                <li><span class="year">{{$item->begin_dt}}</span></li>
                            @empty
                                {{--@foreach($data as $item)--}}

                            {{--<li><span class="year">{{$item->anchor}}</span></li>--}}

                            {{--@endforeach--}}

                            @endforelse


                    </ul>
                </div>
                <div class="col-sm-9 video">
                    <div id="divPlugin" class="plugin"></div>
                    <div id="progress" class="progress" style="width: 600px;">
                        <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">

                        </div>
                    </div>
                    <button type="button" class="btn btn-default btn-md pause">
                        <i class="fa fa-pause" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-md resume">
                        <i class="fa fa-play"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}
