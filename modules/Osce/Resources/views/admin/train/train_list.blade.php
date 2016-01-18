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
    <script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':''}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考前培训</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{ route('osce.admin.getAddTrain')  }}" class="btn btn-outline btn-default" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>标题</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $list)
                <tr>
                    <td>1</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->begin_dt }}~{{ $list->end_dt }}</td>
                    <td value="1">
                        <a href="{{ route('osce.admin.getEditTrain',array('id'=>$list->id)) }}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
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