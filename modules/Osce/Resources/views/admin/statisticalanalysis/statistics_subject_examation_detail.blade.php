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
        <div class="col-sm-6"><p>科目：{{$title['subjectTitle']}}</p></div>
        <div class="col-sm-6"><p>班级：{{$title['stationName']}}</p></div>
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
            @foreach($stationDetails as $detail)
                <tr>
                    <td>{{$detail['number']}}</td>
                    <td>{{$detail['studentName']}}</td>
                    <td>{{$detail['begin_dt']}}</td>
                    <td>{{$detail['time']}}</td>
                    <td>{{$detail['score']}}</td>
                    <td>{{$detail['teacherName']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@stop{{-- 内容主体区域 --}}