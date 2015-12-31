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
            var this_id = $(this).attr('data');
            var url = "/msc/admin/professionaltitle/holder-remove?id="+this_id;
            //询问框
            layer.confirm('您确定要删除该职称？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
            });
        })

        $(".stop").click(function(){
            var this_id = $(this).attr('data');
            var type = $(this).attr('data-type');
//                alert(this_id);
            var url = "/msc/admin/professionaltitle/holder-status?id="+this_id+"&type="+type;
            var str = '';
            if(type == 1){
                str = '您确定要恢复职称？';
            }else{

                str = '您确定要禁用职称？';
            }
            //询问框
            layer.confirm(str, {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
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
        $('.edit').click(function () {
            $('input[name=name]').val($(this).parent().parent().find('.name').html());
            $('input[name=description]').val($(this).parent().parent().find('.describe').html());
//                var sname = $(this).parent().parent().find('.sname').html();
            var status = '';
            if($(this).parent().parent().find('.status').html() ==='正常'){
                status = 1;
            }else{
                status = 0;
            }
//            alert(status);
//            var status = $(this).parent().parent().find('.status').attr('data');
            $('.state option').each(function(){
//                alert(status);
                if($(this).val() == status){
                    $(this).attr('selected','selected');
                }

            });
            $('#add_from').attr('action','{{route("msc.admin.professionaltitle.HolderSave")}}');
            var id = $(this).attr("data");
            $('#add_from').append('<input type="hidden" name="id" value="'+id+'">');
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
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{@$keyword}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-default" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button href="/msc/admin/lab/had-open-lab-add" class="right btn btn-success" data-toggle="modal" data-target="#myModal">新增职称</button>
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
                                    <a href="{{route('msc.admin.professionaltitle.JobTitleIndex',['keyword'=>@$keyword,'status'=>'1'])}}">正常</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.professionaltitle.JobTitleIndex',['keyword'=>@$keyword,'status'=>'0'])}}">停用</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>操作</th>

                </tr>
                </thead>
                <tbody>
                @if(!empty($list))
                    @foreach($list as $val)
                        <tr>
                            <td class="number">{{ @$val['id'] }}</td>
                            <td class="name">{{ @$val['name'] }}</td>
                            <td class="describe">{{ @$val['description'] }}</td>
                            <td class="status" data="{{@$val['status']}}">@if(@$val['status']==1)正常@else<span class="state2">禁用</span>@endif</td>

                            <td class="opera">
                                <a href=""   data="{{@$val['id']}}" class="state1 edit" data-toggle="modal" data-target="#myModal"><span>编辑</span></a>
                                @if($val['status']==1)
                                    <a   data="{{@$val['id']}}"  data-type="0"  class="state2 modal-control stop">禁用</a>
                                @else
                                    <a   data="{{@$val['id']}}" data-type="1" class="state2 modal-control stop">恢复</a>
                                @endif
                                <span class="state2 delete" data="{{ @$val['id'] }}">删除</span>
                                <input type="hidden" class="setid" value="1"/>
                            </td>
                        </tr>
                    @endforeach
                @endif
                {{--<tr>--}}
                    {{--<td class="number">2</td>--}}
                    {{--<td class="name">医师</td>--}}
                    {{--<td class="describe">--}}
                        {{--主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述主任医师描述--}}
                    {{--</td>--}}
                    {{--<td class="type">--}}
                        {{--<span>正常</span>--}}
                    {{--</td>--}}
                    {{--<td class="opera">--}}
                        {{--<a href=""  class="state1 edit" data-toggle="modal" data-target="#myModal"><span>编辑</span> </a>--}}
                        {{--<span class="state1 stop">停用</span>--}}
                        {{--<span class="state1 delete">删除</span>--}}
                        {{--<input type="hidden" class="setid" value="2"/>--}}
                    {{--</td>--}}
                {{--</tr>--}}
                </tbody>
            </table>

        </form>
    </div>
@stop

@section('layer_content')
<!--新增-->
<form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{ route('msc.admin.professionaltitle.HolderAdd') }}" method="post">
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
                <input type="text" class="form-control describe add-describe" name="description" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
            <div class="col-sm-9">
                <select id="select_Category"   class="form-control m-b state" name="status">
                    <option value="-1">请选择状态</option>
                    <option value="1">正常</option>
                    <option value="0">禁用</option>
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