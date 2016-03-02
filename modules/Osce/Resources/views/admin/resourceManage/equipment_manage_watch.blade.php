@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .left-text{
            line-height: 34px;
            margin-right: 20px;
        }
        .right-list{
            width: 60%;
        }
        table tbody tr td:last-child{width: initial!important;}
    </style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script>
    $(function(){

        var start = {
            elem: '#starts', //需显示日期的元素选择器
            event: 'click', //触发事件
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式
            istime: true, //是否开启时间选择
            isclear: true, //是否显示清空
            istoday: true, //是否显示今天
            issure: true, //是否显示确认
            festival: true, //是否显示节日
            min: '1900-01-01 00:00:00', //最小日期
            max: '2099-12-31 23:59:59', //最大日期
            start: layer.now,    //开始日期
            fixed: false, //是否固定在可视区域
            zIndex: 99999999, //css z-index
            choose: function(dates){ //选择好日期的回调

            }
        };

        var end = {
            elem: '#ends', //需显示日期的元素选择器
            event: 'click', //触发事件
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式
            istime: true, //是否开启时间选择
            isclear: true, //是否显示清空
            istoday: true, //是否显示今天
            issure: true, //是否显示确认
            festival: true, //是否显示节日
            min: '1900-01-01 00:00:00', //最小日期
            max: '2099-12-31 23:59:59', //最大日期
            start: layer.now,    //开始日期
            fixed: false, //是否固定在可视区域
            zIndex: 99999999, //css z-index
            choose: function(dates){ //选择好日期的回调

            }
        };

        laydate(start);
        laydate(end);

    })
</script>
@stop


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">使用记录</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>3])}}" class="btn btn-outline btn-default" style="float: right;">返回</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form" action="{{route('osce.admin.machine.getWatchLogList')}}" method="get">
            <div class="panel blank-panel">
                <div  class="row" style="margin:20px 0;">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="pull-left left-text">设备ID:</label>

                        <div class="pull-left right-list">
                            <input class="form-control m-b" name="code" value="{{$code==null?'':$code }}"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <label class="pull-left left-text">使用人:</label>
                        <div class="pull-left right-list">
                            <input class="form-control m-b" name="student_name" value="{{$student_name==null?'':$student_name }}"/>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12" style="padding-right:0; ">
                        <label class="pull-left left-text">使用时间:</label>
                        <div class="pull-left">
                            <input class="form-control" name="begin_dt"  id="starts" value="{{$begin_dt==null?'':$begin_dt }}"/>
                        </div>
                        <label class="pull-left left-text" style="margin-left: 20px;">到</label>
                        <div class="pull-left" style="margin-right:20px;" >
                            <input class="form-control" name="end_dt" id="ends" value="{{$end_dt==null?'':$end_dt }}"/>
                        </div>
                        <button class="btn  btn-primary" type="submit"  style="float:left;height: 34px;" />搜索</button>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>设备ID</th>
                        <th>使用人</th>
                        <th>使用时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $key=>$item)
                        <tr>
                            <td>{{$item->code}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{(empty($item->context['time'])?'':$item->context['time'])}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pull-left">
                    共{{$list->total()}}条
                </div>
                <div class="btn-group pull-right">
                   {!! $list->appends($_GET)->render() !!}
                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}