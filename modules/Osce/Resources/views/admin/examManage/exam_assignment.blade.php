@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    #start,#end{width: 160px;}
    .coloru79 {color: #66CC00;}
    .coloru80 {color: #FF0000;}
    .description-this{
        display: inline-block;
        width: 293px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .btn.btn-primary{
        padding: 4px 9px; !important;
		margin:0;
    }
	table tbody tr td:last-child{white-space: nowrap;}
	table tbody tr td:last-child a:link:hover{text-decoration: none;}
    </style>
@stop

@section('only_js')
   <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script> 
   <script>
		/*function stop_exam(id) {
            //删除用户
            var thisElement=$(this);
            var _layer;
            _layer = layer.confirm('是否确认结束考试？', {
                title:"结束考试",
                btn: ['确定','取消'] //按钮
            }, function(){
				window.location.href = "{{route('osce.admin.exam.stopexam')}}?id="+id;
                
            });*/
       function stop_exam(id){
           layer.open({
               type: 2,
               title: '结束考试',
               shadeClose: true,
               shade: 0.8,
               area: ['90%', '90%'],

               content: 'stopexam?id='+id,
           });
       }
   </script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_assignment','deletes':'{{route('osce.admin.exam.postDelete')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考试安排</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.exam.getAddExam')}}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>考试编号</th>
                <th>考试名称</th>
                <th>考试时间</th>
                <th>考试组成</th>
                <th>考试人数</th>
                <th>排考状态</th>
                <th style="width:12%;min-width:190px;">操作</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->id}}</td>
                    <td><span class="description-this">{{$item->name}}</span></td>
                    <td>{{date('Y-m-d H:i',strtotime($item->begin_dt))}} ~ {{date('Y-m-d H:i',strtotime($item->end_dt))}}</td>
                    <td>{{$item->constitute}}</td>
                    <td>{{$item->total}}</td>
                    <td><span class="coloru{{($item->arranged)?'79':'80'}}">{{($item->arranged)?'是':'否'}}</span></td>
                    <td value="{{$item->id}}">
                        <a href="{{route('osce.admin.exam.getEditExam',['id'=>$item->id])}}"><span class="read  state1 detail"><i class="fa fa-cog fa-2x"></i></span></a>

                        @if($item->status==0)
                            <a href="javascript:void(0)"><span class="read state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        @else
                            <a href="javascript:void(0)" style="text-decoration:none;cursor: default;"><span>&nbsp;&nbsp;&nbsp;</span></a>
                        @endif
                        @if($item->status==2 && $item->real_push==0)
                            <a href="{{route('osce.admin.index.getReleaseScore', ['id'=>$item->id])}}">
                                <button class="btn btn-primary" {{($item->status==2 && $item->real_push==0)?'':'disabled'}} type="button">发布成绩</button>
                            </a>
                        @else
                            <a href="javascript:void (0);">
                                <button class="btn btn-primary" {{($item->status==2 && $item->real_push==0)?'':'disabled'}} type="button">发布成绩</button>
                            </a>
                        @endif
							
							
                        @if($item->status==0 || $item->status==2)
							<a href="javascript:stop_exam('{{$item->id}}');">
                                <button class="btn btn-primary" disabled type="button">结束考试</button>
                            </a>
                        @else
                            <a href="javascript:stop_exam('{{$item->id}}');"><button class="btn btn-primary" type="button">结束考试</button></a>
                        @endif
</td>
</tr>
@endforeach
</tbody>
</table>

<div class="pull-left">
共{{$data->total()}}条
</div>
<div class="btn-group pull-right">
{!! $data->appends($_GET)->render() !!}
</div>
</form>
</div>
@stop{{-- 内容主体区域 --}}