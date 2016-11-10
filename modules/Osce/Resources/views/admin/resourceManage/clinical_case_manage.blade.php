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
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'clinical_case_manage','deletes':'{{route('osce.admin.case.postDelete')}}',
'firstpage':'{{route('osce.admin.case.getCaseList')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">病例管理</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.case.getCreateCase')}}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <div class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>病例</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td><span class="description" title="{{$item->name}}">{{$item->name}}</span></td>
                    <td><span class="description" title="{{$item->description}}">{{$item->description}}</span></td>
                    <td>
                        <a href="{{route('osce.admin.case.getEditCase')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                        <a href="javascript:void(0)" class="delete" value="{{$item->id}}"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pull-left">
            共{{$data->total()}}条
        </div>
        <div class="btn-group pull-right">
            <nav>
                <ul class="pagination">
                    {!! $data->appends($_GET)->render() !!}
                </ul>
            </nav>
           
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}