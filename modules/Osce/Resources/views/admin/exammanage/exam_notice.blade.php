@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">考试管理</a></li>
            <li class="route-active">资讯&通知</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">资讯&通知</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>标题</th>
                        <th>发布时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="btn-group pull-right">

                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}