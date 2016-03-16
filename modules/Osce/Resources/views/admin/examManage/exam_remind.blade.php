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
        .exam-intro{
            margin-top: 45px;
            font-size: 44px;
            color: #ED5565;
        }
    </style>
@stop
@section('content')
    <div class="pin_box">
        <p class="clearfix pin_title">{{ $exams->name }}<span class="red time"><?php echo date('Y-m-d H:i',time())?></span></p>
        <div id="name_list">
            
        </div>
        <p class="exam-intro">考场纪律说明：</p>
        <marquee id="marquee" style="width:100%;height:300px" Behaviour="alternate" scrollamount="2" direction="up">
            {!! $exams->rules !!}
        </marquee>
    </div>
@stop{{-- 内容主体区域 --}}
@section('only_js')
    <script>
        $(function(){

            /**
             * 处理小于10的情况
             * @author mao
             * @version 1.0
             * @date    2016-03-16
             * @param   {[number]}   res [传入参数]
             * @return  {[number]}       [返回值]
             */
            function toFormat(res) {
                return res > 9 ? res: '0' + res;
            }

            /**
             * 实时时间
             * @author mao
             * @version 1.0
             * @date    2016-03-16
             */
            setInterval(function() {
                //获取时间
                var nowTime = new Date(),
                    time = nowTime.getFullYear() + '-' + (nowTime.getMonth() > 8 ? (parseInt(nowTime.getMonth()) + 1): '0' + (parseInt(nowTime.getMonth()) + 1)) + '-' + toFormat(nowTime.getDate()) +' '+ toFormat(nowTime.getHours()) + ':' + toFormat(nowTime.getMinutes());

                $('.time').text(time);
            }, 1000);

            /**
             * 获取考生信息
             * @author mao
             * @version 1.0
             * @date    2016-03-16
             */
            function getExamInfo(res,nowPage) {

                //请求数据
                $.ajax({
                    type: 'get',
                    url: '',
                    data: {page:nowPage},
                    success: function(res) {
                        var html = '',
                            data = res.rows.data;

                        if(res.code == 1) {
                            //todo
                            for(var i = 0; i < data.length; i++) {
                                html += '<dl><dt>'+ data[i].stationName +'</dt>';
                                for(var j in data) {
                                    html = html + '<dd>'+ data[i].student[j] +'</dd>';
                                }
                                html += '</dl>'; 
                            }

                            $('#name_list').html(html);

                            //获取表头的列数，设置宽度
                            var count = data.length,
                                _w = 100/count;

                            $("#name_list dl").css({width: _w+"%"});

                            //刷新数据
                            setTimeout(function() {
                                if(nowPage < data.total) {
                                    nowPage ++;
                                } else {
                                    nowPage = 1;
                                }
                                getExamInfo(nowPage);
                            },5000);

                        }
                    }
                });
            }
            //启动数据
            getExamInfo(res,1);


        })
    </script>
@stop

