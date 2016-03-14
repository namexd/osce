@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style type="text/css">
        @charset "utf-8";
        /* CSS Document */
        html, body, div, span, iframe, h1, h3, h4, h5, h6, p, blockquote, pre, a, address,
        big, cite, code, del, em, font, img, ins, small, strong, var, b, u, center, dl,
        dt, dd, ol, ul, li, fieldset, form, label, legend{margin: 0px;padding: 0px}
        body{width:100%;height:100%;background:#E1E1E8;font-size:16px;line-height:1em;font-family: "微软雅黑";position:relative}
        ul li{ list-style:none}
        .pin_box{width:100%;height:auto;margin:50px auto 0;padding: 20px 20px 0 20px;font-size: 44px;}
        .pin_box .red{float:right;color:#ED5565;}
        .clearfix:after{content:""; display:table;  clear:both; }
        .clearfix{*zoom:1}
        .pin_title{height:80px;line-height:80px;color:#000;font-weight:bold;text-align: center;}
        #marquee{margin-top:20px}
        #marquee p{margin:0;line-height:2em;}
        #name_list{width:100%;height:auto;min-height:420px;background:#fff}
        #name_list dl{float:left}
        #name_list dl dt{height:80px;line-height:80px;background:#2B3A40;color:#fff;text-align:center}
        #name_list dl dd{text-align: center;line-height:1.5em;padding:10px;color:#333;}
    </style>
@stop
@section('content')
    <div class="pin_box">
        <p class="clearfix pin_title">{{ $exams->name }}<span class="red time"><?php echo date('Y-m-d H:i',time())?></span></p>
        <div id="name_list">
            <input class="name_count" type="hidden" value="{{count($list)}}">
            @foreach($list as $key=>$lists)
                <dl>
                    <dt>{{$key}}</dt>
                    @foreach($lists as $name)
                        <dd>{{$name->name}}</dd>
                    @endforeach
                </dl>
            @endforeach
        </div>
        <marquee id="marquee" style="width:100%;height:200px" Behaviour="alternate" scrollamount="2" direction="up" >
            <p style="font-size:44px;color:#ED5565;">考场纪律说明：</p>
            {!! $exams->rules !!}
        </marquee>
    </div>
@stop{{-- 内容主体区域 --}}
@section('only_js')
    <script>
        $(function(){
            var i=parseInt($(".name_count").val());
            var w=100/i;
            $("#name_list dl").css({width:w+"%"});
            setInterval(function(){
            	window.location.reload();
            },60000)
        })
    </script>
@stop

