@extends('msc::admin.layouts.admin')

@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/returnmanage/css/history.css')}}">
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        var start = {
            elem: "#start",
            format: "YYYY/MM/DD hh:mm:ss",
            min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                end.min = a;
                end.start = a
            }
        };
        var end = {
            elem: "#end",
            format: "YYYY/MM/DD hh:mm:ss",
            min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                start.max = a
            }
        };
        $(function(){
            
            //时间选择
            laydate(start);
            laydate(end);

            $('#search').click(function(){

            })
        })
    </script>
@stop


@section('content')

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-6">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start">
                        <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end">
                    </div>
                </div>
            <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
        </div>
        <div class="col-xs-6 col-md-2">

            <div class="input-group">
                <input type="text" placeholder="请输入关键字" class="input-sm form-control">
            <span class="input-group-btn">
                <button type="button" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
            </span>
            </div>

        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('msc.admin.resourcesManager.getStatistics')}}" class="btn btn-primary marl_10">外借统计分析</a>

        </div>

    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>设备名称</th>
                <th>借用时间</th>
                <th>设备编号</th>
                <th>借用人</th>
                <th>借用理由</th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">设备归还状态 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}">全部</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=1">已归还</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=0">借出未归还</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=-1">预约已过期</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=-2">取消预约</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=-3">超期未归还</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=4">已归还但有损坏</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=5">超期归还</a>
                            </li>

                        </ul>
                    </div>
                </th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">是否按时归还 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?is_gettime=2">是</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}?status=1">否</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
            @forelse($pagination as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{is_null($item->toolItem)? '-':(is_null($item->toolItem->resourcesTools)? '-':$item->toolItem->resourcesTools->name )}}</td>
                    <td>{{$item->real_begindate}}-{{$item->real_enddate}}</td>
                    <td>{{is_null($item->toolItem)? '-':$item->toolItem->code}}</td>
                    <td>{{is_null($item->lenderInfo)? '-':$item->lenderInfo->name}}</td>
                    <td>{{$item->detail}}</td>
                    <td>{{$item->status}}</td>
                    <td><span class="state3">是</span></td>
                    <td><a class="read  state1" href="{{route('msc.admin.resourcesManager.getRecordInfo')}}?id={{$item->id}}">查看</a></td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        <div class="btn-group pull-right">
            {!! $pagination->render() !!}
        </div>
    </form>
    <div ng-include="'configs.html'"></div>
</div>
@stop{{-- 内容主体区域 --}}