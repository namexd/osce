@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script>
        $(function(){
            $('#sourceForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '病例名称不能为空'
                            }
                        }
                    },
                    code: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '编号不能为空'
                            }
                        }
                    }
                }
            });
        })
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增PAD</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postAddMachine')}}">

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name">
                                    <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="2" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">编号</label>

                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="code" name="code">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备状态</label>
                                <div class="col-sm-10">
                                    <select id=""   class="form-control m-b" name="status">
                                        <option value="0">未使用</option>
                                        <option value="1">使用中</option>
                                        <option value="2">维修</option>
                                        <option value="3">报废</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
{{--                                    <a class="btn btn-white" href="{{route('osce.admin.machine.getMachineList', ['cate_id'=>2])}}">取消</a>--}}

                                </div>
                            </div>


                        </form>

                    </div>

                </div>
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}