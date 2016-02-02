@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link rel="stylesheet" href="{{asset('osce/admin/css/demo.css')}}">
    <style>
        .tabs{
            margin: 20px 0;
            font-weight: 700;
        }
        .year{
            margin-right: 20px;
        }
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
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/statistics_query/js/statistics_query.js')}}" ></script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'exam_vcr'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
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
                        @forelse($data as $item)
                            <li><span class="year">{{$item->anchor}}</span></li>
                        @empty
                        @endforelse
                    </ul>
                </div>
                <div class="col-sm-9">
                    <div id="divPlugin" class="video"></div>
                    <input type="button" class="btn" value="暂停" id="pause" />
                    <input type="button" class="btn" value="恢复" id="resume" />
                    <input type="button" class="btn" value="慢放" id="playslow" />
                    <input type="button" class="btn" value="快放" id="playfast"/>
                    <input type="button" class="btn" value="停止回放" id="stopplay"/>
                </div>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}