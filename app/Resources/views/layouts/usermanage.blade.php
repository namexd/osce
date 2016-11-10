@extends('layouts.base')
@section('meta')
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
<link href="{{asset('msc/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
<link href="{{asset('msc/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
<link href="{{asset('msc/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
<link href="{{asset('msc/admin/plugins/css/style.min.css?v=3.0.0')}}" rel="stylesheet">
<link href="{{asset('msc/wechat/jquery-confirm/jquery-confirm.css')}}" rel="stylesheet">
<link href="{{asset('msc/common/css/bootstrapValidator.css')}}" rel="stylesheet">
@stop

@section('head_js')
<script src="{{asset('msc/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/jquery-ui-1.10.4.min.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
<script src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>
<!-- �Զ���js -->
<script src="{{asset('msc/wechat/jquery-confirm/jquery-confirm.js')}}"></script>
@stop
@section('head_style')
<style type="text/css">

</style>
@show
@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')

@section('content')
@show{{-- ������������ --}}

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @section('layer_content')
            @show{{-- ������������ --}}
        </div>
    </div>
</div>

@show

@section('footer_js')
<!--ȫ��Css�Զ��岿��-->
<link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
<!-- ȫ��js -->

<script src="{{asset('msc/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>

<!-- �Զ���js -->
<script src="{{asset('msc/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
<script type="text/javascript" src="{{asset('msc/admin/plugins/js/contabs.min.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/content.min.js')}}"></script>
<!-- ������ -->
<script src="{{asset('msc/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>

@show{{-- footer����javscript�ű� --}}

{{-- �����������JS��� --}}
<script type="text/javascript">
    $(document).ready(function(){
        <!--highlight main-sidebar-->
        @section('filledScript')
        @show{{-- ��document ready �������һЩJS���� --}}
    });

</script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('jk/js/jk.js') }}" type="text/javascript"></script>
@section('extraSection')
@show{{-- ��������һЩ��������һ����JS��������HTML --}}
@stop