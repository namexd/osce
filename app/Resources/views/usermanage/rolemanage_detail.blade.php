@extends('layouts.usermanage')

@section('only_css')

    <link href="{{asset('osce/admin/css/common.css')}}" rel="stylesheet">
    <style>
        .clear_padding{
            padding: 0;
        }
        .clear_margin{
            margin: 0;
        }
        .border-bottom{
            border-bottom: none!important;
        }
        .btn-default{
            color: #9c9c9c;
        }
        .btn_padding{
            padding: 2px 5px;
        }
        .btn_focus{
            background-color: #bababa!important;
            color: #fff!important;
        }
        .btn:hover{
            color: #fff;
            background-color: #bababa;
        }
        .btn:focus{
            background-color: #fff;
            color: #9c9c9c;
        }
        .btn.btn-primary.marl_10{
            background-color: #1ab394;
            border-color: #1ab394;
        }
        body {
            font-family: 微软雅黑;
            font-size: 14px;
        }
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/usermanage/rolemanage.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'rolemanage_detail'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $name }}</h5>
            </div>
            <div class="ibox-content">
                <div style="margin-top: 10px;display: none;">
                    <label class="check_label checkbox_input">
                        <div class="check_icon" style="display:inline-block"></div>
                        <input type="checkbox" value="">
                        <span style="float: right;text-indent: 6px">技能中心管理系统</span>
                    </label>
                    <label class="check_label checkbox_input" style="margin-left: 15px">
                        <div class="check_icon" style="display: inline-block"></div>
                        <input type="checkbox" value="">
                        <span style="float: right;text-indent: 6px">OSCE考试智能管理系统</span>
                    </label>
                    <label class="check_label checkbox_input" style="margin-left: 15px">
                        <div class="check_icon" style="display: inline-block"></div>
                        <input type="checkbox" value="">
                        <span style="float: right;text-indent: 6px">智能分析系统</span>
                    </label>
                    <div class="col-xs-6 col-md-2" style="float: right">
                        <a href="#" class="btn btn-primary marl_10" id="saveForm" style="text-decoration: none">保存</a>
                    </div>
                </div>
                {{--<div class="hr-line-dashed"></div>--}}
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        {{--<li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> Web端</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">微信端</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">Pad端</a></li>--}}
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <form method="post" action="{{ route('auth.SavePermissions') }}" id="authForm" class="panel-body">
                                <input type="hidden" value="{{ $role_id }}" name="role_id">
                                <table class="table table-striped" id="table-striped" style="border: 1px">
                                    <thead>
                                    <!-- <tr>
                                        <th style="width: 30%;padding-bottom: 28px">&nbsp;</th>
                                    </tr> -->
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="background:#fff;border:none;">
                                                <ul class="clear_padding">
                                                    @foreach($MenusList as $val)
                                                        <li>
                                                            <div class="ibox float-e-margins clear_margin">
                                                                <label class="check_label checkbox_input clearfix" hidevalue="{{ @$val['SysPermissionMenu']['permission_id'] }}">
                                                                    <div style="float:left;"  class="check_real check_icon display_inline @if(!empty($val['SysPermissionMenu']['permission_id']) && in_array(@$val['SysPermissionMenu']['permission_id'],$PermissionIdArr)) check @endif"></div>
                                                                    @if(!empty($val['SysPermissionMenu']['permission_id']) && in_array(@$val['SysPermissionMenu']['permission_id'],$PermissionIdArr))<input type="hidden"  name="permission_id[]" value="{{ @$val['SysPermissionMenu']['permission_id'] }}"> @endif
                                                                    <span style="float:left;position: relative;top:-1px;" class="check_name">&nbsp;&nbsp;{{ @$val['name'] }}</span>
                                                                </label>
                                                                <div class="ibox-tools">
                                                                    <a class="collapse-link">
                                                                        <i class="fa fa-chevron-up"></i>
                                                                    </a>
                                                                </div>
                                                                <div class="ibox-content" style="border-top:none">
                                                                    @if(!empty($val['child']))
                                                                        @foreach($val['child'] as $v)
                                                                            <button type="button" hidevalue="{{ @$v['SysPermissionMenu']['permission_id'] }}" class="btn btn-outline @if(!empty($v['SysPermissionMenu']['permission_id']) && in_array(@$v['SysPermissionMenu']['permission_id'],$PermissionIdArr)) btn_focus @else btn-default2 @endif  font10 btn_padding" permission_id="{{ @$v['SysPermissionMenu']['permission_id'] }}" >{{ @$v['name'] }}</button>
                                                                            @if(!empty($v['SysPermissionMenu']['permission_id']) && in_array(@$v['SysPermissionMenu']['permission_id'],$PermissionIdArr))
                                                                                <input type="hidden"  name="permission_id[]" value="{{ @$v['SysPermissionMenu']['permission_id'] }}">
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary marl_10" type="submit">保存</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@stop{{-- 内容主体区域 --}}
