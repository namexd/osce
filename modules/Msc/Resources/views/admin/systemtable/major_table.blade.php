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
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
        $(function(){
//            删除
            $(".delete").click(function(){
                var this_id = $(this).siblings(".setid").val();
                //询问框
                layer.confirm('您确定要删除该专业？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    layer.msg('删除成功', {icon: 1,time: 1000});
                });
            });
//            停用
            $(".stop").click(function(){
                var this_id = $(this).siblings(".setid").val();

                //询问框
                layer.confirm('您确定要停用该专业？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    layer.msg('停用成功', {icon: 1,time: 1000});
                });
            });
//            编辑
            $('#add_from').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    number: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '专业代码不能为空'
                            }
                        }
                    },
                    name: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '专业名称不能为空'
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
                    }

                }
            });

//            导入
            $("#in").click(function(){
                $("#load_in").click();
            });
            $("#load_in").change(function(){
                var str=$("#load_in").val().substring($("#load_in").val().lastIndexOf(".")+1);
                if(str != "xlsx"){
                    layer.alert(
                            "请上传正确的文件格式？",
                            {title:["温馨提示","font-size:16px;color:#408aff"]}
                    );
                }else{
                    $.ajaxFileUpload({
                        type:"post",
                        url:"",
                        fileElementId:"load_in",
                        success:function(data,status){

                        },
                        error:function(){
                            console.log("失败");
                            layer.alert(
                                    "上传失败！",
                                    {title:["温馨提示","font-size:16px;color:#408aff"]}
                            );
                        }
                    })
                }
            })
        })
    </script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="" />
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row table-head-style1">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button class="btn btn_pl btn-success right">
                    <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                        <span style="color: #fff;">新增专业</span>
                    </a>
                </button>
                <button class="btn btn_pl btn-white right button_margin" id="in">导入专业</button>
                <input type="file" name="training" id="load_in" style="display: none" value="">
            </div>
		</div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <form action="" class="container-fluid" id="list_form">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>专业代码</th>
                            <th>专业名称</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#">全部</a>
                                        </li>
                                        <li>
                                            <a href="#">正常</a>
                                        </li>
                                        <li>
                                            <a href="#">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>0001</td>
                            <td>临床医学</td>
                            <td>正常</td>
                            <td>
                                <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none"><span>编辑</span> </a>
                                <a class="state2 modal-control stop">停用</a>
                                <a class="state2 edit_role modal-control delete">删除</a>
                                <input type="hidden" class="setid" value="1"/>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>0002</td>
                            <td>临床医学</td>
                            <td class="state2">停用</td>
                            <td>
                                <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none"><span>编辑</span> </a>
                                <a class="state2 modal-control stop">停用</a>
                                <a class="state2 edit_role modal-control delete">删除</a>
                                <input type="hidden" class="setid" value="1"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>

            </div>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <ul class="pagination">
                <li>
                    <span>«</span>
                </li>
                <li class="active">
                    <span>1</span>
                </li>
                <li>
                    <a>2</a>
                </li>
                <li>
                    <a>3</a>
                </li>
                <li>
                    <a>4</a>
                </li>
                <li>
                    <a>5</a>
                </li><li>
                    <a>6</a>
                </li>
                <li>
                    <a>7</a>
                </li>
                <li>
                    <a>»</a>
                </li>

            </ul>
        </div>
	</div>
@stop

@section('layer_content')
{{--编辑--}}
    <form class="form-horizontal" id="add_from" novalidate="novalidate" action="/msc/admin/user/student-add" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增专业/编辑专业</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>专业代码</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="number" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>专业名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="name" />
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
                    <button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>

@stop