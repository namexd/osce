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
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.case.postNameUnique')}}',//验证地址
                                message: '病例名称已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    return {
                                        id: '{{$_GET['id']}}',
                                        name: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
                            },
                            notEmpty: {/*非空提示*/
                                message: '病例名称不能为空'
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
            <h5>病例编辑</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例名称</label>
                            <div class="col-sm-10">
                                <input type="text"  class="form-control" id="name" value="{{$data->name}}" name="name">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例描述</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="description" value="{{$data->description}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="{{route("osce.admin.case.getCaseList")}}">取消</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}