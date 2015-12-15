@extends('layouts.usermanage')

@section('only_css')
    <link href="{{asset('')}}" rel="stylesheet">
@stop

@section('only_js')
    <script src="{{asset('')}}"></script>
    <script>
        $(function(){
            var $check_label=$(".check_label");
            $check_label.click(function(){
                alert("123");
                $(this).children(".check_icon").addClass("check");
            })
        })
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>设备管理员</h5>
            </div>
            <div>
                <label class="check_label checkbox_input">
                    <div class="check_icon"></div>
                    <input type="checkbox" value="">
                </label>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
