@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    #start,#end{width: 160px;}
    /*TODO: fandian，2016-2-26, 只用于本页面*/
    .btn.btn-primary{
        padding: 4px 8px; !important;
    }
    </style>
@stop

@section('only_js')
   <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_assignment','deletes':'{{route('osce.admin.exam.postDelete')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">今天的所有考试</h5>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>考试编号</th>
                <th>考试名称</th>
                <th>时间</th>
                <th>考试人数</th>
                <th>开考</th>
                <th>大屏</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->id}}</td>
                    <td><a href="{{route('osce.admin.exam.getEditExam',['id'=>$item->id])}}">{{$item->name}}</a> </td>
                    <td>{{date('Y-m-d H:i', strtotime($item->begin_dt))}} ~ {{date('Y-m-d H:i', strtotime($item->end_dt))}}</td>
                    <td>{{$item->total}}</td>
                    <td value="{{$item->id}}">
                        @if($item->status ==0)
                            @if(count($item->examPlan) > 0)

                                <a href="{{route('osce.admin.index.getSetExam',['id'=>$item->id])}}">
                                    <input class="btn btn-primary" type="button" value="开始考试"/>
                                </a>
                            @else
                                <a href="javascript:void(0)">
                                    <input class="btn btn-primary" type="button" disabled value="开始考试"/>
                                </a>
                            @endif
                        @elseif($item->status==1)
                            正在考试
                        @else
                            考试已结束   
                        @endif
                    </td>
                    <td>
                        @if($item->status==2)
                            考试已结束
                        @else
                            <a class="btn btn-primary" href="{{route('osce.admin.getWaitDetail',['exam_id'=>$item->id])}}" target="_blank">
                                大屏
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pull-left">
{{--            共{{$data->total()}}条--}}
        </div>
        <div class="btn-group pull-right">
           {{--{!! $data->appends($_GET)->render() !!}--}}
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}