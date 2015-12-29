@extends('msc::admin.layouts.admin')

@section('only_css')
    <link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
@stop

@section('content')
    <link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-4 selected-all">
            <button type="button" class="btn btn_pl btn-link" id='examine_del'>批量删除</button>
            <button type="button" class="btn btn_pl btn-link" id='examine_through'>批量通过</button>
            <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
        </div>
        <div class="col-xs-3 col-md-2">

            <div class="input-group">
                <input type="text" placeholder="请输入项目名称" class="input-sm form-control">
            <span class="input-group-btn">
                <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
            </span>
            </div>

        </div>
        <div class="col-xs-9 col-md-6">
            <div class="pull-right">
                <button type="button" class="btn btn_pl btn-white">导出excel</button>
                <button type="button" class="btn btn_pl btn-white">教务处导入</button>
                <button type="button" class="btn btn_pl btn-white">其他导入</button>
                <button type="button" class="btn btn_pl btn-success">上传校验</button>
            </div>
        </div>
    </div>

    <form class="container-fluid ibox-content" id="list_form">
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
                <th>姓名</th>
                <th>学号</th>
                <th>班级</th>
                <th>专业</th>
                <th>手机号</th>
                <th>证件类型</th>
                <th>证件号码</th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">审核状态 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{  route('msc.verify.student',['status'=>1]) }}">已审核</a>
                            </li>
                            <li>
                                <a href="{{  route('msc.verify.student',['status'=>0]) }}">未审核</a>
                            </li>
                            <li>
                                <a href="{{  route('msc.verify.student') }}">所有</a>
                            </li>
                        </ul>
                    </div>
                </th>
                <th>
                    操作
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($list as $v)
                <tr list_id = "{{ $v['id'] }}">
                    <td>
                        <label class="check_label checkbox_input">
                            <div class="check_icon"></div>
                            <input type="checkbox" class="check_id" name="check_id[]" value="{{ $v['id'] }}" />
                        </label>
                    </td>
                    <td>{{ $v['id'] }}</td>
                    <td>{{ $v['name'] or '' }}</td>
                    <td>{{ $v['code']  or ''}}</td>
                    <td class="text-navy">{{$v->className->name or ''}}</td>
                    <td>{{ $v->professionalName->name  or ''}}</td>
                    <td>{{ $v->userInfo->mobile  or '' }}</td>
                    <td>{{ $v->getIdCardType() }}</td>
                    <td>{{ $v->userInfo->idcard or '' }}</td>

                    @if($v['validated'] == 1 )
                        <td class="examineText">已通过</td>
                        @else
                        <td class="examineText">未审核</td>
                    @endif
                    <td>
                        <div class="opera">
                            <span class="pass" style="cursor: pointer" >通过</span>
                            <span class="no_pass del" style="cursor: pointer" >删除</span>
                         </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        还没有待审核的数据！
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="btn-group pull-right">
            <?php echo $list->render(); ?>
        </div>
    </form>
    {{-- postDelMany --}}
    <form action="{{ url('/msc/admin/examine/del-many') }}" method="post" id="submitDel">
        <input type="hidden" name="ids">
    </form>
    <form action="{{ url('/msc/admin/examine/change-users-status') }}" method="post" id="submitSave">
        <input type="hidden" name="ids">
        <input type="hidden" name="id">
        <input type="hidden" name="status">
        <input type="hidden" name="type">
    </form>

    <script>

        $(function(){
            //通过
            $("#list_form").delegate(".pass","click",function(){
                var id = $('.pass').parent().parent().parent().attr('list_id');

                $.confirm({
                    title: '提示：',
                    content: '确定要通过该条记录么?',
                    confirmButton: '确定',
                    cancelButton:'取消',
                    confirm: function(){

                        var DelForm =  $('#submitSave');
                        DelForm.attr('action',"{{ route('verify.postChangeUsersStatus') }}");
                        console.log(DelForm.attr('action'));
                        DelForm.find('input[name="id"]').val(id);
                        DelForm.find('input[name="status"]').val(1);
                        DelForm.find('input[name="type"]').val('student');
                        DelForm.submit();
                    }
                });
            });

            //不通过
            $("#list_form").delegate(".del","click",function(){
                var id = $(this).parents('tr').attr('list_id');
                $.confirm({
                    title: '提示：',
                    content: '确定要删除该条记录么，删除后不能恢复!',
                    confirmButton: '确定',
                    cancelButton:'取消',
                    confirm: function(){
                        var DelForm =  $('#submitDel');
                        DelForm.find('input[name="ids"]').val(id);
                        DelForm.submit();
                    }
                });

            });

            //批量删除
            $('#examine_del').click(function(){
                var check_id = $('.check_id');
                var str = '';
                if(check_id != undefined && check_id.length>0){
                    str = getId();
                    $.confirm({
                        title: '提示：',
                        content: '确定要删除该条记录么，删除后不能恢复?',
                        confirmButton: '确定',
                        cancelButton:'取消',
                        confirm: function(){
                            var DelForm =  $('#submitDel');
                            DelForm.find('input[name="ids"]').val(str);
                            DelForm.submit();
                        }
                    });
                }
            })

            //批量通过
            $('#examine_through').click(function(){
                var check_id = $('.check_id');
                var str = '';
                if(check_id != undefined && check_id.length>0){
                    str = getId();
                    $.confirm({
                        title: '提示：',
                        content: '确定要批量通过么?',
                        confirmButton: '确定',
                        cancelButton:'取消',
                        confirm: function(){
                            var DelForm =  $('#submitSave');
                            DelForm.attr('action',"{{ url('/msc/admin/verify/change-users-status') }}");
                            DelForm.find('input[name="ids"]').val(str);
                            DelForm.find('input[name="status"]').val(1);
                            DelForm.find('input[name="type"]').val('student');
                            DelForm.submit();
                        }
                    });

                }
            })

            //获取被选中的id
            function getId(){
                var check_id = $('.check_id');
                var str = '';
                if(check_id != undefined && check_id.length>0){
                    for(var i=0;i<check_id.length;i++){
                        var check_id_item = $(check_id[i]);
                        if(check_id_item.is(':checked')){
                            if(str){
                                str += ',';
                            }
                            str += check_id_item.val();
                        }
                    }
                }
                return str;
            }

        })
    </script>


</div>
@stop{{-- 内容主体区域 --}}