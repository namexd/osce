@extends('layouts.base')


@section('header')

    <link href="{{ url('lib/Font-Awesome-4.4.0/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('lib/ionicons/2.0.1/css/ionicons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('lib/AdminLTE-2.3.0/dist/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('lib/AdminLTE-2.3.0/dist/css/skins/_all-skins.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ url('lib/iCheck/flat/blue.css') }}" rel="stylesheet" type="text/css" />

    <!--[if lt IE 9]>
    <script src="{{ url('js/html5shiv.js') }}"></script>
    <script src="{{ url('js/respond.min.js') }}"></script>
    <![endif]-->

    <!-- AdminLTE App -->
    <script src="{{ url('lib/slimScroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('lib/AdminLTE-2.3.0/dist/js/app.min.js') }}" type="text/javascript"></script>
    @yield('header_context')
  {{--  <script src="{{ url('lib/AdminLTE-2.3.0/dist/js/pages/dashboard2.js') }}" type="text/javascript"></script>--}}

    @stop


    @section('body')

            <!--wrapper start-->
    <div class="wrapper">





        <div class="content-wrapper">

            <section class="content-header">



            </section>


            <section class="content" >

@yield('body_content')

            </section>
        </div>



    </div>


    <footer class="main-footer">

        <div class="pull-right hidden-xs">

        </div>

        <strong>Copyright &copy; Copyright 2015-2018 <a href=""></a></strong>华西临床技能中心业务管理系统(<code>MscMis</code>)  版本: v1.0
    </footer>

    <script type="text/javascript">
        $(document).ready(function(){
            $('ul.treeview-menu>li').find('a[href="{{Route::currentRouteName()}}"]').closest('li').addClass('active');  //二级链接高亮
            $('ul.treeview-menu>li').find('a[href=""]').closest('li.treeview').addClass('active');  //一级栏目[含二级链接]高亮
            $('.sidebar-menu>li').find('a[href=""]').closest('li').addClass('active');  //一级栏目[不含二级链接]高亮
        });
    </script>
    <script src="{{ url('lib/AdminLTE-2.3.0/dist/js/demo.js') }}" type="text/javascript"></script>
@stop
