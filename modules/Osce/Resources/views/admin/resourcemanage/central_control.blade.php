@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    #start,#end{width: 160px;}
    .input-group input{height: 34px;}

    </style>
@stop

@section('only_js')
    <script>
        $(function(){
            $('.fa-trash-o').click(function(){
                //console.log($('.active').attr('href'))
                var thisElement = $(this);
                layer.confirm('确认删除？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.ajax({
                        type:'post',
                        async:true,
                        url:"{{route('osce.admin.room.postDelete')}}",
                        data:{id:thisElement.parent().parent().parent().attr('value'),type:($('.active').find('a').attr('href')).split('=')[1]},
                        success:function(res){
                            if(res.code==1){
                                location.href = (location.href).split('?')[0];
                            }else{
                                layer.alert(res.message)
                            }
                        }
                    })
                });
            });
        })
    </script>
@stop


@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">中控室</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.room.getAddRoom',['type'=>$type])}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
    <form class="container-fluid ibox-content" id="list_form">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        @foreach($area as $key => $item)
                            <li class="{{($key === 1)?'active':''}}">
                                <a href="{{route('osce.admin.room.getRoomList',['type'=>$key])}}">{{$item}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>场所名称</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $k=>$item)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->description}}</td>
                        <td value="{{$item->id}}">
                            <a href="{{route('osce.admin.room.getEditRoom',['id'=>$item->id,'type'=>$type])}}">
                                <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                            </a>
                            <a href="javascript:void(0)"><span class="read  state1"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>

            <div class="pull-left">
                共{{$data->total()}}条
            </div>
            <div class="btn-group pull-right">
               {!! $data->appends($_GET)->render() !!}
            </div>
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}