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
    <script src="{{asset('osce/admin/sysmanage/js/sysmanage.js')}}" ></script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'authorization_add'}" />
<div class="ibox-title route-nav">
    <ol class="breadcrumb">
        <li><a href="#">资源管理</a></li>
        <li class="route-active">场所管理</li>
    </ol>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">场所管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.Place.getAddPlace')}}" class="btn btn-primary" style="float: right;">新增</a>
            </div>
        </div>
    <form class="container-fluid ibox-content" id="list_form">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a href="#">考场</a></li>
                        <li class="active"><a href="#">中控室</a></li>
                        <li class=""><a href="#">走廊</a></li>
                        <li class=""><a href="#">候考室</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="input-group" style="width: 290px;margin:20px 0;">
                <input type="text" placeholder="请输入关键字" class="input-sm form-control">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-sm btn-primary" id="search">搜索</button>
                </span>
            </div>

            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>场所名称</th>
                    <th>描述</th>
                    <th>操作</th>

                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>反对撒法</td>
                        <td>fgs</td>
                        <td>
                            <a href="#"><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-group pull-right">
               
            </div>
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}