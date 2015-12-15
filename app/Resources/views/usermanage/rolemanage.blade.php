@extends('layouts.usermanage')

@section('only_css')
    <link href="{{asset('')}}" rel="stylesheet">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/usermanage/rolemanage.js')}}"></script>
@stop

@section('content')

    <input type="hidden" id="parameter" value="{'pagename':'rolemanage'}" />
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title overflow">
                <h5>角色权限管理</h5>
                <button type="button" class="btn btn-w-m btn-primary right" id="add_role" data-toggle="modal" data-target="#myModal">新增角色</button>
            </div>
            <div class="container-fluid ibox-content">
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>角色名称</th>
                        <th>描述</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                @foreach($roleList as $role)
                    <tr>
                        <td class="open-id">{{@$role->id}}</td>
                        <td class="role_name">{{@$role->name}}</td>
                        <td class="role_descrip">{{@$role->description}}</td>

                        <td class="opera">
                            <span class="state1 edit_role modal-control" data-toggle="modal" data-target="#myModal" data="{{@$role->id}}">编辑</span>
                            <span class="state1 modal-control" >设置权限</span>
                            <span class="state2 delete" data="{{@$role->id}}">删除</span>
                        </td>
                    </tr>
                @endforeach
                    </tbody>
                </table>
                <div class="pull-right">


                </div>
            </div>

        </div>


    </div>

@stop{{-- 内容主体区域 --}}

@section('layer_content')
    <form class="form-horizontal" id="Form1" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增角色</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">角色名称：</label>
                <div class="col-sm-9">
                    <input type="text" name="" class="form-control" placeholder="请输入文本">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">角色描述：</label>
                <div class="col-sm-9">
                    <input type="text" name="" class="form-control" placeholder="请输入文本">

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='sure-notice' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>

    <form type="post" action="{{ url('/auth/edit-role') }}" class="form-horizontal" id="Form2" novalidate="novalidate" style="display: none" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑角色</h4>
        </div>
        <div class="modal-body">
            <input id="edit_id" type="hidden" name="" class="form-control" placeholder="请输入文本" value="">
            <div class="form-group">
                <label class="col-sm-3 control-label">角色名称：</label>
                <div class="col-sm-9">
                    <input id="edit_name" type="text" name="" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">角色描述：</label>
                <div class="col-sm-9">
                    <input id="edit_des" type="text" name="" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='sure-notice' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>
@stop{{-- 内容主体区域 --}}