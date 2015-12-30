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
                var url = "/msc/admin/laboratory/delete-lab?id="+this_id;
                //询问框
                layer.confirm('您确定要删除该实验室？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    window.location.href=url;
                });
            });
//            停用
            $(".stop").click(function(){
                var this_id = $(this).attr('data');
                var url = "/msc/admin/laboratory/stop-lab?id="+this_id;
                //询问框
                layer.confirm('您确定要停用实验室？', {
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
                    hospital: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '请选择所属分院'
                            }

                        }
                    },
                    building: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '请选择教学楼'
                            }

                        }
                    },
                    floor: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '请选择楼层'
                            }

                        }
                    },
                    name: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '实验室名称不能为空'
                            }
                        }
                    },
                    number: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '房号不能为空'
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

            $('.school').change(function(){
                var id = $(this).val();
                var opstr = '<option value="-1">请选择教学楼</option>';
                $.ajax({
                    type: "POST",
                    url: "{{route('msc.admin.laboratory.getLocal')}}",
                    data: {id:id},
                    success: function(msg){
                        if(msg){
                            $(msg).each(function(i,k){

                                opstr += '<option value="'+k.id+'">'+k.name+'</option>';
                            });
                            $('.local').html(opstr);
                        }
                    }
                });
            });

            $('#add_from').delegate('.local','change',function(){
                var id = $(this).val();
                var opstr = '<option value="-1">请选择楼层</option>';
                $.ajax({
                    type: "POST",
                    url: "{{route('msc.admin.laboratory.getFloor')}}",
                    data: {id:id},
                    success: function(msg){
                        if(msg){
                            $(msg).each(function(i,k){

                                opstr += '<option value="'+i+'">'+k+'</option>';
                            });
                            $('.floor').html(opstr);
                        }
                    }
                });
            });

            $('.update').click(function () {
                $('input[name=code]').val($(this).parent().parent().find('.code').html());
                //$('input[name=floor_top]').val($(this).parent().parent().find('.lname').attr('data'));
                $('input[name=floor]').val($(this).parent().parent().find('.floors').attr('data-b'));
               // $('input[name=open_type]').val($(this).parent().parent().find('.open_type').html());
                $('input[name=name]').val($(this).parent().parent().find('.name').html());
                $('input[name=short_name]').val($(this).parent().parent().find('.short_name').val());
                $('input[name=enname]').val($(this).parent().parent().find('.enname').val());
                $('input[name=short_enname]').val($(this).parent().parent().find('.short_enname').val());

                $('.school option').each(function(){
                    if($(this).val() == $('.lname').attr('data')){
                        $(this).attr('selected','selected');
                    }
                });

                var floor_op = '<option value="'+$('.floors').html()+'">'+$('.floors').html()+'</option>';
                $('.floor').html(floor_op);
                var str = '<option value="'+$('.lname').attr('data-local')+'">'+$('.lname').html()+'</option>';
                $('.local').html(str);
                var status = $(this).parent().parent().find('.status').attr('data');
                $('.sta option').each(function(){
                    if($(this).val() == status){
                        $(this).attr('selected','selected');
                    }
                });

                var teach = $(this).parent().parent().find('.tname').attr('data');
                $('.teacher option').each(function(){
                    if($(this).val() == teach){
                        $(this).attr('selected','selected');
                    }
                });
                var open_type = $(this).parent().parent().find('.open_type').attr('data');
                $('.opentype option').each(function(){
                    if($(this).val() == open_type){
                        $(this).attr('selected','selected');
                    }
                });
                $('#add_from').attr('action','{{route("msc.admin.laboratory.getEditLabInsert")}}');
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
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{@$keyword}}">
                        <input type="hidden" name="status" class="input-sm form-control" value="{{@$status}}">
                        <input type="hidden" name="open_type" class="input-sm form-control" value="{{@$open_type}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button class="btn btn_pl btn-success right">
                    <a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                        <span style="color: #fff;">新增实验室</span>
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
                            <th>实验室名称</th>
                            <th>房号</th>
                            <th>教学楼</th>
                            <th>楼层</th>
                            {{--<th>容量</th>--}}
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        类型
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">


                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=0">不开放</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=1">只对学生开放</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=2">只对老师开放</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=3">对所有人开放</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=4">对指定用户开放</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>管理员</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&open_type={{@$open_type}}&status=-1">全部</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status=0&open_type={{@$open_type}}">正常</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status=1&open_type={{@$open_type}}">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($datalist))
                            @foreach($datalist as $k=>$v)
                                <tr>
                                    <td>{{@$k+1}}</td>
                                    <td class="name">{{@$v['name']}}</td>
                                    <td class="code">{{@$v['code']}}</td>
                                    <td class="lname" data="{{@$v->school_id}}" data-local="{{@$v->location_id}}">{{@$v['lname']}}</td>
                                    <td class="floors">{{@$v['floor']}}</td>
                                    <td class="open_type" data="{{@$v->opentype}}">{{@$v['open_type']}}</td>
                                    <td class="tname" data="{{@$v['tid']}}">{{@$v['tname']}}</td>
                                    <td class="status" data="{{@$v['status']}}">@if($v['status'] == 0)正常@else停用@endif</td>
                                    <input type="hidden" class="short_name" value="{{@$v->short_name}}">
                                    <input type="hidden" class="enname" value="{{@$v->enname}}">
                                    <input type="hidden" class="short_enname" value="{{@$v->short_enname}}">
                                    <td>
                                        <a href=""  data="{{$v['id']}}"  class="state1 edit update" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                                            <span>编辑</span>
                                        </a>
                                        <a  data="{{$v['id']}}" class="state2 modal-control stop">停用</a>
                                        <a data="{{$v['id']}}" class="state2 edit_role modal-control delete">删除</a>
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
            <?php echo $datalist->render();?>
        </div>
	</div>
@stop

@section('layer_content')

    <form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{route('msc.admin.laboratory.getAddLabInsert')}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增实验室/编辑实验室</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label>
                <div class="col-sm-9">
                    <select id="select_Category" class="form-control m-b school" name="hospital">
                        <option value="-1">请选择所属分院</option>
                        @if(!empty($school))
                            @foreach($school as $ss)
                                <option value="{{$ss->id}}">{{$ss->name}}</option>
                            @endforeach
                                @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label>
                <div class="col-sm-9">
                    <select id="select_Category" class="form-control m-b local" name="building">
                        <option value="-1">请选择教学楼</option>

                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label>
                <div class="col-sm-9">
                    <select id="select_Category" class="form-control m-b floor" name="floor">
                        <option value="-1">请选择楼层</option>

                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>简称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="short_name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>英文全称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="enname" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>引文缩写</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="short_enname" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>房号</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="" />
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label class="col-sm-3 control-label">容量</label>--}}
                {{--<div class="col-sm-9">--}}
                    {{--<input type="text" class="form-control describe add-describe" name="capacity" />--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                <label class="col-sm-3 control-label">管理员</label>
                <div class="col-sm-9">
                    <select id="select_Category" class="form-control m-b teacher" name="manager_user_id">
                        <option value="-1">点击选择</option>
                        @if(!empty($teacher))
                            @foreach($teacher as $tch)
                                <option value="{{$tch->id}}">{{$tch->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">实验室性质</label>
                <div class="col-sm-9">
                    <select id="select_Category" class="form-control m-b opentype" name="open_type">
                        <option value="-1">点击选择</option>
                        <option value="0">不开放</option>
                        <option value="1">只对学生开放</option>
                        <option value="2">只对老师开放</option>
                        <option value="3">对所有人开放</option>
                        <option value="4">对指定用户开放</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b sta" name="status">
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