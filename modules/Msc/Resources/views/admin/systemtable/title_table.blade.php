@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
<script>
    $(function(){
        $(".delete").click(function(){
            var this_id = $(this).siblings(".setid").val();
            //询问框
            layer.confirm('您确定要删除该职称？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                layer.msg('删除成功', {icon: 1,time: 1000});
            });
        })
        $(".stop").click(function(){
            var this_id = $(this).siblings(".setid").val();

            //询问框
            layer.confirm('您确定要停用该职称？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                layer.msg('停用成功', {icon: 1,time: 1000});
            });
        })
        $('#add_from').bootstrapValidator({
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
                type: {
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '请选择状态'
                        }
                    }
                },

            }
        });
    })

</script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-default" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button href="/msc/admin/lab/had-open-lab-add" class="right btn btn-success">新增职称</button>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>名称</th>
                    <th>描述</th>
                    <th>
                        {{--<input type="hidden" name="status" value="{{$status}}">--}}
                        {{--<input type="hidden" name="manager_name" value="{{manager_name}}">--}}
                        {{--<input type="hidden" name="opened" value="{{opened}}">--}}

                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle" type="button">状态<span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="">正常</a>
                                </li>
                                <li>
                                    <a href="">停用</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>操作</th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="number">1</td>
                    <td class="name">主任医师</td>
                    <td class="describe">
                        主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述
                    </td>
                    <td class="type">
                        <span class="state2">停用</span>
                    </td>
                    <td class="opera">
                        <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal"><span>编辑</span> </a>
                        <span class="state1 stop">停用</span>
                        <span class="state1 delete">删除</span>
                        <input type="hidden" class="setid" value="1"/>
                    </td>
                </tr>
                <tr>
                    <td class="number" setid="2">2</td>
                    <td class="name">医师</td>
                    <td class="describe">
                        主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述
                    </td>
                    <td class="type">
                        <span>正常</span>
                    </td>
                    <td class="opera">
                        <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal"><span>编辑</span> </a>
                        <span class="state1 stop">停用</span>
                        <span class="state1 delete">删除</span>
                        <input type="hidden" class="setid" value="2"/>
                    </td>
                </tr>
                </tbody>
            </table>

        </form>
    </div>
@stop

@section('layer_content')
<!--新增-->
<form class="form-horizontal" id="add_from" novalidate="novalidate" action="/msc/admin/user/student-add" method="post">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">新增职称/编辑职称</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="dot">*</span>职称名称</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="name" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">职称描述</label>
            <div class="col-sm-9">
                <input type="text" class="form-control describe add-describe" name="describe" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
            <div class="col-sm-9">
                <select id="select_Category"   class="form-control m-b" name="type">
                    <option value="-1">请选择状态</option>
                    <option value="0">正常</option>
                    <option value="1">停用</option>
                </select>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2 right">
                <button class="btn btn-primary"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                <button class="btn btn-white2 right" type="button" data-dismiss="modal">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button>
            </div>
        </div>

    </div>
</form>

<!--删除-->

@stop