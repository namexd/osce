@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
        .table .not{
            color:#ccc;
            cursor: none;
        }
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/labmanage/labmanage.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'ban_maintain'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{$keyword}}">
                        <input type="hidden" name="status" class="input-sm form-control" value="{{@$status}}">
                        <input type="hidden" name="schools" class="input-sm form-control" value="{{@$schools}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button class="btn btn_pl btn-success right">
                    <a href=""  class="state1 editadd" data-toggle="modal" data-target="#myModal" style="text-decoration: none" id="add_ban">
                        <span style="color: #fff;">新增楼栋</span>
                    </a>
                </button>
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <form action="" class="container-fluid" id="list_form">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>楼栋名称</th>
                            <th>楼层数</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        所属分院
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{$keyword}}&status={{@$status}}">全部</a>
                                        </li>
                                        @if(!empty($school))
                                            @foreach($school as $sch)
                                                <li>
                                                    <a href="/msc/admin/floor/index?keyword={{$keyword}}&status={{@$status}}&schools={{$sch->id}}">{{@$sch->name}}</a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </th>
                            <th>地址</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}&status=&schools={{@$schools}}">全部</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}&status=1&schools={{@$schools}}">正常</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/floor/index?keyword={{@$keyword}}&status=0&schools={{@$schools}}">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($data))
                            @foreach($data as $k=>$v)
                                <tr>
                                    <td>{{@$k+1}}</td>
                                    <td class="name">{{@$v->name}}</td>
                                    <td  class="floor" data="{{@$v->floor_top}}" data-b="{{@$v->floor_bottom}}">{{intval(@$v->floor_top) + intval(@$v->floor_bottom)}}</td>
                                    <td class="sname" data="{{@$v->school_id}}">{{@$v->sname}}</td>
                                    <td class="address">{{@$v->address}}</td>
                                    <td class="status" data="{{@$v->status}}">@if($v->status)正常@else<span class="state2">停用</span>@endif</td>
                                    <td>
                                        <a href=""  data="{{$v->id}}"  class="state1 edit edit_ban" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                                            <span>编辑</span>
                                        </a>
                                        @if($v->status == 1)
                                            <a  data="{{$v['id']}}" data-type="0" class="state2 modal-control stop">停用</a>
                                        @else
                                            <a  data="{{$v['id']}}" data-type="1" class="state2 modal-control stop">启用</a>
                                        @endif
                                        <a data="{{$v->id}}" class=" edit_role modal-control delete state2">删除</a>
                                        <input type="hidden" class="setid" value="1"/>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>
                </form>

            </div>

        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <?php echo $data->render();?>
        </div>
    </div>
@stop

@section('layer_content')
    <div id="form_box">
        {{--编辑--}}
        <input type="hidden" value="{{route('msc.admin.floor.postEditFloorInsert')}}" id="editUrl">
        <form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{route('msc.admin.floor.postEditFloorInsert')}}" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">编辑楼栋</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼栋名称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name lname" name="name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地上)</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control name add-name floor_top" name="floor_top" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地下)</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control name add-name floor_bottom" name="floor_bottom" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">地址</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control describe add-describe address" name="address" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">所属分院</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b school edit_select" name="school_id">
                            @if(!empty($school))
                                @foreach($school as $ss)
                                    <option value="{{$ss->id}}">{{$ss->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                    <div class="col-sm-9">
                        <select id="select_Category"   class="form-control m-b state" name="status">
                            <option value="-1">请选择状态</option>
                            <option value="1">正常</option>
                            <option value="0">停用</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2 right">
                        <button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                        <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                    </div>
                </div>
            </div>
        </form>

        {{--新增--}}
        <input type="hidden" value="{{route('msc.admin.floor.postAddFloorInsert')}}" id="addUrl">
        <form class="form-horizontal" id="edit_from" novalidate="novalidate" action="{{route('msc.admin.floor.postAddFloorInsert')}}" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">新增楼栋</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼栋名称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地上)</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control name add-name" name="floor_top" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地下)</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control name add-name" name="floor_bottom" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">地址</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control describe add-describe" name="address" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">所属分院</label>
                    <div class="col-sm-9">
                        <select id="select_Category"   class="form-control m-b add_select" name="school_id">
                            <option value="-1">请选择所属分院</option>
                            @if(!empty($school))
                                @foreach($school as $ss)
                                    <option value="{{$ss->id}}">{{$ss->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                    <div class="col-sm-9">
                        <select id="select_Category"   class="form-control m-b " name="status">
                            <option value="-1">请选择状态</option>
                            <option value="1">正常</option>
                            <option value="0">停用</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2 right">
                        <button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                        <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop