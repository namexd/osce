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
        .pin_box{width:720px;height:500px;margin:50px auto 0;}
        .pin_box table{width:100%;margin-bottom:20px;padding-bottom:10px;background:#fff;}
        .pin_box table th{height:40px;line-height:40px;background:#2B3A40;color:#fff;}
        .pin_box table td{text-align: center;line-height:1.5em;padding:10px;color:#333;}
        .pin_box .red{float:right;color:#ED5565;}
        .clearfix:after{content:""; display:table;  clear:both; }
        .clearfix{*zoom:1}
        .pin_title{height:40px;line-height:40px;color:#000;font-weight:bold;text-align: center;}
        #marquee p{margin:0;line-height:2em;}
    </style>
@stop
@section('content')
    <div class="pin_box">
        <p class="clearfix pin_title">临床技能中心2015年第3期技能考试<span class="red time">12-20 08:55</span></p>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                @foreach($list as $key=>$v)
                <th>{{ $key }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($list as $key=>$v)
                <td>{{$v[0]->name}}</td>
                @endforeach
            </tr>
            <tr>
                @foreach($list as $key=>$v)
                   @foreach($v as $z=>$k)
                   @if($z==1)
                    <td>{{$k->name}}</td>
                   @endif
                   @endforeach
                @endforeach
            </tr>
            <tr>
                @foreach($list as $key=>$v)
                    @foreach($v as $z=>$k)
                        @if($z==2)
                            <td>{{$k->name}}</td>
                        @endif
                    @endforeach
                @endforeach
            </tr>
            <tr>
                @foreach($list as $key=>$v)
                    @foreach($v as $z=>$k)
                        @if($z==3)
                            <td>{{$k->name}}</td>
                        @endif
                    @endforeach
                @endforeach
            </tr>
        </table>
        <marquee id="marquee" style="width:100%;height:200px" Behaviour="alternate" scrollamount="2" direction="up" >
            <p style="font-size:18px;color:#ED5565;">考场纪律说明：</p>
           {{ $description  }}
        </marquee>
    </div>
@stop{{-- 内容主体区域 --}}
@section('only_js')
@stop

