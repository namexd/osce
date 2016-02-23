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
    <script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script> 
    
    <script>
		$(function(){

            $(".fa-trash-o").click(function(){
                var thisElement=$(this);

                layer.confirm('确认删除？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.ajax({
                        type:'get',
                        async:true,
                        url:"{{route('osce.admin.getDelTrain')}}?id="+thisElement.parent().parent().parent().attr('value'),
                        success:function(data){
                            if(data.code == 1){
                                location.href='{{route('osce.admin.getTrainList')}}?page=1';
                            }else {
                                layer.msg(data.message,{skin:'msg-error',type:1});
                            }
                        },
                        error:function(data){
                            layer.msg('没有权限！',{skin:'msg-error',type:1});
                        }
                    })
                });
            })



		    /*$(".fa-trash-o").click(function(){
		        var thisElement=$(this);
		        layer.alert('确认删除？',function(){
		            $.ajax({
		                type:'get',
		                async:false,
		                url:"{{route('osce.admin.getDelTrain')}}?id="+thisElement.parent().parent().parent().attr('value'),
		                success:function(data){
		                    location.href='{{route('osce.admin.getTrainList')}}?page=1';
		                }
		            })
		        });
		    })*/
		})
	</script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':''}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考前培训</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{ route('osce.admin.getAddTrain')  }}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>标题</th>
                <th>培训时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $key => $data)
                <tr>
                    <td>{{$key+1}}</td>
                    <td><a href="{{route('osce.admin.getTrainDetail',array('id'=>$data->id))}}">{{ $data->name }}</a></td>
                    <td>{{date('Y-m-d H:i',strtotime($data->begin_dt))}} ~ {{date('Y-m-d H:i',strtotime($data->end_dt))}}</td>
                    <td value="{{$data->id}}">
                    <!--<td value="1">-->
                        <a href="{{ route('osce.admin.getEditTrain',array('id'=>$data->id)) }}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
<div style="float: left">
    共{{$list->total()  }}条
</div>
        <div style="float: right">
            {!! $list->render() !!}
        </div>
    </form>

</div>
@stop{{-- 内容主体区域 --}}