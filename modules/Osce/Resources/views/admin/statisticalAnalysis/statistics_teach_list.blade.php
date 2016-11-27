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
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">班级成绩明细</h5>
            </div>
            <div class="col-xs-6 col-md-2 right">
                <a  href="javascript:history.go(-1)" class="btn btn-outline btn-default right">&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="row tabs">
                <div class="col-sm-2 col-md-3">考试：<span>2016年度OSCE考试第1期</span></div>
                <div class="col-sm-2 col-md-4">
                    考试时间：<span>2016-01-06 09:00 ~ 12:00</span>
                </div>
                <div class="col-sm-2 col-md-2">科目：<span>冠心病问诊</span></div>
                <div class="col-sm-2 col-md-3">班级：<span>临床一班</span></div>
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
                    <tr>
                        <td>1</td>
                        <td>张三</td>
                        <td>2015</td>
                        <td>10</td>
                        <td>20</td>
                        <td>张老师</td>
                    </tr>
                </tbody>
            </table>
            @if ($studentList->count() > 0)
            <div class="pull-left">
                共{{$studentList->total()}}条
            </div>
            <div class="btn-group pull-right">
               {!! $studentList->appends($_GET)->render() !!}
            </div>
            @else
                <div class="pull-left">
                    共0条
                </div>
                <div class="btn-group pull-right">

                </div>
            @endif
        </div>

    </div>
@stop{{-- 内容主体区域 --}}