@extends('osce::admin.layouts.admin_index')

@section('only_css')
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
                <a  href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div>
                <div class="col-sm-3">
                    <h4>标记点</h4>
                    <hr style="margin-top: 10px;margin-bottom: 10px">
                    <ul class="points">
                        <li><span class="year">2015/1/16</span><span>12:00:43</span></li>
                        <li><span class="year">2015/1/16</span><span>12:00:43</span></li>
                    </ul>
                </div>
                <div class="col-sm-9"></div>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}