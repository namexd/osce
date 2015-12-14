@extends('msc::admin.layouts.admin')
@section('only_css')
<link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
<link href="{{asset('msc/admin/css/operation_node.css')}}" rel="stylesheet">
	<style type="text/css">
		.btn-default:hover{background-color: #1a7bb9;border-color: #1a7bb9;}
		.easy-tree{margin:50px 0 0 30px}
	</style>
@stop
@section('only_js')
<script src="{{asset('msc/admin/js/operation_node.js')}}"></script>
<script type="text/javascript">
	$(function(){
		var open_child = true;
		//展开下一级节点
		$('.list_table').delegate('.show_child','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var pid = tr.attr('pid');
			if(open_child){
				//open_child = false;
				$.getJSON("{{route('msc.admin.resourcesManager.getAjaxResourcesToolsCate')}}",'id='+pid,function(obj){
					var str = '';
					if(obj != undefined && obj.length>0){
						for(var i=0;i<obj.length;i++){
							str += '<tr class="cor_fff father" pid="'+obj[i].id+'">' +
									'<td height="40" align="center">'+obj[i].id+'</td>' +
									'<td align="center"><span class="bnt_show_input show_child"></span><span class="bnt_hidden_input bnt_hidden_child pack"></span>'+obj[i].name+'</td>' +
									'<td align="center">'+obj[i].repeat_max+'</td>' +
									'<td align="center">'+obj[i].manager_name+'</td>' +
									'<td align="center">'+obj[i].location+'</td>' +
									'<td align="center">'+obj[i].loan_days+'</td>' +
									'<td align="center"><a class="sc delete_type" style="margin-left: 40px"  href="#">删除</a></td>' +
									'</tr>';
						}
					}
					str += '<tr class="cor_fff" pid="'+pid+'">' +
							'<td height="40" align="center">&nbsp;</td>' +
							'<td ><a class="tit_ bnt_add_type" href="#"></a></td>' +
							'<td align="center">&nbsp;</td>' +
							'<td align="center"><a class="was" href="#">&nbsp;</a></td>' +
							'<td align="center"></td>' +
							'<td align="center"></td>' +
							'<td align="center"></td>' +
							'</tr>';
					tr.after(str);
					$this.hide();
					$this.next('.bnt_hidden_child').show();
				})

				//open_child = true;
			}

		})

		//收起下一级节点
		$('.list_table').delegate('.bnt_hidden_child','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var id = tr.attr('pid');
			//$('tr[pid='+id+']').hide();
			tr.nextUntil('tr[pid='+id+']').hide();
			$this.hide();
			$this.prevAll('.show_child').show();
		})

	})
</script>
@stop
@section('content')
	<div class="list_table">
		<table width="100%" border="1" bordercolor="#cfd9e8">
			<tr>
				<th width="" height="35" scope="col">#</th>
				<th width="" scope="col">类别名称</th>
				<th width="" scope="col">最大续借（次）</th>
				<th width="" scope="col">负责人信息</th>
				<th width="" scope="col">摆放地址</th>
				<th width="" scope="col">最大借出（天）</th>
				<th width="" scope="col">操作</th>
			</tr>

			@foreach($list as $v)
				<tr class="cor_fff father" pid="{{ $v['id'] }}">
					<td height="40" align="center">{{ $v['id'] }}</td>
					<td align="center"><span class="bnt_show_input show_child"></span><span class="bnt_hidden_input bnt_hidden_child pack"></span>{{ $v['name'] }}</td>
					<td align="center">{{ $v['repeat_max'] }}</td>
					<td align="center">{{ $v['manager_name'] }}</td>
					<td align="center">{{ $v['location'] }}</td>
					<td align="center">{{ $v['loan_days'] }}</td>
					<td align="center"><a class="sc delete_type" style="margin-left: 40px"  href="#">删除</a></td>
				</tr>
			@endforeach
			<tr class="cor_fff" pid="0">
				<td height="40" align="center">&nbsp;</td>
				<td ><a class="tit_ bnt_add_type" href="#"></a></td>
				<td align="center">&nbsp;</td>
				<td align="center"><a class="was" href="#">&nbsp;</a></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"></td>
			</tr>
		</table>
	</div>

@stop{{-- 内容主体区域 --}}