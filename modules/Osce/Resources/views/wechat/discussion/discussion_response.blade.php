@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <style type="text/css">
        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .content-box{
            padding: 20px;
            background: #fff;
            text-align: left;
        }
        .history-list{margin-top: 10px}
        .history-list li{
            background-color: #fff;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .history-list li textarea{
            width: 100%;
            height: 120px;
        }
        /*header*/
        .item-l,.item-c,.item-r{
            float: left;
            color: #cccccc;
            margin: 2px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .item-l{width: 20%;color:#42b2b1;}
        .item-c{width: 60%}
        .item-r{width: 20%}
        .title-con{padding-top: 10px;}

        /*content*/
        .content-header{margin-bottom: 10px;}
        .content-l{
            font-size: 12px;
            width: 60%;
            float: left;
        }
        .content-l span{margin: 0 3px;}
        .content-l span:first-child{margin-left: 0;}
        .content-l .student{color: #42b2b1;}
        .content-l .time{color: #cccccc;}
        .content-r{
            width: 40%;
            float: left;
        }
        .option{
            z-index: 10;
            position: absolute;
            background: #e7eaed;
            top: 45px;
            right: 0;
            display: none;
        }
        .option li{
            margin: 0;
            height: 30px;
            width: 60px;
            line-height: 30px;
            text-align: center;
        }
        .btn2{background: #42b2b1}
    </style>
@stop
@section('only_head_js')
    
@stop


@section('content')
    <ul class="option">
        <li><a href="">回复</a></li>
        <li><a href="#">编辑</a></li>
        <li><a href="">删除</a></li>
    </ul>
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       回复
        <a class="right header_btn" href="javascript:void(0)">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="content-box">
        <div>
            <h2 class="title">关于OSEC考试2015第三期考试的疑问</h2>
            <div class="title-footer">
                <div class="item-l">学生1</div>
                <div class="item-c">2015-12-23 12:23:34</div>
                <div class="item-r">&nbsp;</div>
                <div class="clearfix"></div>
            </div>
            <div class="title-con">
                <p>关于2015年第3期技能培训考场安排，存在下面3方面的疑问，麻烦帮忙解答下，谢谢！</p>
                <p>1、XXXXXXXXXXXXXXXXXXXXXXXXXX安排；</p> 
                <p>2、XXXXXXXXXXXXXXXXXXXXXXXXXXx的安排；</p>
                <p>3、XXXXXXXXXXXXXXXXXXXXXXXXXX的安排</p>
            </div>
        </div>
    </div>
    <ul class="history-list">
        <li>
            <textarea placeholder="请在此输入"></textarea>
            <input type="submit" value="提交" class="btn2" />
        </li>
    </ul>
@stop