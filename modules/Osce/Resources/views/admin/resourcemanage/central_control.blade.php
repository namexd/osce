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
                <th>场所名称</th>
                <th>描述</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->status}}</td>
                    <td class="state2"><i class="fa fa-trash-o"></i></td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        <div class="btn-group pull-right">
           
        </div>
    </form>
    <div ng-include="'configs.html'"></div>
</div>
@stop{{-- 内容主体区域 --}}