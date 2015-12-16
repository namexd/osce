@extends('layouts.usermanage')

@section('only_css')
    <link href="{{asset('')}}" rel="stylesheet">
    <style>
        .check_name{
            font-weight: normal;
            float: right;
            text-indent: 6px;
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
                <h5>设备管理员</h5>
            </div>
            <div class="ibox-content">
                <div style="margin-top: 10px">
                    <label class="check_label checkbox_input">
                        <div class="check_icon" style="display: inline-block"></div>
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
                        <a href="#" class="btn btn-primary marl_10" style="text-decoration: none">保存</a>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> Web端</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">微信端</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">Pad端</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <table class="table table-striped" id="table-striped" style="border: 1px">
                                    <thead>
                                    <tr>
                                        <th style="width: 30%;padding-bottom: 28px">一级</th>
                                        <th style="width: 30%;padding-bottom: 28px">二级</th>
                                        <th style="width: 40%;padding-bottom: 28px">极限细则</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td rowspan="2">
                                                <label class="check_label checkbox_input">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">资源管理</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label class="check_label checkbox_input">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">现有资源</span>
                                                </label>
                                            </td>
                                            <td>
                                                <label class="check_label checkbox_input">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">查看</span>
                                                </label>
                                                <label class="check_label checkbox_input" style="margin-left: 15px">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">编辑</span>
                                                </label>
                                                <label class="check_label checkbox_input" style="margin-left: 15px">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">报废</span>
                                                </label>
                                                <label class="check_label checkbox_input" style="margin-left: 15px">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">二维码打印</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label class="check_label checkbox_input">
                                                    <div class="check_icon" style="display: inline-block"></div>
                                                    <input type="checkbox" value="">
                                                    <span class="check_name">新增资源</span>
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <table class="table table-striped" id="table-striped" style="border: 1px">
                                    <thead>
                                    <tr>
                                        <th style="width: 30%;padding-bottom: 28px">一级</th>
                                        <th style="width: 30%;padding-bottom: 28px">二级</th>
                                        <th style="width: 40%;padding-bottom: 28px">极限细则</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td rowspan="2">
                                            <label class="check_label checkbox_input">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">资源管理</span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="check_label checkbox_input">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">现有资源</span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="check_label checkbox_input">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">查看</span>
                                            </label>
                                            <label class="check_label checkbox_input" style="margin-left: 15px">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">编辑</span>
                                            </label>
                                            <label class="check_label checkbox_input" style="margin-left: 15px">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">报废</span>
                                            </label>
                                            <label class="check_label checkbox_input" style="margin-left: 15px">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">二维码打印</span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <label class="check_label checkbox_input">
                                                <div class="check_icon" style="display: inline-block"></div>
                                                <input type="checkbox" value="">
                                                <span class="check_name">新增资源</span>
                                            </label>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">
                                <strong>HTML5</strong>
                                <p>Bootstrap 使用到的某些 HTML 元素和 CSS 属性需要将页面设置为 HTML5 文档类型。在你项目中的每个页面都要参照下面的格式进行设置。备友好的。这次不是简单的增加一些可选的针对移动设备的样式，而是直接融合进了框架的内核中。也就是说，Bootstrap 是移动设备优先的。针对移动设备的样式融合进了框架的每个角落，而不是增加一个额外的文件。</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@stop{{-- 内容主体区域 --}}
