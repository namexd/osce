@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/admin/plugins/js/plugins/jqueryCalendario/css/calendar.css')}}" rel="stylesheet">
<link href="{{asset('osce/admin/plugins/js/plugins/jqueryCalendario/css/custom_2.css')}}" rel="stylesheet">
<style>
*,
*:after,
*:before {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
}
.main {
    padding: 30px 50px;
    width: 100%;
    margin: 0 auto;
    background-color: #fff;
}
.fc-calendar .fc-row > div.fc-today{
    background-color: #fff;
    box-shadow: none;
}
.fc-calendar .fc-row > div.fc-today > span.fc-date{color: #686a6e;}
.fc-content{background-color: #1ab394!important;}
.fc-content>span {
    color: #fff!important;
    box-shadow: none!important;
}
.fc-calendar .fc-row > div.fc-content:after{
    content: '';
}
.fc-calendar .fc-row > div.btn_disabled {
    background: #fff;
    cursor: default;
}
.fc-calendar .fc-row > div.btn_disabled{
    background:transparent!important;
    color:white;
    cursor:default!important;
}
#time-periods td{text-align: center;}
</style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/jqueryCalendario/js/modernizr.custom.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/jqueryCalendario/js/jquery.calendario.js')}}"></script>
@stop


@section('content')
<!-- 快速排考 -->
<section class="main">
    <div class="custom-calendar-wrap">
        <div id="custom-inner" class="custom-inner">
            <div class="custom-header clearfix">
                <nav>
                    <span id="custom-prev" class="custom-prev"></span>
                    <span id="custom-next" class="custom-next"></span>
                </nav>
                <h2 id="custom-month" class="custom-month"></h2>
                <h3 id="custom-year" class="custom-year"></h3>
            </div>
            <div id="calendar" class="fc-calendar-container"></div>
        </div>
    </div>
    <table class="table" id="time-periods" index="0">
      <tbody></tbody>
    </table>
    <!-- 按钮 -->
    <div class="form-group">
      <div class="col-sm-5 col-sm-offset-5">
          <button class="btn btn-primary" id="save" type="submit">确定</button>
          <a class="btn btn-white" href="#">取消</a>
      </div>
    </div>
</section>

<script type="text/javascript"> 
    $(function() {

        //数据加载
        var codropsEvents = {};

        //获取当前时间，传入
        var index = parseInt($('#time-periods').attr('index')); console.log(index)

        todaydate = getFormattedDate(); //获取当前日期  全局变量
        function getFormattedDate(date) { //获取当前日期
            var date=new Date();
            var year = date.getFullYear();
            var month = (1 + date.getMonth()).toString();
            month = month.length > 1 ? month : '0' + month;
            var day = (date.getDate()).toString();
            day = day.length > 1 ? day : '0' + day;
            return year + month  + day;
        }

        /**
         * 格式化数据
         * @author mao
         * @version 1.0
         * @date    2016-04-01
         */
        function testFormat(data) {
            return data > 9 ? data: '0'+data;
        }

        //日历初始化
        var transEndEventNames = {
                'WebkitTransition' : 'webkitTransitionEnd',
                'MozTransition' : 'transitionend',
                'OTransition' : 'oTransitionEnd',
                'msTransition' : 'MSTransitionEnd',
                'transition' : 'transitionend'
            },
            transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
            $wrapper = $( '#custom-inner' ),
            $calendar = $( '#calendar' ),
            cal = $calendar.calendario({
                onDayClick : function( $el, data, dateProperties ) {

                    if($el.hasClass('fc-content') || $el.css('cursor') == 'default') {
                        $el.removeClass('fc-content');

                        //删除数据
                        var str = testFormat(dateProperties.month)+'-'+testFormat(dateProperties.day) +'-' +dateProperties.year;
                        delete codropsEvents[str];

                        //删除时间段输入框
                        $('.'+str).remove();
                        index -= 1;

                    } else {
                        $el.addClass('fc-content');

                        //添加数据
                        var str = testFormat(dateProperties.month)+'-'+testFormat(dateProperties.day) +'-' +dateProperties.year;
                        codropsEvents[str] = ' ';

                        //计数加一
                        index += 1;
                        
                        //新增时间段输入框
                        var html = '<tr class="'+str+'" count="'+index+'">'+
                                    '<td>时间段'+index+'</td>'+
                                    '<td>开始时间</td>'+
                                    '<td style="width:181px;"><input type="text" class="form-control"/></td>'+
                                    '<td>到</td>'+
                                    '<td style="width:181px;"><input type="text" class="form-control"/></td>'+
                                   '</tr>';
                        $('#time-periods tbody').append(html);
                    }

                },
                caldata : codropsEvents,
                displayWeekAbbr : true,
                events: 'click'
            }),
            $month = $( '#custom-month' ).html( cal.getMonthName() ),
            $year = $( '#custom-year' ).html( cal.getYear() );

        //上一月，下一月
        $( '#custom-next' ).on( 'click', function() {
            //重载数据
            cal.setData(codropsEvents);
            cal.gotoNextMonth( updateMonthYear );
        } );
        $( '#custom-prev' ).on( 'click', function() {
            //重载数据
            cal.setData(codropsEvents);
            cal.gotoPreviousMonth( updateMonthYear );
        } );

        function updateMonthYear() {                
            $month.html( cal.getMonthName() );
            $year.html( cal.getYear() );
        }

        
    
    });
</script>
@stop{{-- 内容主体区域 --}}


