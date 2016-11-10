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

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>考核项</th>
                <th>平均成绩</th>
                <th>考试人数</th>
                <th>通过率</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $detail)
                <tr>
                    <td>{{$detail['number']}}</td>
                    <td>{{$detail['standardContent']}}</td>
                    <td>{{$detail['scoreAvg']}}</td>
                    <td>{{$detail['studentQuantity']}}</td>
                    <td>{{$detail['qualifiedPass']}}</td>

                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@stop{{-- 内容主体区域 --}}