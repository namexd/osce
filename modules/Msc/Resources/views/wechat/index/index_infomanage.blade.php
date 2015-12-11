@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/index/css/index.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        信息管理
    </div>

    <div>
        <div class="container container_index">
            <div class="row clearfix manageindex row1">
                <div class="col-sm-6 column">
                    <div class="normal_background ">
                        <span class="manageindex_icon icon1"></span>
                        <a  href="{{route('wechat.lab-tools.getOpenToolsOrderIndex')}}" ><span>开放设备的预约使用</span></a>
                    </div>
                </div>
                <div class="col-sm-6 column">
                    <a href="{{ url('/msc/wechat/open-laboratory/type-list') }}">
                    <div class="normal_background">
                        <span class="manageindex_icon icon2"></span>
                        <span>开放实验室的预约使用</span>
                    </div>
                    </a>
                </div>
            </div>
            <div class="row clearfix manageindex row2">
                <div class="col-xs-4 column">
                    <a  href="{{ url('/msc/wechat/resources-manager/borrow-student-manage') }}" >
                        <div class="normal_background">
                            <span class="manageindex_icon icon3"></span>
                            <p>设备外借</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-4 column">
                    <div class="normal_background">
                        <span class="manageindex_icon icon4"></span>
                        <p>考勤签到</p>
                    </div>
                </div>
                <div class="col-xs-4 column">
                    <div class="normal_background">
                        <span class="manageindex_icon icon5"></span>
                        <p>智能保管箱</p>
                    </div>
                </div>
                <div class="col-xs-4 column">
                    <a href="{{ url('/msc/wechat/course-order/course-list') }}">
                        <div class="normal_background ">
                            <span class="manageindex_icon icon6"></span>
                            <p>课程预约</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-4 column">
                    <a href="{{ url('/msc/wechat/resource/resource-manage') }}">
                        <div class="normal_background">
                            <span class="manageindex_icon icon7"></span>
                            <p>资源管理</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="footer">
     <ul class="w_90">
         <li>
             <a href="#"><span class="icon1"></span><p>消息</p></a>
         </li>
         <li class="check">
             <a href="#"><span class="icon2"></span><p>信息管理</p></a>
         </li>
         <li>
             <a href="{{ url('/msc/wechat/personal-center/index') }}"><span class="icon3"></span><p>我</p></a>
         </li>
     </ul>

    </div>
@stop