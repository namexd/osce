@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style>
        .layer-date{max-width: 100%!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
        $(function(){
            //时间选择
            laydate(start);
            laydate(end);
            $('.cancel').click(function (){
                //history.go(-1);
                var url = '{{ route("msc.admin.resourcesManager.getResourcesList") }}';
                window.location.href = url;
            });
        })
        var start = {
            elem: "#start",
            format: "YYYY-MM-DD",
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
        };
        var end = {
            elem: "#end",
            format: "YYYY-MM-DD",
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
        };
        $("#select_Category").change( function(){
            if($(this).val()=="Classroom") {
                $(".select-floor").show();
            }else{
                $(".select-floor").hide();
            }
        })
        /*{}{
         * 下面是进行插件初始化
         * 你只需传入相应的键值对
         * */
        $('#sourceForm').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '用户名不能为空'
                        }
                    }
                },
                manager_name: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '用户名不能为空'
                        },
                        stringLength: {
                            min:2,
                            message: '用户名长度必须大于2'
                        }
                    }
                },
                manager_mobile: {
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
                },
                location: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '地址不能为空'
                        }
                    }
                }
            }
        });


  </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增实验室</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-md-12">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('msc.admin.resourcesManager.getAddResources')}}">

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="cate_id" id="cate_id" value="-1" />
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10 select_code">
                                    <select id="select_Category"   class="form-control m-b" name="account">
                                        <option value="-1">请选择类别</option>
                                        {{--@foreach ($resourcesCateList as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="manager_name" name="manager_name" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_mobile" name="manager_mobile"  class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>

                                <div class="col-sm-10">
                                    <input type="text"  id="detail" name="type" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text" id="location" name="location" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放开始时间</label>

                                <div class="col-sm-10">
                                    <input class="form-control layer-date laydate-icon" id="start" name="begintime">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放结束时间</label>
                                <div class="col-sm-10">
                                    <input  class="form-control layer-date laydate-icon" id="end" name="endtime">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">说明(功能描述)</label>

                                <div class="col-sm-10">
                                    <input type="text" name="detail" id="detail" class="form-control">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最大预约人数</label>

                                <div class="col-sm-10">
                                    <input type="text" name="maxorder" id="maxorder" class="form-control">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div id="code_list">

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-white cancel" type="button">取消</button>
                                    <button class="btn btn-primary" type="submit">保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                                </div>
                            </div>

                    </form>
                </div>
            </div>

        </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}