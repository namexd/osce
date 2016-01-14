@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
    
@stop


@section('content')
<div class="ibox-title route-nav">
    <ol class="breadcrumb">
        <li><a href="#">系统设置</a></li>
        <li class="route-active">添加场所类别</li>
    </ol>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">添加场所类别</h5>
            </div>

        </div>
    <form class="container-fluid ibox-content" id="list_form" action="{{route('osce.admin.config.postAreaStore')}}" method="post">


        <div class="col-md-9 col-sm-9">
            <div class="hr-line-dashed"></div>
            <div class="hr-line-dashed"></div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-2 control-label">场所类别:</label>
                <div class="col-sm-10">
                    <input type="text"  id="name" name="name" class="form-control">
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-2 control-label">描述:</label>

                <div class="col-sm-10">
                    <input type="text"  id="description" name="description" class="form-control">
                </div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-2 control-label">类别:</label>

                <div class="col-sm-10">
                    <input type="text"  id="cate" name="cate" class="form-control">
                </div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-2 control-label">代码:</label>

                <div class="col-sm-10">
                    <input type="text"  id="code" name="code" class="form-control">
                </div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <button class="btn btn-primary" type="submit">保存</button>
                    <button class="btn btn-white return-pre" type="button">取消</button>
                </div>
            </div>
        </div>


            <div class="btn-group pull-right">
               
            </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}