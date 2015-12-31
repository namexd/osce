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
        <li><a href="#">系统管理</a></li>
        <li class="route-active">用户管理</li>
    </ol>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">用户管理</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.Place.getAddPlace')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>帐号</th>
                <th>姓名</th>
                <th>角色</th>
                <th>联系电话</th>
                <th>最近登录</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>zhangsan</td>
                    <td>张三</td>
                    <td>系统管理员</td>
                    <td>13800000000</td>
                    <td>2015/12/23 12:23:52</td>
                    <td>
                        <a href="#"><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="btn-group pull-right">
           
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}