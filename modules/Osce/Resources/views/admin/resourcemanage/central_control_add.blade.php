@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style>
    .select2-selection.select2-selection--multiple{
        border-radius: 0;
        border-color:#e5e6e7;
    }
</style>
@stop

@section('only_js')
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
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
                    name: {/*键名username和input name值对应*/
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '名称不能为空'
                            }
                        }
                    },
                    address: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
                            }
                        }
                        
                    },
                    video: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '摄像头不能为空'
                            }
                        }
                    },
                    description: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '描述不能为空'
                            }
                        }
                    }
                }
            });

        $('select').select2({
            placeholder: '',
            minimumResultsForSearch: Infinity,
            ajax:{
                url: '',
                delay:0,
                data: function (elem) {

                    //老师id
                    var ids = $('select').val();
                    //请求参数
                    return {
                        spteacher_id:ids
                    };
                },
                dataType: 'json',
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].name});
                    }

                    //加载入数据
                    return {
                        results: str
                    };
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
            <h5>新增</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">场所名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" name="name" value="">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">地址</label>
                            <div class="col-sm-10">
                                <input id="select_Category" class="form-control m-b" name="address" value="" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">关联摄像机</label>
                            <div class="col-sm-10">
                                <select class="form-control js-example-basic-multiple" name="video" multiple="multiple">
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="num" name="description" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>

                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}