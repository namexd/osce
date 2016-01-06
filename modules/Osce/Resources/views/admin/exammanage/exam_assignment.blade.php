@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
    
@stop


@section('content')
<div class="ibox-title route-nav">
    <ol class="breadcrumb">
        <li><a href="#">考试管理</a></li>
        <li class="route-active">考试安排</li>
    </ol>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考试安排</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.exam.getAddExam')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>考试名称</th>
                <th>时间</th>
                <th>考试组成</th>
                <th>考试人数</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->begin_dt}}~{{$item->end_dt}}</td>
                    <td>{{$item->description}}</td>
                    <td>{{$item->total}}</td>
                    <td>
                        <a href="#"><span class="read  state1 detail"><i class="fa  fa-cog"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="btn-group pull-right">
           
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}