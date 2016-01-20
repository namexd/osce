@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        label{margin-bottom: 0;}
        .treeview .lab_num{background-color: #f5f5f5;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/labmanage/labmanage.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'resource_maintain','addUrl':'{{ route('msc.admin.LadMaintain.DevicesAdd') }}','editUrl':'{{route('msc.admin.LadMaintain.DevicesTotalEdit')}}','listUrl':'{{route('msc.admin.LadMaintain.LaboratoryListData')}}','deleteUrl':'{{ route('msc.admin.LadMaintain.LadDevicesDeletion') }}','ajaxUrl':'{{ route('msc.admin.LadMaintain.LaboratoryDeviceList')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="col-sm-5">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <select name="" id="ban_select" class="select">
                        <option value="-1">请选择楼栋</option>
                        @if(!empty($location))
                            @foreach($location as $k=>$v)
                                @if($k == 0)
                                    <option value="{{@$v->id}}" selected="selected">{{@$v->name}}</option>
                                @else
                                    <option value="{{@$v->id}}">{{@$v->name}}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="ibox-content">
                    <div class="treeview">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <div class="left">
                        <p class="left">已选实验室：</p>
                        <h5 class="left labname">无</h5>
                    </div>
                    <div class="left" style="margin-left: 20px">
                        <p class="left">容量：</p>
                        <h5 class="left  labtotal " >0人</h5>
                    </div>
                    <input type="button" class="btn btn_pl btn-success right" data-toggle="modal" data-target="#myModal" disabled="disabled" value="新增设备" id="add_device">
                </div>
                <div class="ibox-content overflow">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>设备名称</th>
                            <th>设备类型</th>
                            <th>设备数量</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table><div class="clear">

                    </div>
                    <div class="btn-group pull-right">
                        <ul class="pagination" id="paginationOne">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('layer_content')
    {{--新增--}}
    <form class="form-horizontal" id="add_device_form" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">添加设备</h4>
        </div>
        <div class="modal-body overflow">
            <div class="row" style="padding: 12px 0">
                <div class="col-xs-12 col-md-12">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="">
                            <span class="input-group-btn">
                                <button class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped" id="addition">
                <thead>
                <tr>
                    <th>
                        <label class="check_label checkbox_input check_all">
                            <div class="check_real check_icon display_inline"></div>
                            <input type="hidden" name="" value="">
                        </label>
                    </th>
                    <th>序号</th>
                    <th>数量</th>
                    <th>资源名称</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                资源类型
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" id="device-type">
                            </ul>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="overflow">
                <div class="btn-group pull-right">
                    <ul class="pagination" id="paginationTwo" style="margin: 0;">

                    </ul>
                </div>
            </div>
            <div class="clear"></div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <input type="hidden" id="lab_id">
                    <button class="btn btn-primary"  type="submit" id="addDevice" data-dismiss="modal" aria-hidden="true">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
    {{--编辑--}}
    <form class="form-horizontal" id="edit_form" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑数量</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">资源名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="腹腔镜" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">资源类型</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="type" value="耗材" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">数量</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control describe add-describe plus" name="total">
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary" id="saveEdit"  type="submit" data-dismiss="modal" aria-hidden="true">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop