@extends('msc::admin.layouts.admin')
@section('only_css')
<link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
<link href="{{asset('msc/admin/css/operation_node.css')}}" rel="stylesheet">
	<style type="text/css">
		.btn-default:hover{background-color: #1a7bb9;border-color: #1a7bb9;}
		.easy-tree{margin:50px 0 0 30px}
		.edit_text{display: none}
	</style>
@stop
@section('only_js')
<script type="text/javascript">
	$(function(){
		var margin_left = 20;
		var open_child = true;
		//展开下一级节点
		$('.list_table').delegate('.show_child','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var left = parseInt($this.css('margin-left'));
			var pid = tr.attr('pid');
			if(open_child){
				//open_child = false;
				$.getJSON("{{route('msc.admin.resourcesManager.getAjaxResourcesToolsCate')}}",'id='+pid,function(obj){
				open_child = false;
					var str = '';
					if(obj != undefined && obj.length>0){
						for(var i=0;i<obj.length;i++){
							str += addData(obj[i],(margin_left+left));
						}
					}
					//添加加号
					str += addPlus(pid,(margin_left+left+5));
					tr.after(str);
					$this.hide();
					$this.next('.bnt_hidden_child').show();
					open_child = true;
				})

			}

		})

		//收起下一级节点
		$('.list_table').delegate('.bnt_hidden_child','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var id = tr.attr('pid');
			$('tr[pid='+id+']').hide();
			tr.show();
			tr.nextUntil('tr[pid='+id+']').hide();
			$this.hide();
			$this.prevAll('.show_child').show();
		})

		//添加一条类别数据
		$('.list_table').delegate('.bnt_add_type','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var pid = tr.attr('pid');
			var left = parseInt($this.css('margin-left'));
			if(open_child) {
				open_child = false;
				$.post("{{ url('/msc/admin/resources-manager/add-cate-by-pid') }}", 'pid=' + pid, function (obj) {
					if (obj != false) {
						var data = {
							id: obj.data,
							name: '分类名称',
							repeat_max: '最大续借次数',
							manager_name: '责任人名称',
							manager_mobile:'联系方式',
							location: '摆放地址',
							loan_days: '最大借出天数'
						};
						var str = '';
						if (pid == 0) {
							str += addData(data, left);
						} else {
							str += addData(data, (left - 5));
						}
						tr.before(str);
					} else {
						alert('添加分类失败');
					}
					open_child = true;
				})
			}
		})

		//删除分类数据
		$('.list_table').delegate('.delete_type','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var id = tr.attr('pid');
			if(open_child) {
				open_child = false;
				$.post("{{ url('/msc/admin/resources-manager/del-resources-tools-cate') }}", 'id=' + id, function (obj) {
					if (obj == true) {
						tr.remove();
					} else {
						alert('删除分类失败');
					}
					open_child = true;
				})
			}
		})

		//点击显示编辑分类
		$('.list_table').delegate('.edit_type','click',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var id = tr.attr('pid');
			$this.next('.edit_text').val($this.val());
			$this.next('.edit_text').show();
			$this.hide();
		})
		//编辑信息并且保存
		$('.list_table').delegate('.edit_text','blur',function(){
			var $this = $(this);
			var tr = $this.parents('tr');
			var id = tr.attr('pid');
			$this.attr('name');
			var key = $this.attr('name');
			var val = $this.val();
			if(open_child && val.length>0) {
				open_child = false;
				$.post("{{ url('/msc/admin/resources-manager/edit-resources-tools-cate') }}", 'id=' + id+'&key='+key+'&val='+val, function (obj) {
					if(obj){
						$this.prev('.edit_type').val(val);
						$this.prev('.edit_type').show();
						$this.hide()
					}else{
						alert('编辑失败');
					}
					open_child = true;
				})
			}

		})

		//添加下面的加号那一段的Tr
		function addPlus(pid,magin_left){
			return '<tr class="cor_fff" pid="'+pid+'">' +
			'<td height="40" align="center">&nbsp;</td>' +
			'<td ><a class="tit_ bnt_add_type" style="margin-left: '+magin_left+'px" href="javascript:void(0)"></a></td>' +
			'<td align="center">&nbsp;</td>' +
			'<td align="center"><a class="was" href="#">&nbsp;</a></td>' +
			'<td align="center"></td>' +
			'<td align="center"></td>' +
			'<td align="center"></td>' +
			'<td align="center"></td>' +
			'</tr>';
		}

		//添加数据的表格
		function addData(data,magin_left){
			var str = '';
			str += '<tr class="cor_fff father" pid="'+data.id+'">' +
					'<td height="40" align="center">'+data.id+'</td>' +
					'<td align="center">' +
					'<span class="bnt_show_input show_child" style="margin-left: '+magin_left+'px"></span>' +
					'<span class="bnt_hidden_input bnt_hidden_child pack" style="margin-left: '+magin_left+'px" ></span><input type="button" class="edit_type" value="'+data.name+'"/><input type="text" name="name"   class="edit_text"/></td>' +
					'<td align="center"><input type="button" class="edit_type" value="'+data.repeat_max+'"/><input type="text" name="repeat_max" class="edit_text"/></td>' +
					'<td align="center"><input type="button" class="edit_type" value="'+data.manager_name+'"/><input type="text" name="manager_name" class="edit_text"/></td>'+
					'<td align="center"><input type="button" class="edit_type" value="'+data.manager_mobile+'"/><input type="text" name="manager_mobile" class="edit_text"/></td>'+
					'<td align="center"><input type="button" class="edit_type" value="'+data.location+'"/><input type="text" name="location" class="edit_text"/></td>'+
					'<td align="center"><input type="button" class="edit_type" value="'+data.loan_days+'"/><input type="text" name="loan_days" class="edit_text"/></td>'+
					'<td align="center"><a class="sc delete_type" style="margin-left: 40px"  href="#">删除</a></td>' +
					'</tr>';
			return	str;
		}


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
				<th width="" scope="col">负责人姓名</th>
				<th width="" scope="col">负责人联系方式</th>
				<th width="" scope="col">摆放地址</th>
				<th width="" scope="col">最大借出（天）</th>
				<th width="" scope="col">操作</th>
			</tr>

			@foreach($list as $v)
				<tr class="cor_fff father" pid="{{ $v['id'] }}">
					<td height="40" align="center">{{ $v['id'] }}</td>
					<td align="center"><span class="bnt_show_input show_child"></span><span class="bnt_hidden_input bnt_hidden_child pack"></span><input
								type="button" class="edit_type" value="{{ $v['name'] }}"><input type="text" name="name" class="edit_text"/></td>
					<td align="center"><input type="button" class="edit_type" value="{{ $v['repeat_max'] }}"/> <input type="text" name="repeat_max" class="edit_text"/></td>
					<td align="center"><input type="button" class="edit_type" value="{{ $v['manager_name'] }}"/><input type="text" name="manager_name" class="edit_text"/></td>
					<td align="center"><input type="button" class="edit_type" value="{{ $v['manager_mobile'] }}"/><input type="text" name="manager_mobile" class="edit_text"/></td>
					<td align="center"><input type="button" class="edit_type" value="{{ $v['location'] }}"/><input type="text" name="location" class="edit_text"/></td>
					<td align="center"><input type="button" class="edit_type" value="{{ $v['loan_days'] }}"/><input type="text" name="loan_days" class="edit_text"/></td>
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
				<td align="center"></td>
			</tr>
		</table>
	</div>

@stop{{-- 内容主体区域 --}}