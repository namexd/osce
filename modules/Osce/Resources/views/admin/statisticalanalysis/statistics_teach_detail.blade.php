@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        .tabs{
            margin: 20px 0;
            font-weight: 700;
        }
    </style>
@stop

@section('only_js')

@stop


@section('content')
        <div class="container-fluid ibox-content">
            <div class="row">
                <div class="col-sm-6"><p>考试：{{@$data->name}}</p></div>
                <div class="col-sm-6"><p>考试时间：{{@$data->begin_dt}}</p></div>
                <div class="col-sm-6"><p>科目：{{@$data->title}}</p></div>
                <div class="col-sm-6"><p>班级：{{@$data->grade_class}}</p></div>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>考生</th>
                    <th>考试时间</th>
                    <th>耗时</th>
                    <th>成绩</th>
                    <th>评价老师</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty(@$datalist))
                    @foreach(@$datalist as $k=>$list)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$list->name}}</td>
                            <td>{{$list->begin_dt}}</td>
                            <td>{{$list->time}}</td>
                            <td>{{$list->score}}</td>
                            <td>{{$list->tname}}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{--@if ($studentList->count() > 0)--}}
            {{--<div class="pull-left">--}}
                {{--共{{$studentList->total()}}条--}}
            {{--</div>--}}
            {{--@else--}}
                {{--<div class="pull-left">--}}
                    {{--共0条--}}
                {{--</div>--}}
            {{--@endif--}}
        </div>
@stop{{-- 内容主体区域 --}}