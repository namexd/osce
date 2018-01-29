@extends('osce::theory.base')

@section('title')
	IP地址管理
@stop
@section('head_css')
   <style>
   		#addlb { display: none;}
   		.layui-layer-page .layui-layer-content {overflow: hidden;}
   		.laydate-icon { padding-left: 5px;}
   </style>

@stop

@section('head_js')
   <script>

		var addlbindex;
		
		$(function () {
		    $('#ip_start,#ip_end').keyup(function () {
		        this.value = this.value.replace(/[^\d.]/g, '');
		    });
    		$('.addip').click(function () {
    			$('#ip_start').val('');
    			$('#ip_end').val('');
				addlbindex = layer.open({
					type:1,
					title:'新增IP地址段',
					closeBtn:1,
					area:['600px','300px'],
					content:$('#addlb')
				});    			
    		});
    		
    		$('.addlb-close').click(function () {
    			layer.close(addlbindex);
    		});
    		
    		$('.addlb-save').click(function () {
    			var _start = $.trim($('#ip_start').val()).replace(/[^\d.]/g, '');
    			var _end = $.trim($('#ip_end').val()).replace(/[^\d.]/g, '');
    			if (_start=='') {
    				uselayer2(3,'IP开始段不能为空！');
    				$('#ip_start').focus();
    				return false;
    			}
    			if (_end=='') {
    				uselayer2(3,'IP结束段不能为空！');
    				$('#ip_end').focus();
    				return false;
    			}
    			if (!ipgeshi(_start)) {
    				uselayer2(3,'IP开始段输入错误！');
    				$('#ip_start').focus();
    				return false;
    			}
    			if (!ipgeshi(_end)) {
    				uselayer2(3,'IP结束段输入错误！');
    				$('#ip_end').focus();
    				return false;
    			}
    			if (!ipxianzhi(_start,_end)) {
    				uselayer2(3,'请确认IP段在同一网段，且结束段大于开始段！');
    				$('#ip_end').focus();
    				return false;
    			}
    			$('#ip_start').val(_start);
    			$('#ip_end').val(_end);
    			$('#addlb').submit();
    		});
		});
		
		function ipgeshi(str) {
			var arr = str.split('.');
			if (arr.length!=4) {
				return false;
			}
			for (var i = 0 ; i < arr.length; i++) {
				if (arr[i]=='') {
					return false;
				}
			}
			return true;
		};
		
		function ipxianzhi(start,end) {
			var aS = start.split('.');
			var aE = end.split('.');
			if (aS[0]!=aE[0]||aS[1]!=aE[1]||aS[2]!=aE[2]||aS[3]>aE[3]) {
				return false;
			}
			return true;
		};
		
		function deletelist(id) {
			uselayer(2,'确定要删除该IP段吗？',function () {
				$('.delete-form input').val(id);
				$('.delete-form').submit();
			});
		};
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">IP列表</h5>
	        </div>
	        <div class="col-xs-6" style="float: right;">
	            <a  href="javascript:;" class="btn btn-primary addip" style="float: right;">&nbsp;新增IP段&nbsp;</a>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>IP开始段</th>
                        <th>IP结束段</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($list as $k=>$val)
	                    <tr>
	                        <td >{{$k+1}}</td>
	                        <td >{{$val->ip_start}}</td>
	                        <td >{{$val->ip_end}}</td>
	                        <td >{{$val->created_at}}</td>
	                        <td>
	                            <a class="state1 modal-control" onclick="deletelist({{$val->id}})" href="javascript:;">
	                                <span class="read  state2 "><i class="fa fa-search fa-2x"></i>删除</span>
	                            </a>
		                    </td>
	                    </tr>
               		@endforeach
                </tbody>
            </table>
            <form method="get" class="form-horizontal delete-form" action="{{route('osce.theory.delLimit')}}">
            	<input type="hidden" name="id" value="" />
            </form>
		</div>

	</div>
	
	
    
<form class="form-horizontal" id="addlb" method="post" action="{{route('osce.theory.addLimit')}}">
    <div class="form-group" style="margin-top: 40px;">
        <label class="col-sm-3 control-label">IP开始段：</label>
		<div class="col-sm-7">
			<input type="text" name="ip_start" id="ip_start" class="form-control" />	
		</div>
    </div> 
    <div class="form-group" style="margin-top: 20px;">
        <label class="col-sm-3 control-label">IP结束段：</label>
		<div class="col-sm-7">
			<input type="text" name="ip_end" id="ip_end" class="form-control" />	
		</div>
    </div> 
    <div class="form-group" style="margin-top: 50px;">
        <div class="col-sm-6 col-sm-offset-4">
            <button class="btn btn-primary addlb-save">保存</button>
            <a class="btn btn-white addlb-close">取消</a>
        </div>
    </div>
</form>
	
	
@stop{{-- 内容主体区域 --}}