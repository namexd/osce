@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/systemtable/resource_table.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'resource_table'}" />
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row table-head-style1">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{@$keyword}}">
                        <input type="hidden" name="status" class="input-sm form-control" value="{{@$status}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button href=""   id="addResources"    class="right btn btn-success" data-toggle="modal" data-target="#myModal">新增资源</button>
            </div>
		</div>
        <div class="ibox float-e-margins">
            <form action="" class="container-fluid ibox-content" id="list_form">
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>资源名称</th>
                        <th>
                            <div class="btn-group Examine">
                                <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                    资源类型
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(!empty($devicetype))
                                        @foreach($devicetype as $type)
                                            <li>
                                                <a href="{{route('msc.admin.resources.ResourcesIndex',['keyword'=>@$keyword,'devices_cate_id'=>@$type->id])}}">{{$type->name}}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </th>
                        <th>设备说明</th>
                        <th>
                            <div class="btn-group Examine">
                                <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle" type="button" aria-expanded="false">
                                    状态
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        
                                        <a href="{{ route('msc.admin.resources.ResourcesIndex',['keyword'=>@$keyword,'status'=>'2'])}}">全部</a>
                                    </li>
                                    <li>
                                        <a href="{{route('msc.admin.resources.ResourcesIndex',['keyword'=>@$keyword,'status'=>'1'])}}">正常</a>
                                    </li>
                                    <li>
                                        <a href="{{route('msc.admin.resources.ResourcesIndex',['keyword'=>@$keyword,'status'=>'0'])}}">停用</a>
                                    </li>
                                </ul>
                            </div>
                        </th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list))
                        @foreach($list as $val)
                    <tr>
                        <td>{{$val['id']}}</td>
                        <td class="name">{{$val['name']}}</td>

                        <td class="catename"   data="{{$val['devices_cate_id']}}">{{$val['catename']}}</td>

                        <td class="detail">{{$val['detail']}}</td>

                        <td class="status" data="{{$val['status']}}">@if($val['status']==1)正常@else<span class="state2">停用</span>@endif</td>
                        <td>
                            <a href=""   data="{{$val['id']}}" class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none"><span>编辑</span> </a>

                            @if($val['status']==1)
                                <a   data="{{$val['id']}}"  data-type="0"  class="state2 modal-control stop">停用</a>
                            @else
                                <a   data="{{$val['id']}}" data-type="1" class="state2 modal-control stop">启用</a>
                            @endif
                            <a   data="{{$val['id']}}" class="state2 edit_role modal-control delete">删除</a>
                            <input type="hidden" class="setid" value="1"/>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </form>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <?php  echo $pagination->render()?>
        </div>
	</div>
@stop

@section('layer_content')
  
    {{--新增--}}
    <form class="form-horizontal" id="add_from" novalidate="novalidate"  action="{{ route('msc.admin.resources.ResourcesAdd') }}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            
            <h4 class="modal-title" id="myModalLabel">新增资源</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>资源名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>资源类型</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b cate" name="devices_cate_id">
                        <option value="-1">请选择类型</option>
                        @if(!empty($devicetype))
                            @foreach($devicetype as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">说明</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="detail" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b state" name="status">
                        <option value="-1">请选择状态</option>
                        <option value="1">正常</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
{{--编辑--}}
    <form class="form-horizontal" id="edit_from" novalidate="novalidate" action="{{route("msc.admin.resources.ResourcesSave")}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑资源</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>资源名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>资源类型</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b cate" name="devices_cate_id">
                        @if(!empty($devicetype))
                            @foreach($devicetype as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">说明</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="detail" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                <div class="col-sm-9">
                    <select id="select_Category"   class="form-control m-b state" name="status">
                        <option value="1">正常</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop