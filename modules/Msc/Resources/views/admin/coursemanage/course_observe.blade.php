@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/coursemanage/css/course_observe.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/css/demo.css')}}">
    <style type="text/css">
        iframe{
            margin-right: 10px;
        }
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
           value="{'pagename':'course_observe','lessonUrl':'{{route("msc.admin.courses.getClassObserveVideo")}}','vcrUrl':'{{route("msc.admin.courses.getClassroomVcr")}}'}">
    <div class="row  main-content">
        <div class="content-left ibox-content">
            <div class="serach-box">
                <form action="{{route("msc.admin.courses.getClassObserve")}}">
                    <input type="text" placeholder="按教室编号搜索" name="keyword" id="serach-text"><input type="submit" class="btn btn-primary"  id="serach-btn" value="搜索">
                </form>
            </div>
            <nav class="classroom-list">
               <ul>

                   <li class="first-level">
                       <p flag="false">临床教学楼
                           <i class="glyphicon glyphicon-chevron-right"></i>
                           <i class="glyphicon glyphicon-chevron-down"></i>
                       </p>
                       <ul>
                           @forelse($data as $item)
                           <li class="second-level">
                               <p flag="false" id="{{$item['id']}}">
                                   {{$item['code']}}{{$item['name']}}
                               </p>
                               {{--<ul>
                                   <li class="third-level">101</li>
                                   <li class="third-level">102</li>
                                   <li class="third-level">103</li>
                               </ul>--}}
                           </li>
                            @empty
                           @endforelse
                       </ul>
                   </li>


               </ul>
            </nav>
        </div>
        <div class="content-right ibox-content">
            <div class="row content-head">
                <div class="pull-left head-left">
                    <label for=""><span>课程内容:</span><span id="lesson"></span></label>
                    <label for=""><span>授课老师:</span><span id="teacher"></span></label>
                </div>
                <div class="pull-right head-right">
                    <span id="year"></span>
                    <span id="hour"></span>
                    <span>信号强度<span id="info-strength"></span></span>
                </div>
            </div>
            <div id="vcr-box">

            </div>
        </div>
    </div>
@stop