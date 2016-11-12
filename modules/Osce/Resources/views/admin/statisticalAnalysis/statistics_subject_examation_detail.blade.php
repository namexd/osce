@extends('osce::admin.layouts.admin_index')

@section('only_css')
<style>
    body{background-color: #fff!important;}
</style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="container-fluid ibox-content">
        <div class="row">
        <div class="col-sm-6"><p>考试：{{$title['examName']}}</p></div>
        <div class="col-sm-6">
            <p>考试时间：{{$title['time']}}</p>
        </div>
        <div class="col-sm-6"><p>考试项目：{{$title['subjectTitle']}}</p></div>
        <div class="col-sm-6"><p>考站名称：{{$title['stationName']}}</p></div>
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
            @forelse($stationDetails as $key => $detail)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$detail->studentName}}</td>
                    <td>{{$detail->begin_dt.'~'.$detail->end_dt}}</td>
                    <td>{{$detail->time}}</td>
                    <td>{{$detail->score? :0.00}}</td>
                    <td>{{$detail->teacherName}}</td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

    </div>
@stop{{-- 内容主体区域 --}}