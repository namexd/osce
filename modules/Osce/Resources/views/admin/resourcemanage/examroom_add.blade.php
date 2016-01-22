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
                                message: '考场名称不能为空'
                            }
                        }
                    },
                    code: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '编号不能为空'
                            },
                            regexp: {
                                regexp:  /^[\u4E00-\u9FA5a-zA-Z0-9_]*$/,
                                message: '不能输入中文和特殊字符'
                            }
                        }
                    },
                    description: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '功能描述不能为空'
                            }
                        }
                    },
                    address: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
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
            <h5>新增考场</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.room.postCreateRoom')}}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">编号</label>

                            <div class="col-sm-10">
                                <input type="text"  required  ng-model="num" id="code" class="form-control" name="code">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div><div class="form-group">
                            <label class="col-sm-2 control-label">关联摄像机</label>

                            <div class="col-sm-10">
                                <select name="vcr_id" id="" class="form-control">
                                    @foreach($vcr as $key=>$item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div><div class="form-group">
                            <label class="col-sm-2 control-label">功能描述</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="description" id="description" class="form-control" name="description">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">地址</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="address">
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>

                        {{--<input type="hidden" class="description"  name="vcr_id" value="{{@$vcr->id}}"/>--}}
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
                                {{--<button class="btn btn-white" type="submit">取消</button>--}}
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}