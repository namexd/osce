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
    
@stop


@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">用户管理</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.user.getAddUser')}}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>编号</th>
                <th>帐号</th>
                <th>姓名</th>
                <th>性别</th>
                <th>角色</th>
                <th>联系电话</th>
                <th>最近登录</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @forelse($list as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->username}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->gender}}</td>
                    <td>{{$item->roles[0]->name}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->lastlogindate}}</td>
                    <td>
                        <a href="{{route('osce.admin.user.getEditStaff',['id'=>$item->id])}}">
                            <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                        </a>
                        <a class="state1 modal-control" href="{{ route('osce.admin.user.getChangeUsersRole',array('id'=>$item->id)) }}"><i class="fa  fa-cog fa-2x"></i></a>
                        <a href="javascript:void(0)"><span class="read state2"><i class="fa fa-trash-o fa-2x" uid="{{$item->id}}"></i></span></a>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
        <br/>
        <div class="pull-left">
            共{{$list->total()}}条
        </div>
        <div class="btn-group pull-right">
            <nav>
                <ul class="pagination">
                    {!! $list->appends($_GET)->render() !!}
                </ul>
            </nav>
        </div>
    </form>
</div>
<script>
    $(function(){
        //删除用户
        $(".fa-trash-o").click(function(){
            var thisElement=$(this);
            var uid=thisElement.attr("uid");

            layer.confirm('确认删除？', {
                title:'删除',
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type:'post',
                    async:true,
                    url:'{{route('osce.admin.user.postDelUser')}}',
                    data:{id:uid},
                    success:function(data){
                        if(data.code == 1){
                            location.reload();
                        }else {
                            layer.msg(data.message,{skin:'msg-error',type:1});
                        }
                    }
                })
            });
        })

    })
</script>
@stop{{-- 内容主体区域 --}}