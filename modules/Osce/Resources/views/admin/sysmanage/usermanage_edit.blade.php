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
            <li><a href="#">OSCE系统</a></li>
            <li class="route-active">用户管理</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">用户编辑</h5>
            </div>
        </div>
        <form class="form-horizontal" id="Form3" novalidate="novalidate" action="{{route('osce.admin.user.postEditUser')}}" method="post">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">姓名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control name edit-name" value="{{$item->name}}" name="name" />
                        <input type="hidden" class="edit-hidden-name" value="{{$item->id}}" name="id" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">性别</label>
                    <div class="col-sm-offset-2" style="padding-left: 15px;">
                        <input type="radio" class="check_icon edit-man" name="gender" {!! $item->gender=='男'? 'checked="checked"':'' !!}   value="1"/> <span style="padding-right: 40px;">男</span>
                        <input type="radio" class="check_icon edit-woman" name="gender" {!! $item->gender=='女'? 'checked="checked"':'' !!}  value="2" /> <span>女</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">手机号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control mobile edit-mobile" name="mobile" value="{{$item->mobile}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2">
                        <button type="submit" class="btn btn-primary btn-edit" data-dismiss="modal" aria-hidden="true">确定</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}