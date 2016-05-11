@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style>
    table tr td .form-group {
        margin-bottom: 0;
    }
    td input{margin: 5px 0;}
    #file0{
        height: 34px;
        width: 70px;
        opacity: 0;
        position: relative;
        top: -20px;
        left: 0;
    }
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
    .display-none{display: none;}
    .select2-container--default{width:100% !important;}
         .select2-container--default .select2-selection--multiple{border:1px solid #e5e6e7;}
         .select2-container--default.select2-container--focus .select2-selection--multiple {border:1px solid  #1ab394 !important;outline: 0;}
         .select2-container--default .select2-selection--single {background-color: #fff;border: 1px solid #e5e6e7;border-radius: 1px;}
             .select2-container--default  .select2-dropdown {border: 1px solid #e5e6e7;}
             .select2-container--default .select2-search--dropdown .select2-search__field {border: 1px solid #e5e6e7;}
             .select2-container--open .select2-selection--single {background-color: #fff;border: 1px solid #1ab394 !important;border-radius: 4px;}
             .select2-container--open .select2-dropdown {border: 1px solid #1ab394 !important;}
             .select2-container--open .select2-search--dropdown .select2-search__field {border: 1px solid #1ab394 !important;}
             .select2-container .select2-selection--single { height: 34px;}
             .select2-container--default .select2-selection--single .select2-selection__arrow b {margin-left: -4px;margin-top: 1px;}
</style>
@stop

@section('only_js')
<script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>
<script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
<script>
    $(function(){
        /**
         * 编辑和新增共用了一段代码，这里必须将验证单独拿出
         * @author mao
         * @version 1.0
         * @date    2016-02-19
         */
        $('#sourceForm').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                title: {/*键名username和input name值对应*/
                    validators: {
                        threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                        remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                            url: "{{route('osce.admin.topic.postNameUnique')}}",//验证地址
                            message: '名称已经存在',//提示消息
                            delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                            type: 'POST',//请求方式
                            /*自定义提交数据，默认值提交当前input value*/
                            data: function(validator) {
                                return {
                                    name: $('#title').val()
                                }
                            }
                        },
                        notEmpty: {/*非空提示*/
                            message: '名称不能为空'
                        },
                        stringLength: {
                            max:32,
                            message: '名称字数不超过32个'
                        }
                    }
                },
                'cases[]':{
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '病例不能为空'
                        }
                    }
                },
                case_id: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '病例不能为空！'
                        }
                    }
                },
                time: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '时间间隔不能为空'
                        },
                        regexp: {
                            regexp: /^([0-9]+)$/,
                            message: '请输入正确的时间间隔'
                        }
                    }
                },
                total: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '总分不能为空'
                        },
                        regexp: {
                            regexp: /^([0-9]+)$/,
                            message: '请输入正确的总分'
                        }
                    }
                },
                mins: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '时间不能为空'
                        },
                        regexp: {
                            regexp: /^[1-9]\d*$/,
                            message: '请输入正确的时间'
                        },
                        stringLength: {
                            max:20,
                            message: '长度不超过20个'
                        }
                    }
                }
            }
        });
    });
</script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'course_module','Unique':'{{route('osce.admin.topic.postNameUnique')}}','excel':'{{route('osce.admin.topic.postImportExcel')}}','clinical_add':'{{route('osce.admin.case.getCreateCase')}}','clinicalList':'{{route('osce.admin.topic.getSubjectCases')}}','goodList':'{{route('osce.admin.topic.getSubjectSupply')}}','topticOptionMaxNumer':'{{config('osce.topticOptionMaxNumer')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增考试项目</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.topic.postAddTopic')}}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="title" name="title" maxlength="32"/>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group display-none">
                            <label class="col-sm-2 control-label">类别</label>
                            <div class="col-sm-10">
                                <select id="select_Category" class="form-control" name="category"/>
                                    <option value="1">问诊</option>
                                    <option value="2">查询</option>
                                    <option value="3">操作</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed display-none"></div>

                        <div class="form-group display-none">
                            <label class="col-sm-2 control-label">操作</label>
                            <div class="col-sm-5">
                                <select id="select_Category" class="form-control" name="category"/>
                                    <option value="1">内科</option>
                                    <option value="2">外科</option>
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <select id="select_Category" class="form-control" name="category"/>
                                    <option value="1">胸穿</option>
                                    <option value="2">腹穿</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed display-none"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">时间限制(分钟)</label>
                            <div class="col-sm-10">
                                <input id="time" class="form-control" name="mins" value="{{$time}}" placeholder="请输入分钟数" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例</label>
                            <div class="col-sm-10">
                                <select id="select-clinical" class="form-control" name="cases[]" multiple="multiple" />
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">总分</label>
                            <div class="col-sm-10">
                                <input id="total" class="form-control" name="total"/>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="desc" class="form-control" name="desc"/>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-12" id="checkbox_div">
                                <label class="check_label checkbox_input checkbox_one" style="height: 15px;line-height: 28px;margin-left: 13.7%;">
                                     <div class="check_icon" style="display: inline-block;margin:5px 0 0 5px;float:left;"></div>
                                     <input type="checkbox" name="special_score_flag" value="0" checked="checked">
                                     <span class="check_name" style="display: inline-block;float:left;">特殊评分项</span>
                                </label>
                            </div>
                            
                            <div class="col_special" style="display: none;">
                                <label class="col-sm-2 control-label">&nbsp;</label>
                                <div class="col-sm-10" id="col_special">
                                    <div class="ibox float-e-margins">
                                        <div class="ibox-title">
                                            <h5></h5>
                                            <div class="ibox-tools">
                                                <button type="button" class="btn btn-outline btn-default" id="add-special-score">新增特殊评分项</button>
                                            </div>
                                        </div>
                                        <div class="ibox-content">
                                            <table class="table table-bordered" id="special-score">
                                                <thead>
                                                <tr>
                                                    <th width="70%">名称</th>
                                                    <th>分数</th>
                                                    <th width="10%">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody index="0">
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">物品准备</label>
                            <div class="col-sm-10">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5></h5>
                                        <div class="ibox-tools">
                                            <button type="button" class="btn btn-outline btn-default" id="add-things">新增物品</button>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-bordered" id="things-use">
                                            <thead>
                                                <tr>
                                                    <th width="481">用物</th>
                                                    <th>数量</th>
                                                    <th width="160">操作</th>
                                                </tr>
                                            </thead>
                                            <tbody index="0">
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">评分标准</label>
                            <div class="col-sm-10">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5></h5>
                                        <div class="ibox-tools">
                                            <button type="button" class="btn btn-outline btn-default" id="add-new">新增考核点</button>
                                            <a href="javascript:void(0)" class="btn btn-outline btn-default" id="file1" style="height:34px;padding:5px;color:#333;">
                                                导入<input type="file" name="topic" id="file0" multiple="multiple" />
                                            </a>
                                            {{--<a href="{{route('osce.admin.topic.getToppicTpl')}}" class="btn btn-outline btn-default" style="float: right;color:#333;">下载模板</a>--}}
                                            <a href="{{$tempUrl}}" class="btn btn-outline btn-default" style="float: right;color:#333;">下载模板</a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-bordered" id="judgement">
                                            <thead>
                                                <tr>
                                                    <th>序号</th>
                                                    <th>考核内容</th>
                                                    <th width="120">分数</th>
                                                    <th width="160">操作</th>
                                                </tr>
                                            </thead>
                                            <tbody index="0">

                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <input class="btn btn-primary" id="submit-btn" type="submit" value="保存">
                                <a class="btn btn-white" id="cancel-btn" href="javascript:void(0)">取消</a>
                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>
<script>
$(function() {
    /**
     * 弹出新增
     * @author mao
     * @version 3.4
     * @date    2016-04-20
     */
    var type_status = location.href.split('?')[1];
    $('#cancel-btn').click(function() {
        if(type_status != undefined){
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        } else {
            location.href = '{{route("osce.admin.topic.getList")}}';
        }
    });
})
</script>
@stop{{-- 内容主体区域 --}}