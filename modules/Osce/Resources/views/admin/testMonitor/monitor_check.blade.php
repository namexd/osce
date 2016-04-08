@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        #myModalLabel{color: #16BEB0;}
        /*强调颜色*/
        .state{color: #ed5565;}
        /*标题展示区域*/
        .success-element:hover {cursor: default!important;}
        .titleBackground{background-color: #E9EDEF!important;}
        .messageColor{color: #999A9E}
    </style>
@stop

@section('only_js')
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'monitor_check'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">视频回看</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <ul class="sortable-list connectList agile-list ui-sortable" style="background-color: #fff;">
                <li class="success-element titleBackground">
                    <p class="font20 fontb">2016.3.18第2场考试</p>
                </li>
            </ul>
            <div class="panel-heading">
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="list_all">
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
