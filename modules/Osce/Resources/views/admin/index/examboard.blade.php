@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    #start,#end{width: 160px;}
    /*TODO: fandian，2016-2-26, 只用于本页面*/
    .btn.btn-primary{
        padding: 4px 8px; !important;
    }
    	    .form-horizontal label i {color: red; }
#addlb {
    overflow-x: hidden;
    padding: 50px;
    display: none;
}
    </style>
@stop

@section('only_js')
   <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>

	<script>
		$(function () {
			$('.yichang').click(function () {
				$('#id_card').val('');
				$('#code').val('');
				$('#exam_id').val($(this).attr('_id'));
				addlbindex = layer.open({
					type:1,
					title:'异常操作（身份绑定）',
					closeBtn:1,
					area:['600px','330px'],
					content:$('#addlb'),
					success:function () {
						$('#id_card').focus();
					}
				});
			});
			$('.addlb-close').click(function () {
				layer.close(addlbindex);
			});
			
			$('.addlb-save').click(function () {
	        	if (noempty($('#addlb'))) {
	        		return false;
	        	}	
	        	var shade = layer.load(0, {shade: [0.3,'#000']});
	        	$.ajax({
	        		type:'get',
	        		url:"{{route('osce.api.watch.bound')}}",
	        		data:{
	        			id_card:$.trim($('#id_card').val()),
	        			code:$.trim($('#code').val()),
	        			flag:1,
	        			exam_id:$.trim($('#exam_id').val())
	        		},
	        		success:function (res) {
	        			layer.close(shade);
	        			if (res.code==1) {
	        				uselayer(31,res.message,function () {
								$('#id_card').val('');
								$('#code').val('');
	        				});
	        			} else {
	        				uselayer(1,res.message);
	        			}
	        		},
	        		error:function () {
	        			layer.close(shade);
	        			uselayer(3,'操作失败，请重试');
	        		}
	        	});
			});	
			
		});
		
    function noempty(obj) {
    	var empty = false;
    	$(obj).find('label i').each(function () {
    		var _p = $(this).parent().parent()
    		var _obj = ($(_p).find('.form-control').length!=0?$(_p).find('.form-control'):$(_p).find('.laydate-icon'));
    		if ($.trim($(_obj).val())=='') {
    			empty = true;
    			uselayer(3,($(_obj).attr('emptymsg')?$(_obj).attr('emptymsg'):$(_obj).attr('placeholder')));
    			$(_obj).focus();
    			return false;
    		}
    	});
    	if (empty) {
    		return true;
    	}
    	return false;
    };	
	function uselayer(type,str,fn,title,obj) {
		var data = {title:[(title?title:'信息'),'background-color:#16abff;color:#fff']};
		if (type=='1') {
			var ilayer = layer.alert(str,data,function () {
					layer.close(ilayer);
					fn&&fn();							
			});		
		} else if (type=='2') {
			var ilayer = layer.confirm(str,data,function () {
					layer.close(ilayer);
					fn&&fn();
				}
			);
		} else if (type=='3') {
			
			var ilayer = layer.msg(str,{skin:'msg-error center',icon:1,time:1500},function(){
			  		fn&&fn();
			});  		
		} else if (type=='31') {
			
			var ilayer = layer.msg(str,{skin:'msg-success center',icon:1,time:1500},function(){
			  		fn&&fn();
			});  		
		} else if (type=='4') {
			data = {
				type: 1,
				title: data.title,
				area: $(obj).width(),
				content: $(obj)
			};
			var ilayer = layer.open(data);	
		}
		return ilayer;
	};	
	</script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_assignment','deletes':'{{route('osce.admin.exam.postDelete')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">今天的所有考试</h5>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>考试编号</th>
                <th>考试名称</th>
                <th>时间</th>
                <th>考试人数</th>
                <th>开考</th>
                <th>大屏</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->id}}</td>
                    <td><a href="{{route('osce.admin.exam.getEditExam',['id'=>$item->id])}}">{{$item->name}}</a> </td>
                    <td>{{date('Y-m-d H:i', strtotime($item->begin_dt))}} ~ {{date('Y-m-d H:i', strtotime($item->end_dt))}}</td>
                    <td>{{$item->total}}</td>
                    <td value="{{$item->id}}">
                        @if($item->status ==0)
                            @if(count($item->examPlan) > 0)
                                <a href="{{route('osce.admin.index.getSetExam',['id'=>$item->id])}}">
                                    <input class="btn btn-primary" type="button" value="开始考试"/>
                                </a>
                            @else
                                <a href="javascript:void(0)">
                                    <input class="btn btn-primary" type="button" disabled value="开始考试"/>
                                </a>
                            @endif
                        @elseif($item->status==1)
                            正在考试
                            <input _id="{{$item->id}}" class="btn btn-warning marl_5 yichang" type="button" value="异常操作"/>
                        @else
                            考试已结束   
                        @endif
                    </td>
                    <td>
                        @if($item->status==2)
                            考试已结束
                        @else
                            <a class="btn btn-primary" href="{{route('osce.admin.getWaitDetail',['exam_id'=>$item->id])}}" target="_blank">
                                大屏
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pull-left">
{{--            共{{$data->total()}}条--}}
        </div>
        <div class="btn-group pull-right">
           {{--{!! $data->appends($_GET)->render() !!}--}}
        </div>
    </form>
</div>

<div class="form-horizontal" id="addlb">
	<input type="hidden" name="exam_id" id="exam_id" />
    <div class="form-group">
        <label class="col-sm-3 control-label">身份证号：<i>*</i></label>
		<div class="col-sm-7">
			<input type="text" id="id_card" class="form-control" placeholder="请输入身份证号" />
		</div>
    </div> 
    <div class="form-group">
        <label class="col-sm-3 control-label">卡号：<i>*</i></label>
		<div class="col-sm-7">
			<input type="text" id="code" class="form-control" placeholder="请输入卡号" />
		</div>
    </div> 
    <div class="form-group" style="margin-top: 50px;">
        <div class="col-sm-6 col-sm-offset-4">
            <button class="btn btn-primary addlb-save">绑定</button>
            <a class="btn btn-white addlb-close">取消</a>
        </div>
    </div>
</div>



@stop{{-- 内容主体区域 --}}