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
    <input type="hidden" id="parameter"
           value="{'pagename':'course_vcr','ip':'{{$vcr->ip}}','port':'{{$vcr->port}}',
           'username':'{{$vcr->username}}','password':'{{$vcr->password}}'}">
    <div>
        <div class="vcr-head">
            <div id="vcr-name" class="pull-left">{{empty($vcr->name)? '-':$vcr->name}}</div>
            <a href="{{route('msc.admin.courses.getCoursesVcr',['lab_id'=>$vcrRelation->resources_lab_id])}}" target="_parent"><span id="vcr-go" class="fa fa-arrows-alt pull-right"></span></a>
        </div>
        <div id="divPlugin" class="video"></div>
    </div>
@stop
