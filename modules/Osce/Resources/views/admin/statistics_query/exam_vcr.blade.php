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
        }
    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/statistics_query/js/statistics_query.js')}}" ></script>
@stop

@section('content')

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
                        @forelse($data as $item)
                            <li><span class="year">{{$item->anchor}}</span></li>
                        @empty
                        @endforelse
                    </ul>
                </div>
                <div class="col-sm-9 video">
                    <div id="divPlugin" class="plugin"></div>
                    <div id="progress" class="progress" style="width: 600px;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0;">

                        </div>
                    </div>
                    <input class="pause" value="暂停"  type="button">
                    <input class="resume" value="恢复"  type="button">
                </div>
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}
