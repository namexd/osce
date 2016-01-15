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
    <script src="{{asset('msc/admin/systemtable/systemtable.js')}}"></script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'title_table'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{@$keyword}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button href=""  id="addtitletable"    class="right btn btn-success" data-toggle="modal" data-target="#myModal">新增职称</button>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>名称</th>
                    <th>描述</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle" type="button">状态<span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('msc.admin.professionaltitle.JobTitleIndex',['keyword'=>@$keyword,'status'=>'3'])}}">全部</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.professionaltitle.JobTitleIndex',['keyword'=>@$keyword,'status'=>'2'])}}">正常</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.professionaltitle.JobTitleIndex',['keyword'=>@$keyword,'status'=>'1'])}}">停用</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($list))
                    @foreach($list as $k => $val)
                        <tr>
                            <td class="number">{{ ($number+$k) }}</td>
                            <td class="name">{{ @$val['name'] }}</td>
                            <td class="describe">{{ @$val['description'] }}</td>
                            <td class="status" data="{{@$val['status']}}">@if(@$val['status']==1)<span>正常</span>@else<span class="state2">停用</span>@endif</td>
                            <td class="opera">
                                <a href=""   data="{{@$val['id']}}" class="state1 edit" data-toggle="modal" data-target="#myModal"><span>编辑</span></a>
                                @if($val['status']==1)
                                    <a   data="{{@$val['id']}}"  data-type="0"  class="state2 modal-control stop">停用</a>
                                @else
                                    <a   data="{{@$val['id']}}" data-type="1" class="state2 modal-control stop">启用</a>
                                @endif
                                <span class="state2 delete" data="{{ @$val['id'] }}">删除</span>
                                <input type="hidden" class="setid" value="1"/>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </form>
    </div>
    <div>
    {{--分页--}}
    <div class="btn-group pull-right">
        <?php echo $pagination->appends(['keyword'=>$keyword,'status'=>$status])->render();?>
    </div>
    </div>
@stop

@section('layer_content')
<div id="formBox">
    <!--新增-->
    <input type="hidden" id="addUrl" value="{{ route('msc.admin.professionaltitle.HolderAdd') }}">
    <form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{ route('msc.admin.professionaltitle.HolderAdd') }}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增职称</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>职称名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">职称描述</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="description" />
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
                    <button class="btn btn-primary sure_btn"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal" id="close">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button>
                </div>
            </div>

        </div>
    </form>
    <!--编辑-->
    <input type="hidden" id="editUrl" value="{{route("msc.admin.professionaltitle.HolderSave")}}">
    <form class="form-horizontal" id="edit_from" novalidate="novalidate" action="{{route("msc.admin.professionaltitle.HolderSave")}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑职称</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot">*</span>职称名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="name" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">职称描述</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="description" />
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
                    <button class="btn btn-primary sure_btn"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                    <button class="btn btn-white2 right" id="edit_button" type="button" data-dismiss="modal">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button>
                </div>
            </div>

        </div>
    </form>
</div>

@stop