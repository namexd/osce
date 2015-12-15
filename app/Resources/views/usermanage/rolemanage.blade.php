@extends('layouts.usermanage')

@section('only_css')
    <link href="{{asset('')}}" rel="stylesheet">
@stop

@section('only_js')
    <script>
        /**
         *角色权限管理弹出框处理
         *吴冷眉
         *QQ：2632840780
         *2015-12-15
         *update：wulengmei（2015-12-15 17:25） （最近更新/更改 作者及时间）
         **/
        var pars;
        $(function(){

            delete_user();
        });
        function rolemanage(){
            var url = pars.ajaxurl;
            console.log("测试引用"+url);
            delete_user();
        }
        function  choice_from(){
            $("#add_role").click(function(){
                $("#Form1").show();
                $("#Form2").hide();
            })
            $("#edit_role").click(function(){
                $("#Form2").show();
                $("#Form1").hide();
            })
        }
        function  delete_user(){
            $(".delete").click(function(){
                parent.layer.alert('确定删除管理员XXXXX', {
                    skin: 'layui-layer-molv' //样式类名
                });
            })
        }

    </script>
@stop

@section('content')

    <input type="hidden" id="parameter" value="{'pagename':'rolemanage','ajaxurl':' '}" />
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

                    <tr>
                        <td class="open-id">1</td>
                        <td>超级管理员</td>
                        <td>超级管理员就是最大最大的帅哥！！！！！</td>

                        <td class="opera">
                            <span class="state1 modal-control" data-toggle="modal" data-target="#myModal" id="edit_role" >编辑</span>
                            <span class="state1 modal-control" >设置权限</span>
                            <span class="state2 delete" >删除</span>
                        </td>
                    </tr>
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

    <form class="form-horizontal" id="Form2" novalidate="novalidate" style="display: none">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑角色</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">角色名称：</label>
                <div class="col-sm-9">
                    <input type="text" name="" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">角色描述：</label>
                <div class="col-sm-9">
                    <input type="text" name="" class="form-control" placeholder="请输入文本" value="绑定的内容">

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='sure-notice' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>
@stop{{-- 内容主体区域 --}}