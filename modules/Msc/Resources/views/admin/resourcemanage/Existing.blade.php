@extends('msc::admin.layouts.admin')
@section('only_css')
    <link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/sweetalert/sweetalert.css')}}">
    <style rel="stylesheet" >
        /*layer*/
        .modal-header{ background-color: #f6f8fa; color: #95b8fd;}
        .modal-body textarea{margin-top: 10px; height: 200px;resize:none;}
        .sname{width:396px;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;}
    </style>
@stop
@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
    <script src="{{asset('msc/admin/js/qrcode.js')}}"></script>
    <script src="{{asset('msc/admin/js/jquery.jqprint-0.3.js')}}"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>
    <script>

        $(document).ready(function () {
            // 报废单个资源
            $('.state2').click(function (){
            	var id = $(this).data('id');
                var name = $(this).data('name');
                $('.sname').text(name);
                $('[name=id]').val(id);
                $.ajax('/msc/admin/resources-manager/rejected-resources',{
                	type: 'get',
		            data: {id:id},
		            success:function(data) {
		            	//console.log(data.resourcesItems);
		            	$('#code').css('display', 'none');
		                $('#Form').css('display', 'block');
		                $(".code_list").children("option").remove();
		                var str='<option value="0">全部报废</option>';
		                for (var i=0;i<data.resourcesItems.length;i++) {
							str+= '<option value="'+data.resourcesItems[i]['code']+'">'+data.resourcesItems[i]['code']+'</option>';
		                }
		                $(".code_list").append(str);
		            },
		            error:function() {
		              	$.alert({
		                  	title: '提示：',
		                  	content: '通讯失败!',
		                  	confirmButton: '确定',
		                  	confirm: function(){
		                  	}
		              	});
		            },
		            dataType: "json"
                });
            });
            
            $(".liyou").change(function(){
            	if($(this).val()==0){
            		$(this).attr("name","");
            		$("#comment").removeAttr("disabled");
            		$("#comment").attr("name","reject_detail");
            		console.log("123");
            	}
            	else{
            		$("#comment").attr("disabled","disabled");
            		$("#comment").attr("name","");
            		$(this).attr("name","reject_detail");
            	}
            })

            // 批量报废资源
            $('.reject-dozen').click(function (){
                var query = confirm('确定批量报废选中资源？');
                if(query)
                {
                    var ids = [];
                    var trs = $("#table-striped tbody tr");
                    for(var i=0;i<trs.length;i++)
                    {
                        if('check_icon check'==$(trs[i]).find('.checkbox_input div').attr('class'))
                        {
                            ids.push($(trs[i]).find('.checkbox_input input').val());
                        }
                    }
                    var url = '{{ url("/msc/admin/resources-manager/rejected-resources-all") }}';
                    $.post(url, {ids: ids}, function (e){
                        if(1 == e.code)
                        {
                            swal({title: "批量报废成功！" });
                        } else{
                            swal({title: "批量报废失败！" })
                        }
                    });
                }
            });

            // 打印二维码
            /*
            var qrcode = new QRCode(document.getElementById('code1'), {
                width : 96,//设置宽高
                height : 96
            });
            */
            $('.state1').click(function (){
                $('#Form').css('display', 'none');
                $('#code').css('display', 'block');

                var codesInputs = $(this).parent().find('input');
                var codeVal = '';
                var codeText = '';
                var qrcode = '';
                for (var i=0;i<codesInputs.length;i++)
                {
                    codeVal = $(codesInputs[i]).val();
                    codeText = '{{ url('/msc/wechat/resource/resource-view') }}?id='+$(this).data('resource-id')+'&code='+codeVal;
                    $("编码："+codeVal+"<div id='"+codeVal+"'></div>").appendTo("#qrcode-area");
                    qrcode = new QRCode(document.getElementById(codeVal), {
                        width : 96,//设置宽高
                        height : 96
                    });
                    qrcode.makeCode(codeText); // 生成二维码
                    //alert($('#'+codeVal+' img').attr('src'));

                    $('#'+codeVal+' img').jqprint(); // 开始打印
                }
            });
        });
    </script>
@stop
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row table-head-style1 ">
            <div class="col-xs-2 col-md-1 head-opera">
                <button type="button" class="btn btn_pl btn-link reject-dozen">批量报废</button>
            </div>
            <div class="col-xs-6 col-md-2">
                <form method="get">
                    <div class="input-group">
                        <input type="text" placeholder="请输入资源名称" class="input-sm form-control" name="keyword" value="{{ Input::get('keyword') }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                    </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th width="100">
                        <label class="check_label all_checked">
                            <div class="check_icon"></div>
                            <input  type="checkbox"  value="">
                        </label>
                    </th>
                    <th>#</th>
                    <th>名称</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">类别 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">开放实验室</a>
                                </li>
                                <li>
                                    <a href="#">教师</a>
                                </li>
                                <li>
                                    <a href="#">开放设备</a>
                                </li>
                                <li>
                                    <a href="#">模型设备</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>负责人</th>
                    <th>
                        负责人电话

                    </th>
                    <th>地址</th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($list as $k => $v)
                    <tr>
                        <td>
                            <label class="check_label checkbox_input">
                                <div class="check_icon"></div>
                                <input type="checkbox" class="check_id" name="check_id[]" value="{{ $v['id'] }}" />
                            </label>
                        </td>
                        <td>{{ $v['id'] }}</td>
                        <td>{{ $v['name'] }}</td>
                        <td>{{ $v['categoryName'] }}</td>
                        <td>{{ $v['manager_name'] }}</td>
                        <td>{{ $v['manager_mobile'] }}</td>
                        <td>{{ $v['locationName'] }}</td>
                        <td>
                            <div class="opera">
                                <a href="{{ url('msc/admin/resources-manager/resources') }}?id={{ $v['id'] }}"><span class="read  state1">查看</span></a>
                                <a href="{{ url('/msc/admin/resources-manager/edit-resources') }}?id={{ $v['id'] }}"><span class="edit state1">编辑</span></a>
                                <span class="Scrap state2" data-toggle="modal" data-target="#myModal" data-id="{{ $v['id'] }}" data-name="{{ $v['name'] }}">报废</span>
                                <span class="Print state1" data-toggle="modal" data-target="#myModal" data-resource-id="{{ $v['resourcesId'] }}">二维码打印</span>
                                {{-- 编号列表 --}}
                                @foreach ($v['codes'] as $code)
                                    <input type="hidden" value="{{ $code }}"/>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pull-left">
                	已选择<span class="sum">0</span>条
            </div>
            <div class="btn-group pull-right">
                <?php echo $pagination->render();?>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}



@section('layer_content')
    <form class="form-horizontal" id="Form" novalidate="novalidate" action="{{ url('msc/admin/resources-manager/rejected-resources') }}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">报废</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="hidden" name="id" value="{$resource['id']}"/>
                <label class="col-sm-3 control-label">设备名称：</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><a class="ablock clo3 nou sname" href="javascript:;"></a></p>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-3 control-label">报废编号：</label>
                <div class="col-sm-9">
                    <select class="form-control code_list" name="code">
                        
                    </select>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-3 control-label">报废理由：</label>
                <div class="col-sm-9">
                    <select class="form-control liyou" name="reject_detail">
                        <option value="1">已损坏</option>
                        <option value="2">版本太低</option>
                        <option value="3">有故障</option>
                        <option value="0">自定义理由</option>
                    </select>
                    <textarea id="comment" disabled="disabled" class="form-control" aria-required="true"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" >提交并报废</button>
        </div>
    </form>


<!-- 二维码在此显示, 显示时将上一个form设置为 display-->
    <div id="code">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="qrcode-area">
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" >打印</button>
        </div>
    </div>
    <!-- 二维码在此显示-->
@stop{{-- 内容主体区域 --}}