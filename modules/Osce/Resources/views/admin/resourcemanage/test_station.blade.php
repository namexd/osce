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
    <div class="panel blank-panel">
        <div class="ibox-title route-nav">
            <ol class="breadcrumb">
                <li><a href="#">资源管理</a></li>
                <li class="route-active">考站管理</li>
            </ol>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row table-head-style1 ">
                <div class="col-xs-6 col-md-2">

                    <div class="input-group" style="width: 290px;">
                        <input type="text" placeholder="请输入关键字" class="input-sm form-control">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                </span>
                    </div>

                </div>
                <div class="col-xs-6 col-md-2" style="float: right;">
                    <a  href="#" class="btn btn-primary marl_10" style="float: right;">&nbsp;新增&nbsp;</a>
                </div>
            </div>
            <form class="container-fluid ibox-content" id="list_form">
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>考站名称</th>
                        <th>类型</th>
                        <th>科目</th>
                        <th>操作</th>

                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>A</td>
                            <td>A</td>
                            <td>麻醉</td>
                            <td>
                                <a href=""><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                                <a href=""><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}