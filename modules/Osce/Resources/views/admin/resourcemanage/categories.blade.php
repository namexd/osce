@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
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
</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script>
<script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'categories','excel':'{{route('osce.admin.topic.postImportExcel')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增科目</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.topic.postAddTopic')}}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="title" name="title">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="select_Category" required  class="form-control m-b" name="desc"/>
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
                                            <a  href="{{route('osce.admin.topic.getToppicTpl')}}" class="btn btn-outline btn-default" style="float: right;color:#333;">下载模板</a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-bordered">
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