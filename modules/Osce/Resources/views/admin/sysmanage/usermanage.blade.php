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
            <a  href="{{route('osce.admin.user.getAddUser')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;新增&nbsp;</a>
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
                @forelse($list as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->username}}</td>
                    <td>{{$item->name}}</td>
                    <td>-</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->lastlogindate}}</td>
                    <td>
                        <a href="{{route('osce.admin.user.getEditStaff',['id'=>$item->id])}}" class="status1" >编辑</a>
                        <a href="{{route('osce.admin.user.getDelUser',['id'=>$item->id])}}" class="status3" onclick="return confirm('确认删除');" >删除</a>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>

        <div class="btn-group pull-right">
            <nav>
                <ul class="pagination">
                    {!! $list->appends($_GET)->render() !!}
                </ul>
            </nav>
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}