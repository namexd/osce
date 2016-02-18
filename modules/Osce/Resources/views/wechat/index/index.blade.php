@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<style>
  .container_index a:hover{text-decoration: none;}
.container_index { padding-bottom:65px; margin-top: 2%;}
.container_index .row1  span{float: left;line-height: 60px; margin-right:20px; font-size: 16px;}
.container_index .row1 .normal_background{
    padding:10px 15px;
    border:1px solid #dce0e4; border-radius: 4px;
    background-color: #fff;
    width: 100%; }
.container_index .row2 .normal_background{
    padding:10px 0px;
    border:1px solid #dce0e4; border-radius: 4px;
    background-color: #fff;
    width: 100%; text-align: center;}
.container_index .row1 .col-sm-6{padding:8px; padding-bottom: 0;}
.container_index .row2 .col-xs-6{padding:8px;padding-bottom: 0;}
.container_index  a { display: flex;}
.container_index  a p{ color:#000;}
.container_index  a span { color:#000;}
.manageindex .normal_background{ display: inline-block;}
.manageindex .manageindex_icon { width:60px; height: 61px; display: inline-block;background:url("{{asset('osce/images/icons.png')}}");background-size: cover;}

.manageindex .icon1{ background-position:0 0px;}
.manageindex .icon2{ background-position:0 -58px;}
.manageindex .icon3{ background-position:0 -123px;}
.manageindex .icon4{ background-position:0 -182px;}
.manageindex .icon5{ background-position:0 -243px;}
.manageindex .icon6{ background-position:0 -302px;}
.manageindex .icon7{ background-position:0 -420px;}


.error_attention{
    width: 80%; margin: 0 auto;
    text-align: center;
    margin-top:5em;
}
.error_attention p{
  line-height: 1.2em; margin:1em; font-size: 16px;
}
.error_attention img{ width:35%;}
</style>
@stop
@section('only_head_js')

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        OSCE考试
    </div>

    <div>
        <div class="container container_index">
            <div class="row clearfix manageindex row2">
                <div class="col-xs-6 column">
                    <a  href="{{route('osce.wechat.notice.getSystemList')}}" >
                        <div class="normal_background">
                            <span class="manageindex_icon icon1"></span>
                            <p>资讯&通知</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 column">
                    <a href="{{route('osce.wechat.student-exam-query.getResultsQueryIndex')}}">
                        <div class="normal_background">
                            <span class="manageindex_icon icon2"></span>
                            <p>成绩查询</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 column">
                    <a href="{{route('osce.wechat.invitation.getList')}}">
                        <div class="normal_background">
                            <span class="manageindex_icon icon3"></span>
                            <p>预约申请</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 column">
                    <a href="{{route('osce.wechat.getDiscussionLists')}}">
                        <div class="normal_background ">
                            <span class="manageindex_icon icon4"></span>
                            <p>讨论区</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 column">
                    <a href="{{route('osce.wechat.getTrainlists')}}">
                        <div class="normal_background">
                            <span class="manageindex_icon icon5"></span>
                            <p>考前培训</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 column">
                    <a href="{{route('osce.wechat.notice-list.getSystemList')}}">
                        <div class="normal_background">
                            <span class="manageindex_icon icon6"></span>
                            <p>系统消息</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>
@stop