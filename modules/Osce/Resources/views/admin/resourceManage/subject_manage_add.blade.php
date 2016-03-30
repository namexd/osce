@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
<style>
    table tr td .form-group {
        margin-bottom: 0;
    }
    td input{margin: 5px 0;}
    .ibox-content{padding-top: 20px;}
    .btn-outline:hover{color: #fff!important;}
    .form-group .ibox-title{border-top: 0;}
    .form-group .ibox-content{
        border-top: 0;
        padding-left: 0;
    }
    .form-horizontal tbody .control-label {
        padding-top: 7px;
        margin-bottom: 0;
        text-align: center;
    }
    .check_other {display: inline-block;vertical-align: middle;}
    .check_top {top: 8px;margin-right: 10px;}
    /*select2样式*/
    .select2-container--default .select2-selection--multiple{border: 1px solid #e5e6e7;border-radius:2px;}
    /*图片上传*/
    #file {position: relative;overflow: hidden;}
    #file input{position: absolute;right: 0;top: 0;font-size: 100px;}
    .file-msg{color: #42b2b1;}
    .upload_list{padding-top:10px;line-height:1em;color:#4f9fcf;}
</style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/subjectManage/subject_manage.js')}}"></script>
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script src="{{asset('osce/admin/js/all_checkbox.js')}}"> </script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
        //试题图片上传
        $(function(){
            $("#file").change(function(){
                var files=document.getElementById("picFile").files;
               // var point = path.lastIndexOf(".");
                //var type = path.substr(point);//图片类型
                var kb=Math.floor(files[0].size/1024);
                if(kb>2048){
                    layer.alert('图片大小不得超过2M!');
                    $("#picFile").val('');
                    return false;
                }
                if($(".picBox p").length > 4){
                    layer.alert('最多只能上传5张图片!');
                    return false;
                }
                $.ajaxFileUpload
                ({
                    url:"{{ route('osce.admin.ExamQuestionController.postQuestionUpload') }}",
                    secureuri:false,//
                    fileElementId:'picFile',//必须要是 input file标签 ID
                    dataType: 'json',
                    success: function (data)
                    {
                        console.log(data);
                        if(data.code){
                            var path=data.data.path;//图片存放路径
                            var picName = data.data.name;//图片名称
                            $(".picBox").append('<p><input type="hidden" name="image[]" value="'+path+'"/>"'+picName+'"<i class="fa fa-2x fa-remove clo6"></i></p>');
                        }else{
                            layer.msg('图片上传失败');
                        }
                    }
                });
            })
        })

    </script>















@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_manage_add'}">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试题</h5>
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{ route('osce.admin.ExamQuestionController.postExamQuestionAdd') }}" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">题目类型</label>
                                <div class="col-sm-10">
                                    <select name="examQuestionTypeId" id="subjectType" class="form-control" style="width: 250px;">
                                        @if(!empty(@$examQuestionTypeList))
                                            @foreach(@$examQuestionTypeList as $val)
                                                <option value="{{@$val['id']}}">{{@$val['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565;">*</span>题目</label>
                                <div class="col-sm-10">
                                    <textarea name="name" id="subjectName" cols="10" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">题目图片</label>
                                <div class="col-sm-10">
                                    <a href="javascript:void(0)" class="btn btn-outline btn-default" id="file" title="请选择图片">
                                        选择图片
                                        <input type="file" id="picFile" name="file">
                                    </a>
                                    <span class="file-msg">(文件大小不得超过2M!)</span>
                                    <div class="picBox upload_list">

                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed chooseLine"></div>
                            <div class="form-group choose">
                                <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565;">*</span>选项</label>
                                <div class="col-sm-10">
                                    <div class="ibox float-e-margins">
                                        <div class="ibox-title">
                                            <div class="ibox-tools">
                                                <span class="btn btn-primary" id="addChose">新增选项</span>
                                            </div>
                                        </div>
                                        <div class="ibox-content">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>选项</td>
                                                        <td>内容</td>
                                                        <td>操作</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>A</td>
                                                        <input type="hidden" name="examQuestionItemName[]" value="A">
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <input type="text" class="form-control" name="content[]">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>B</td>
                                                        <input type="hidden" name="examQuestionItemName[]" value="B">
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <input type="text" class="form-control" name="content[]">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>C</td>
                                                        <input type="hidden" name="examQuestionItemName[]" value="C">
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <input type="text" class="form-control" name="content[]">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>D</td>
                                                        <input type="hidden" name="examQuestionItemName[]" value="D">
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <input type="text" class="form-control" name="content[]">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:void(0)" class="delete">
                                                                <span class="read state2 detail">
                                                                    <i class="fa fa-trash-o fa-2x"></i>
                                                                </span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565">*</span>正确答案</label>
                                <div class="col-sm-10" id="checkbox_div">
                                    <label class="check_label checkbox_input check_top">
                                        <div class="check_icon check_other"></div>
                                        <input type="checkbox" name="answer[]"  value="A">
                                        <span class="check_name">A</span>
                                    </label>
                                    <label class="check_label checkbox_input check_top">
                                        <div class="check_icon check_other"></div>
                                        <input type="checkbox" name="answer[]"  value="B">
                                        <span class="check_name">B</span>
                                    </label>
                                    <label class="check_label checkbox_input check_top">
                                        <div class="check_icon check_other"></div>
                                        <input type="checkbox" name="answer[]"  value="C">
                                        <span class="check_name">C</span>
                                    </label>
                                    <label class="check_label checkbox_input check_top">
                                        <div class="check_icon check_other"></div>
                                        <input type="checkbox" name="answer[]" value="D">
                                        <span class="check_name">D</span>
                                    </label>
                                </div>
                                <div class="col-sm-10" id="radiobox_div" style="display: none;">
                                    <label class="radio_label" style="top: 8px;">
                                        <div class="radio_icon" style="float: left"></div>
                                        <input type="radio" name="judge" value="1">
                                        <span class="radio_name" style="float: left">正确</span>
                                    </label>
                                    <label class="radio_label" style="top: 8px;">
                                        <div class="radio_icon" style="float: left"></div>
                                        <input type="radio" name="judge" value="0">
                                        <span class="radio_name" style="float: left">错误</span>
                                    </label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">解析</label>
                                <div class="col-sm-10">
                                    <textarea name="parsing" id="subjectDes" cols="10" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565">*</span>考核范围</label>
                                <div class="col-sm-10">
                                    @if(!empty($examQuestionLabelTypeList))
                                        @foreach($examQuestionLabelTypeList as $k => $v)
                                            <div style="margin-bottom: 10px" class="clear">
                                                <label class="col-sm-2 control-label">{{ @$v['name'] }}</label>
                                                <div class="col-sm-10">
                                                    <select class="form-control tag" name="tag[]" multiple="multiple">
                                                        @if(!empty($v['examQuestionLabelList']))
                                                            @foreach($v['examQuestionLabelList'] as $key => $val)
                                                                <option value="{{ $val['id'] }}">{{@$val['name']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-sm btn-primary" id="sure" type="submit" disabled>确定</button>
                                    <a href="{{ route('osce.admin.ExamQuestionController.showExamQuestionList') }}" class="btn btn-white btn-sm" id="cancel">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}