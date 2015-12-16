@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/css/demo.css')}}">
    <style type="text/css">

    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('msc/admin/coursemanage/js/coursemanage.js')}}"></script>
    <script>

    </script>
@stop
@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'course_vcr'}">

    <div>
        <div id="divPlugin" class="video"></div>
    </div>
@stop
