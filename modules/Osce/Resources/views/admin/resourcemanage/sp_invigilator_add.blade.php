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
                                message: '名称不能为空'
                            }
                        }
                    },
                    mobile: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '手机号码不能为空'
                            },
                            stringLength: {
                                min: 11,
                                max: 11,
                                message: '请输入11位手机号码'
                            },
                            regexp: {
                                regexp: /^1[3|5|8]{1}[0-9]{9}$/,
                                message: '请输入正确的手机号码'
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
            <h5>新增SP老师</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.invigilator.postAddSpInvigilator')}}">

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name">
                                <input type="hidden" required class="form-control" id="is_sp" name="type" value="2">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">手机号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="mobile">
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="select-floor">
                                <label class="col-sm-2 control-label">病例</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="case_id">
                                        @forelse($list as $option)
                                            <option value="{{$option->id}}">{{$option->name}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
{{--								<a class="btn btn-white" href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">取消</a>--}}
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}