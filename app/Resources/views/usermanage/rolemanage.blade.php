@extends('layouts.usermanage')

@section('only_css')
	<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/css/common.css')}}" rel="stylesheet">
    <style>
    .btn-success{
        background-color: #16beb0!important;
        border-color: #16beb0!important;
    }
    .modal-title{color: #676a6c!important;}
    tbody td .state1{color: #1ab394!important;}
    body {
        font-family: 微软雅黑;
        font-size: 14px;
    }
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('msc/admin/usermanage/rolemanage.js')}}"></script>
    <script type="text/javascript">
    	$(function(){
	    	$('#Form1').bootstrapValidator({
	              message: 'This value is not valid',
	              feedbackIcons: {/*输入框不同状态，显示图片的样式*/
	                  valid: 'glyphicon glyphicon-ok',
	                  invalid: 'glyphicon glyphicon-remove',
	                  validating: 'glyphicon glyphicon-refresh'
	              },
	              fields: {/*验证*/
	                  name: {/*键名username和input name值对应*/
	                      message: 'The username is not valid',
	                      validators: {
	                          notEmpty: {/*非空提示*/
	                              message: '用户名不能为空'
	                          }
	                      }
	                  },
	                  description: {
	                      validators: {
	                          notEmpty: {
	                              /*非空提示*/
	                              message: '角色描述不能为空'
	                          }
	                      }
	                  }
	              }
	        });
	        $('#Form2').bootstrapValidator({
	              message: 'This value is not valid',
	              feedbackIcons: {/*输入框不同状态，显示图片的样式*/
	                  valid: 'glyphicon glyphicon-ok',
	                  invalid: 'glyphicon glyphicon-remove',
	                  validating: 'glyphicon glyphicon-refresh'
	              },
	              fields: {/*验证*/
	                  name: {/*键名username和input name值对应*/
	                      message: 'The username is not valid',
	                      validators: {
	                          notEmpty: {/*非空提示*/
	                              message: '用户名不能为空'
	                          }
	                      }
	                  },
	                  description: {
	                      validators: {
	                          notEmpty: {
	                              /*非空提示*/
	                              message: '角色描述不能为空'
	                          }
	                      }
	                  }
	              }
	        });
        })
    </script>
@stop

@section('content')
    @if($errors->first('chargeError'))
        <div class="alert alert-success alert-dismissable" style="text-align: center">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4> <i class="icon fa fa-check"></i> 提示！</h4>
            {{$errors->first('chargeError')}}
        </div>
    @endif

    <input type="hidden" id="parameter" value="{'pagename':'rolemanage'}" />
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>角色权限管理</h5>
                <button type="button" class="btn btn-primary" id="add_role" style="background:#16BEB0;border:1px solid #16BEB0;margin:0;float:right;margin-top:-10px;" data-toggle="modal" data-target="#myModal">新增角色</button>
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
                            <span class="state1 edit_role modal-control" data-toggle="modal" data-target="#myModal" data="{{@$role->id}}"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                            <a class="state1 modal-control" href="{{ route('auth.SetPermissions',[@$role->id]) }}"><i class="fa  fa-cog fa-2x"></i></a>
                            <span class="state2 delete" data="{{@$role->id}}"><i class="fa fa-trash-o fa-2x"></i></span>
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
    <form class="form-horizontal" id="Form1" novalidate="novalidate" method="post" action="{{url('/auth/add-new-role')}}">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增角色</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">角色名称：</label>
                <div class="col-sm-9">
                    <input type="text" name="name" class="form-control" placeholder="请输入文本">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">角色描述：</label>
                <div class="col-sm-9">
                    <input type="text" name="description" class="form-control" placeholder="请输入文本">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='sure' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
            <button type="button" class="btn btn-white"data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>

    <form type="post" action="{{ url('/auth/edit-role') }}" class="form-horizontal" id="Form2" novalidate="novalidate" style="display: none" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑角色</h4>
        </div>
        <div class="modal-body">
            <input id="edit_id" type="hidden" name="id" class="form-control" placeholder="请输入文本" value="">
            <div class="form-group">
                <label class="col-sm-3 control-label">角色名称：</label>
                <div class="col-sm-9">
                    <input id="edit_name" type="text" name="name" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">角色描述：</label>
                <div class="col-sm-9">
                    <input id="edit_des" type="text" name="description" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='sure-notice' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>
@stop{{-- 内容主体区域 --}}