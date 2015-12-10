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
            format: "YYYY/MM/DD",
            min: laydate.now(),
            max: "2099-06-16",
            istime: false,
            istoday: false,
            choose: function (a) {
                end.min = a;
                end.start = a
            }
        };
        $(function(){
            
            //时间选择
            laydate(start);

            $('#search').click(function(){

            })
        })
    </script>
@stop


@section('content')

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row table-head-style1 ">
      <form method="get" action="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}">
        <div class="col-xs-6 col-md-6">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" placeholder="请选择日期" class="form-control layer-date laydate-icon" id="start" name="date">
                    </div>
                </div>
            <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
        </div>
        <div class="col-xs-6 col-md-2">

            <div class="input-group">
                <input type="text" placeholder="请输入关键字" class="input-sm form-control" name="keyword">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-sm btn-primary" id="search" /><i class="fa fa-search"></i></button>
            </span>
            </div>

        </div>
      </form>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="/msc/admin/lab-tools/history-statistics" class="btn btn-primary marl_10">外借统计分析</a>

        </div>

    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">设备名称<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=1&order_type=asc">升序</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=1&order_type=desc">降序</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>日期</th>
                <th>时间</th>
                <th>编号</th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">预约人<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=2&order_type=asc">升序</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=2&order_type=desc">降序</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>预约理由</th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">是否复位状态自检<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=3&order_type=asc">升序</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=3&order_type=desc">降序</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">是否复位设备<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=4&order_type=asc">升序</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}?order_name=4&order_type=desc">降序</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>@forelse($pagination as $item)
                    <tr>
                        <td>{{$item['id']}}</td>
                        <td>{{$item['name']}}</td>
                        <td>{{ date('Y-m-d',strtotime($item['original_begin_datetime'])) }}</td>
                        <td>{{ date('H:i' , strtotime($item['original_begin_datetime'])) }}-{{date('H:i' , strtotime($item['original_end_datetime'])) }}</td>
                        <td>{{$item['code']}}</td>
                        <td>{{$item['student_name']}}</td>
                        <td>{{$item['detail']}}</td>
                        <td>
                            @if ($item['health'] =="1")
                                完好
                            @elseif($item['health'] =="2")
                                损坏
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($item['reset'] =="1")
                                是
                            @else
                                <span class="state2">否</span>
                            @endif
                        </td>
                        <td><a class="read  state1" href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistoryView')}}?id={{ $item['id'] }}">查看</a></td>
                    </tr>
                @empty
                @endforelse
                <!-- 主体 -->
               <!--  <tr>
                    <td>1</td>
                    <td>设备A</td>
                    <td>2015/9/21</td>
                    <td>08:00-10:00</td>
                    <td>09887</td>
                    <td>李老师</td>
                    <td>训练</td>
                    <td>良好</td>
                    <td>是</td>
                    <td><a class="read  state1" href="javascript:void(0)">查看</a></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>设备A</td>
                    <td>2015/9/21</td>
                    <td>08:00-10:00</td>
                    <td>09887</td>
                    <td>李老师</td>
                    <td>训练</td>
                    <td><span class="state2">有损坏</span></td>
                    <td><span class="state2">否</span></td>
                    <td><a class="read  state1" href="javascript:void(0)">查看</a></td>
                </tr> -->
            </tbody>
        </table>

        <div class="btn-group pull-right">
        <!-- 分页 -->
            {!! $pagination->render() !!}
        </div>
    </form>
    <div ng-include="'configs.html'"></div>
</div>
@stop{{-- 内容主体区域 --}}