@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
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
            /*{}{
             * 下面是进行插件初始化
             * 你只需传入相应的键值对
             * */
            $('#labForm').bootstrapValidator({
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
                                message: '负责人不能为空'
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
                    address: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
                            }
                        }
                    },
                    type: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '类型不能为空'
                            }
                        }
                    },
                    maxorder: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '最大预约人数不能为空'
                            }
                        }
                    },
                    detail: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '描述不能为空'
                            }
                        }
                    },  begindate: {
                        validators: {
                            notEmpty: {
                                message: '开始时间不能为空'
                            },
                        }
                    },
                    enddate: {
                        validators: {
                            notEmpty: {
                                /*非空提示*/
                                message: '结束时间不能为空'
                            },
                            callback: {
                                message: '结束日期不能小于开始日期',
                                callback: function (value, validator, $field) {
                                    var begin = $('#star').val();
                                    $('#star').keypress();
                                    var b_date = begin.replace(/-/g,"");
                                    var e_date = value.replace(/-/g,"");
                                    return parseInt(e_date) >= parseInt(b_date);
                                }
                            }
                        }
                    },

                }
            });
        })
        var start = {
            elem: "#start",
            format: "YYYY-MM-DD",
            max: "2099-06-16",
            istoday: true,
            istime: true,
            istoday: false,
            choose: function(dates){ //选择好日期的回调
                $("#start").val(dates);
                $('#labForm').bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {/*验证*/
                        begindate: {
                            validators: {
                                notEmpty: {
                                    message: '开始时间不能为空'
                                },
                            }
                        }

                    }
                });
            }
        };
        var end = {
            elem: "#end",
            format: "YYYY-MM-DD",
            max: "2099-06-16",
            istoday: true,
            istime: true,
            istoday: false,
            choose: function(dates){ //选择好日期的回调
                $("#end").val(dates);
                $('#labForm').bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {/*验证*/
                        enddate: {
                            validators: {
                                notEmpty: {
                                    /*非空提示*/
                                    message: '结束时间不能为空'
                                },
                                callback: {
                                    message: '结束日期不能小于开始日期',
                                    callback: function (value, validator, $field) {
                                        var begin = $('#star').val();
                                        $('#star').keypress();
                                        var b_date = begin.replace(/-/g,"");
                                        var e_date = value.replace(/-/g,"");
                                        return parseInt(e_date) >= parseInt(b_date);
                                    }
                                }
                            }
                        },

                    }
                });
            }
        };
        $("#select_Category").change( function(){
            if($(this).val()=="Classroom") {
                $(".select-floor").show();
            }else{
                $(".select-floor").hide();
            }
        })



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
                        <form method="post" class="form-horizontal" id="labForm" action="{{route('msc.admin.lab.postHadOpenLabToAdd')}}">

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{@$openLabDetail->name}}"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="opened" id="cate_id" value="-1" />
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10 select_code">
                                    <select id="select_Category"   class="form-control m-b" name="opened">
                                        <option value="-1">请选择类别</option>
                                        <option value="0" @if($openLabDetail['name'] == 0)selected="selected"@endif>普通实验室</option>
                                        <option value="1" @if($openLabDetail['name'] == 1)selected="selected"@endif>开发实验室(只能预约实验室)</option>
                                        <option value="2" @if($openLabDetail['name'] == 2)selected="selected"@endif>开发实验室(只能预约设备)</option>
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
                                    <input type="text"  id="manager_name" name="manager_name" class="form-control" value="{{@$openLabDetail->manager_name}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_mobile" name="manager_mobile"  class="form-control" value="{{@$openLabDetail->manager_mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id="select_Category"   class="form-control m-b" name="status">
                                        <option value="-1">请选择状态</option>
                                        <option value="0" @if($openLabDetail['status'] == 0)selected="selected"@endif>不允许预约使用</option>
                                        <option value="1" @if($openLabDetail['status'] == 1)selected="selected"@endif>正常</option>
                                        <option value="2" @if($openLabDetail['status'] == 2)selected="selected"@endif>已预约</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="address" name="location" class="form-control" value="{{@$openLabDetail->location}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">门牌号</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="code" name="code" class="form-control" value="{{@$openLabDetail->code}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放开始时间</label>

                                <div class="col-sm-10">
                                    <input class="form-control layer-date laydate-icon" id="start" name="begintime"  value="{{@$openLabDetail->begintime}}">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放结束时间</label>
                                <div class="col-sm-10">
                                    <input  class="form-control layer-date laydate-icon" id="end" name="endtime"  value="{{@$openLabDetail->endtime}}">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">说明(功能描述)</label>

                                <div class="col-sm-10">
                                    <input type="text" name="detail" id="detail" class="form-control"  value="{{@$openLabDetail->detail}}">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最大预约人数</label>
                                <div class="col-sm-10">
                                    <input type="number" name="person_total" id="maxorder" class="form-control"  value="{{@$openLabDetail->person_total}}">
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