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
//            删除
            $(".delete").click(function(){
                var this_id = $(this).attr('data');
                var url = "/msc/admin/floor/delete-floor?id="+this_id;
                //询问框
                layer.confirm('您确定要删除该楼栋？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    window.location.href=url;
                });
            });
//            停用
            $(".stop").click(function(){
                var this_id = $(this).attr('data');
                var url = "/msc/admin/floor/stop-floor?id="+this_id;
                //询问框
                layer.confirm('您确定要停用该楼栋？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    window.location.href=url;
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
                    name: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '楼栋名称不能为空'
                            }
                        }
                    },
                    up: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地上层数不能为空'
                            }
                        }
                    },
                    down: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地下层数不能为空'
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

            $('.edit').click(function () {
                $('input[name=name]').val($(this).parent().parent().find('.name').html());
                $('input[name=floor_top]').val($(this).parent().parent().find('.floor').attr('data'));
                $('input[name=floor_buttom]').val($(this).parent().parent().find('.floor').attr('data-b'));
                $('input[name=address]').val($(this).parent().parent().find('.address').html());
                var sname = $(this).parent().parent().find('.sname').html();
                var status = '';
                if($(this).parent().parent().find('.status').html() == '正常'){
                    status = 1;
                }else{
                    status = 0;
                }
                $('.school option').each(function(){
                    if($(this).html() == sname){
                        $(this).attr('selected','selected');
                    }
                });

                $('.state option').each(function(){
                    if($(this).val() == status){
                        $(this).attr('selected','selected');
                    }
                });
                $('#add_from').attr('action','{{route("msc.admin.floor.getEditFloorInsert")}}');
                var id = $(this).attr("data");
                $('#add_from').append('<input type="hidden" name="id" value="'+id+'">');
            });

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
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{$keyword}}">
                        <input type="hidden" name="status" class="input-sm form-control" value="{{@$status}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button class="btn btn-w-m btn_pl btn-success right">
                    <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                        <span style="color: #fff;">新增楼栋</span>
                    </a>
                </button>
            </div>
		</div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <form action="" class="container-fluid" id="list_form">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>楼栋名称</th>
                            <th>楼层数</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        所属分院
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(!empty($school))
                                            @foreach($school as $sch)
                                                <li>
                                                    <a href="/msc/admin/floor/index?keyword={{$keyword}}&status={{@$status}}">{{@$sch->name}}</a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </th>
                            <th>地址</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}">全部</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}&status=1">正常</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}&status=0">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($data))
                            @foreach($data as $k=>$v)
                            <tr>
                                <td>{{@$k}}</td>
                                <td class="name">{{@$v->name}}</td>
                                <td  class="floor" data="{{@$v->floor_top}}" data-b="{{@$v->floor_buttom}}">{{intval(@$v->floor_top) + intval(@$v->floor_buttom)}}</td>
                                <td class="sname">{{@$v->sname}}</td>
                                <td class="address">{{@$v->address}}</td>
                                <td class="status" data="{{@$v->status}}">@if($v->status)正常@else停用@endif</td>
                                <td>
                                    <a href=""  data="{{$v->id}}"  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                                        <span>编辑</span>
                                    </a>
                                    <a  data="{{$v->id}}" class="state2 modal-control stop">停用</a>
                                    <a data="{{$v->id}}" class="state2 edit_role modal-control delete">删除</a>
                                    <input type="hidden" class="setid" value="1"/>
                                </td>
                            </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>
                </form>

            </div>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <?php echo $data->render();?>
        </div>
	</div>
@stop

@section('layer_content')
{{--编辑--}}
    <form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{route('msc.admin.floor.getAddFloorInsert')}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增楼栋/编辑楼栋</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>楼栋名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地上)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control name add-name" name="floor_top" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地下)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control name add-name" name="floor_buttom" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">地址</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="address" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">所属分院</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b school" name="school_id">

                        @if(!empty($school))
                            @foreach($school as $ss)
                                <option value={{$ss->id}}">{{$ss->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b state" name="status">
                        <option value="-1">请选择状态</option>
                        <option value="1">正常</option>
                        <option value="0">停用</option>
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