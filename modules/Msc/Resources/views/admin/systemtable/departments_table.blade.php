@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .list-group-item{cursor:pointer;}
        span.indent{margin-left:10px;margin-right:10px}
        span.icon{margin-right:5px}
        .node-treeview11{color:#428bca;}
        .node-treeview11:hover{background-color:#F5F5F5;}
        b{font-weight: normal;}
        .treeview .checked {background-color: #408aff; color: #fff;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/systemtable/systemtable.js')}}"></script>

@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'departments_table','selectUrl':'{{ url('/msc/admin/dept/select-dept')}}','addUrl':'{{ route('msc.Dept.AddDept') }}','deleteUrl':'{{ route('msc.Dept.DelDept') }}','updateUrl':'{{ route('msc.Dept.UpdateDept') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="col-sm-5">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <h5>科室列表</h5>
                    <input type="button" class="btn  btn_pl btn-success right"  id="new-add-father" value="新增科室">
                </div>
                <div class="ibox-content">
                    <div class="treeview">
                        <ul class="list-group">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <h5>科室信息</h5>
                    <input type="button" class="btn btn_pl btn-success right"  id="new-add-child" value="新增子科室">
                    <button class="btn btn_pl btn-white right button_margin marr_15" id="delete">删除该科室</button>
                    <input type="hidden" value="" id="hidden_this_id"/>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="add_department">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>科室名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control name add-name" name="name" value="" placeholder="请输入科室名称" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>上级科室</label>
                            <div class="col-sm-9">
                                <input type="text" disabled  class="form-control name add-parent" name="up_name" value="" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">描述</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control describe add-describe" name="describe" placeholder="请输入描述" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-9" style="float:right; text-align: right;">
                                <button class="btn btn-primary"  type="button" id="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;认</button>
                                <button class="btn btn-primary"  type="button" id="edit_save" style="display:none" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop

@section('layer_content')

@stop