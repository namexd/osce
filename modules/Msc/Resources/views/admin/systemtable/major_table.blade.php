@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
        .button_margin{margin-right: 10px}
        .loading{ width:82px; height: 34px; position: relative;
            border-radius: 3px; cursor: pointer;}
        #load_in{opacity: 0;width: 100%;height: 100%; color:inherit;
            background:#fff;border:1px solid #e7eaec;cursor: pointer;}
        .loading p{ text-align: center; position: absolute;
            font-size:14px;font-weight: 400; color: #3c3c3c; left: 12px;
            top: 6px;cursor: pointer;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/ajaxupload.js')}}"></script>
    <script src="{{asset('msc/admin/systemtable/systemtable.js')}}"></script>

@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'major_table','deleteUrl':'{{ route('msc.admin.profession.ProfessionDeletion') }}','stopUrl':'{{ route('msc.admin.profession.ProfessionStatus') }}','inUrl':'{{route('msc.admin.profession.ProfessionImport')}}'}" />
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row table-head-style1">
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
                <button id="addprofession" class="right btn btn-success" data-toggle="modal" data-target="#myModal">新增专业</button>
                <span class="right button_margin  btn-white loading" id = "in">
                    <p>导入专业</p>
                    <input  type="file" name="training" id="load_in"  value="">
                </span>
            </div>
		</div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <form action="" class="container-fluid" id="list_form">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>专业代码</th>
                            <th>专业名称</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('msc.admin.profession.ProfessionList',['keyword'=>@$keyword])}}">全部</a>
                                        </li>
                                        <li>
                                            <a href="{{route('msc.admin.profession.ProfessionList',['keyword'=>@$keyword,'status'=>'2'])}}">正常</a>
                                        </li>
                                        <li>
                                            <a href="{{route('msc.admin.profession.ProfessionList',['keyword'=>@$keyword,'status'=>'1'])}}">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                       @if(!empty($list))
                           @foreach($list as $k => $list)
                        <tr>
                            <td>{{($number+$k)}}</td>
                            <td class="code">{{$list['code']}}</td>
                            <td class="name">{{$list['name']}}</td>
                            <td class="status" data="{{$list['status']}}">@if($list['status']==1)正常@else<span class="state2">停用</span>@endif</td>
                            <td>
                                <a href=""  data="{{$list['id']}}"  class="state1 edit" data-toggle="modal" data-target="#myModal" style="text-decoration: none"><span>编辑</span> </a>
                               @if($list['status']==1)
                                <a   data="{{$list['id']}}"  data-type="0"  class="state2 modal-control stop">停用</a>
                                @else
                                    <a   data="{{$list['id']}}" data-type="1" class="state2 modal-control stop">启用</a>
                                @endif
                                <a  data="{{$list['id']}}"  class="state2 edit_role modal-control delete">删除</a>
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
            <?php echo $pagination->appends(['keyword'=>$keyword,'status'=>$status])->render();?>
        </div>
    </div>

@stop

@section('layer_content')
{{--新增--}}
    <input type="hidden" value="{{route('msc.admin.profession.ProfessionAdd')}}" id="addUrl">
    <div id="my_add"></div>
{{--编辑--}}
<input type="hidden" value="{{route("msc.admin.profession.ProfessionSave")}}" id="editUrl">
<div id="my_edit"></div>

 @stop