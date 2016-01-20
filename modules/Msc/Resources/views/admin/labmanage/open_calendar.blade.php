@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{ asset('/msc/admin/css/calendar/calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('/msc/admin/css/calendar/custom_2.css') }}">

    <style>
        /*may-add*/
        .add_time_list .time-set{ width: 45%; float: left;line-height: 34px;}
        .add_time_list .fa-trash-o{font-size:24px;line-height: 34px;}
        .add_time_list lable{ width: 10%; float: left; text-align: center;line-height: 34px;}
        .add_time_list .add_time_button{line-height: 34px;}
        #calendar .check{ background-color: #408AFF; color: #fff!important;}
        #calendar .check span{  color: #fff!important;}
        .fc-calendar .fc-row > div.check:after{color: #fff}
    </style>
@stop
@section('only_js')
    <script src= "{{ asset('/msc/admin/js/calendar2/jquery.calendario.js') }}"></script>
    <script src="{{ asset('/msc/admin/js/calendar2/modernizr.custom.js') }}"></script>
    <script>
        $(function(){
            $(document).ajaxSuccess(function(event, request, settings) {
                //楼栋选项卡切换
                ban();

            });
//            楼栋选项卡切换
            function ban(){
                $(".list-group-parent").unbind().click(function(){
                    $(this).addClass("checked").parent(".list-group").siblings().children(".list-group-parent").removeClass("checked");
                    if($(this).next(".lab_num").children(".list-group-child").size()!="0"){
                        $(this).next(".lab_num").slideToggle("200");
                        $(this).children(".fa").toggleClass("deg");
                    }
                    if($(this).parent().next(".list-group").length=="1"){
                        $(this).next(".lab_num").children().last().addClass("border-bottom");
                    }
                });
                $(".list-group-child").unbind().click(function(){
                    $(".list-group-parent").removeClass("checked");
                    $(".list-group-child").removeClass("checked");
                    $(this).addClass("checked");
                    var labid=$(this).attr("data-labid");
                    getEvent(labid);
                });
            }
 //          楼栋实验室数据绑定
            $("#ban_select").change(function(){
                var $treeview=$(".treeview");
                $treeview.empty();
                var $thisId=$(this).val();
                var url="/msc/admin/laboratory/floor-lab?lid="+$thisId;

                $.ajax({
                    type:"get",
                    url:url,
                    cache:false,
                    success:function(result){
                        $(result).each(function(){
                            //alert(this.floor);

                            if(this.lab!=""){

                                $(".treeview #"+ this.floor +" .list-group-parent").append("<i class='fa fa-angle-right right'></i>");
                                    $treeview.append("<div class='list-group' style='margin-bottom: 0' id='" + this.floor + "'>" +
                                            "<div class='list-group-item list-group-parent'>"
                                            + this.floor + "楼"
                                            + "</div>"
                                            + "<div class='lab_num'></div>"
                                            + "</div>"
                                    );
                                $(this.lab).each(function(){
                                    $(".treeview #"+ this.floor +" .lab_num").append("<div class='list-group-item list-group-child labdetail' data='"+this.total+"' data-labid='"+this.id+"'>"+this.name+"</div>")
                                });
                            }


                        })

                    }
                })
            });
            $('.treeview').delegate('.labdetail','click',function(){
                var total = $(this).attr('data');
                if(total == 'null'){
                    total = 0;
                }
                var labname = $(this).html();
                $('.labname').html(labname);
                $('.labtotal').html(total+'人');
                $('.labid').val('');
                $('.labid').val($(this).attr('data-labid'));
            });
// may_add
            function addtime(){
                $(".add_time_button").click(function(){ //添加时间段
                    var inuput_num=$(".add_time_list  .form-group").size()+1;
                    $(this).parent().parent().parent().append('<div class=" overflow form-group">'
                            +'<div class="col-sm-8">'
                            +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'"placeholder="08：00" value="" />'
                            +'<lable>至</lable>'
                            +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'"placeholder="09：00" value="" />'
                            +'</div>'
                            +'<div class="col-sm-4">'
                            +'<span class="fa fa-trash-o"></span>'
                            +'</div>'
                            +'</div>')
                    deletetime();
                })
            }
            function deletetime(){ //删除时间段
                $(".fa-trash-o").click(function(){
                    $(this).parent().parent().remove();
                })
            }
            //选项框点击事件
            $(".check_label").click(function(){
                $(this).children(".check_icon").toggleClass("check");
            });
//      获取选择的楼栋事件
        function getEvent(labid){
            var qj={id:labid}
            $.ajax({
                url:"{{ route('msc.admin.laboratory.getEditLabCleander')}}", /*${ctx}/*/
                type:"get",
                dataType:"json",
                contentType : 'application/json',
                cache:false,
                data:qj,
                success: function(result) {
                    var eventime;
                    event="";
                    var str = '{';
                    $(result.data).each(function(index, item){

                        if(this.date.day<10){
                            this.date.day="0"+this.date.day;
                        }
                        if(this.date.month<10){
                            this.date.month="0"+this.date.month;
                        }
                        eventime= this.date.month+"-"+this.date.day+"-"+this.date.year;

                        $(this.child).each(function(){
                            event=event+"<div class='timesave' data-id='"+this.id+"' begintime='" +this.begintime
                                            +"' endtime='"+this.endtime
                                    +"' period_type='"+this.period_type+ "'></div>";

                        });
                        str += '"'+eventime+'": "'+event+'",';
                        var obj = eval('('+'{"'+eventime+'": "'+event+'"}'+')');
                        event="";//清空event里面内容

                    })
                    str += '}';
                    codropsEvents = eval('('+str+')');
                    load();
                    $('#savedate').empty();
                }
            })
        }


            function load(){

                var transEndEventNames = {
                            'WebkitTransition' : 'webkitTransitionEnd',
                            'MozTransition' : 'transitionend',
                            'OTransition' : 'oTransitionEnd',
                            'msTransition' : 'MSTransitionEnd',
                            'transition' : 'transitionend'
                        },
                        transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
                        $wrapper = $( '#custom-inner' ),
                        $calendar = $( '#calendar' );
                        var event=false;
                        cal = $calendar.calendario( {
                            onDayClick : function( $el, $contentEl,dateProperties) {
                                if(dateProperties.month<10){
                                    dateProperties.month="0"+dateProperties.month;
                                }
                                if(dateProperties.day<10){
                                    dateProperties.day="0"+dateProperties.day;
                                }
                                var savedate_one=dateProperties.year+"-"+dateProperties.month+"-"+dateProperties.day;

                                if( $contentEl.length > 0 ) {//判断是否是有开放事件
                                    var dateDocArr1 = $('#savedate').find('input');
                                    if(dateDocArr1.length>0){
                                        dateDocArr1.each(function(){
                                            if(savedate_one == $(this).val()){//第二次点击取消操作
                                                $(this).remove();
                                            }else{//未发现当前点击日期
                                                $('#savedate').empty();
                                                $('#savedate').append('<input type="hidden" name="savedate[]" class="dataarr" value="'+savedate_one+'">');
                                            }
                                        })
                                    }else{
                                        $('#savedate').append('<input type="hidden" name="savedate[]" class="dataarr" value="'+savedate_one+'">');
                                    }

                                    event=true;
                               }else{
                                    var make = false;
                                    if(event){//判定之前是否点击过有开放事件的日期
                                        $('#savedate').empty();
                                        event=false;
                                    }
                                    var dateDocArr = $('#savedate').find('input');
                                    if(dateDocArr.length>0){
                                        dateDocArr.each(function(){
                                            if(savedate_one == $(this).val()){
                                                $(this).remove();
                                                make = true;
                                                return false;
                                            }
                                        })
                                    }
                                    if(make){
                                        return false;
                                    }
                                    $('#savedate').append('<input type="hidden" name="savedate[]" class="dataarr" value="'+savedate_one+'">');

                                }


                                //清空时间段

                            },
                            caldata : codropsEvents,
                            displayWeekAbbr : true
                        } ),
                        $month = $( '#custom-month' ).html( cal.getMonthName() ),
                        $year = $( '#custom-year' ).html( cal.getYear() );

                $( '#custom-next' ).on( 'click', function() {
                    cal.gotoNextMonth( updateMonthYear );
                    calendar_lick();
                } );
                $( '#custom-prev' ).on( 'click', function() {
                    cal.gotoPreviousMonth( updateMonthYear );
                    calendar_lick();
                } );
                calendar_lick();
                function calendar_lick(){
                    $("#calendar .fc-body .fc-row div").unbind().click(function(){

                        if($(this).children("div").size() > 0){
                            $(this).toggleClass("check");
                            cleardate();//清理掉下面部分的日期显
                            $(this).siblings().removeClass("check");//去掉已选择状态
                            $(this).parents().siblings(".fc-row").children().removeClass("check");

                            var mark_morning = false;
                            var mark_noon = false;
                            var  mark_afternoon = false;
                            var  mark_night = false;
                            var  inuput_num= 5;//初始设定添加的为第五个

                            $(this).children("div").find(".timesave").each(function(){
                                var period_type= $(this).attr("period_type");
                                var begintime= $(this).attr("begintime");
                                var endtime= $(this).attr("endtime");

                                if(period_type=="1"){//上午
                                    $(".morning").children().children().children(".check_icon").addClass("check");
                                    if(!mark_morning){
                                        $(".morning").children().children(".overflow").children(".col-sm-8").children("input[name='time-begein1']").val(begintime) ;
                                        $(".morning").children().children(".overflow").children(".col-sm-8").children("input[name='time-end1']").val(endtime);
                                        mark_morning = true;
                                    }else{
                                        $(".morning").children(".col-sm-10").append('<div class=" overflow form-group">'
                                                +'<div class="col-sm-8">'
                                                +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" value="'+begintime+'"placeholder="08：00"/>'
                                                +'<lable>至</lable>'
                                                +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" value="'+endtime+'"  placeholder="09：00"/>'
                                                +'</div>'
                                                +'<div class="col-sm-4">'
                                                +'<span class="fa fa-trash-o"></span>'
                                                +'</div>'
                                                +'</div>');
                                        inuput_num++;
                                    }

                                }
                                else if(period_type=="2"){
                                    //中午

                                    $(".noon").children().children().children().addClass("check");
                                    if(!mark_noon){
                                        $(".noon").children().children(".overflow").children(".col-sm-8").children("input[name='time-begein2']").val(begintime) ;
                                        $(".noon").children().children(".overflow").children(".col-sm-8").children("input[name='time-end2']").val(endtime);
                                        mark_noon = true;
                                    }else{
                                        $(".noon").children(".col-sm-10").append('<div class=" overflow form-group">'
                                                +'<div class="col-sm-8">'
                                                +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" value="'+begintime+'"placeholder="08：00"/>'
                                                +'<lable>至</lable>'
                                                +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" value="'+endtime+'"  placeholder="09：00"/>'
                                                +'</div>'
                                                +'<div class="col-sm-4">'
                                                +'<span class="fa fa-trash-o"></span>'
                                                +'</div>'
                                                +'</div>');
                                        inuput_num++;
                                    }

                                }
                                else  if(period_type=="3"){

                                    //下午
                                    $(".afternoon").children().children().children().addClass("check");
                                    if(!mark_afternoon){
                                        $(".afternoon").children().children(".overflow").children(".col-sm-8").children("input[name='time-begein3']").val(begintime) ;
                                        $(".afternoon").children().children(".overflow").children(".col-sm-8").children("input[name='time-end3']").val(endtime);
                                        mark_afternoon = true;
                                    }else{
                                        $(".afternoon").children(".col-sm-10").append('<div class=" overflow form-group">'
                                                +'<div class="col-sm-8">'
                                                +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" value="'+begintime+'"placeholder="08：00"/>'
                                                +'<lable>至</lable>'
                                                +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" value="'+endtime+'"  placeholder="09：00"/>'
                                                +'</div>'
                                                +'<div class="col-sm-4">'
                                                +'<span class="fa fa-trash-o"></span>'
                                                +'</div>'
                                                +'</div>');
                                        inuput_num++;
                                    }

                                }else{
                                    //晚上
                                    $(".night").children().children().children().addClass("check");
                                    if(!mark_night){
                                        $(".night").children().children(".overflow").children(".col-sm-8").children("input[name='time-begein4']").val(begintime) ;
                                        $(".night").children().children(".overflow").children(".col-sm-8").children("input[name='time-end4']").val(endtime);
                                        mark_night = true;
                                    }else{
                                        $(".night").children(".col-sm-10").append('<div class=" overflow form-group">'
                                                +'<div class="col-sm-8">'
                                                +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" value="'+begintime+'"placeholder="08：00"/>'
                                                +'<lable>至</lable>'
                                                +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" value="'+endtime+'"  placeholder="09：00"/>'
                                                +'</div>'
                                                +'<div class="col-sm-4">'
                                                +'<span class="fa fa-trash-o"></span>'
                                                +'</div>'
                                                +'</div>');
                                        inuput_num++;
                                    }
                                }

                            })

                            deletetime();
                            $(".add_time_button").click(function(){ //添加时间段
                                var inuput_num=$(".add_time_list  .form-group").size()+1;
                                var time_frame=$(this).attr("id");
                                $(this).parent().parent().parent().append('<div class=" overflow form-group">'
                                        +'<div class="col-sm-8">'
                                        +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" frame="'+time_frame+'"placeholder="08：00" value="" />'
                                        +'<lable>至</lable>'
                                        +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" frame="'+time_frame+'"   placeholder="09：00" value="" />'
                                        +'</div>'
                                        +'<div class="col-sm-4">'
                                        +'<span class="fa fa-trash-o"></span>'
                                        +'</div>'
                                        +'</div>')
                                deletetime();
                            })
                            function  deletetime(){
                                $(".fa-trash-o").click(function(){
                                    $(this).parent().parent().remove();
                                });
                            }

                        }else{
                           if($(this).children("span").size()!=0){
                               $(this).toggleClass("check");
                               cleardate();//清理掉下面部分的日期显
                               $(this).siblings(".fc-content").removeClass("check");//去掉已选择状态
                               $(this).parents().siblings(".fc-row").children(".fc-content").removeClass("check");
                               addtime()
                               $(".morning").children().children().children().removeClass("check");
                               $(".noon").children().children().children().removeClass("check");
                               $(".afternoon").children().children().children().removeClass("check");
                               $(".night").children().children().children().removeClass("check");
                           }
                        }
                    })
                }

                function cleardate(){
                    $(".add_time_list").children(".col-sm-10").empty();
                    $(".add_time_list").children(".col-sm-10").each(function(){
                        var inuput_num=$(".add_time_list  .form-group").size()+1;
                        $(this).append('<div class=" overflow form-group">'
                                +'<div class="col-sm-8">'
                                +'<input type="text"  class="form-control time-set" name="time-begein'+inuput_num+'" value="" placeholder="08：00"/>'
                                +'<lable>至</lable>'
                                +'<input type="text"  class="form-control time-set" name="time-end'+inuput_num+'" value=""  placeholder="09：00"/>'
                                +'</div>'
                                +'<div class="col-sm-4">'
                                +'<a class="add_time_button" id="morning">添加时间段<span></span></a>'
                                +'</div>'
                                +'</div>');

                    });
                    $(".morning").children().children().children().removeClass("check");
                    $(".noon").children().children().children().removeClass("check");
                    $(".afternoon").children().children().children().removeClass("check");
                    $(".night").children().children().children().removeClass("check");
                }

                function updateMonthYear() {
                    $month.html( cal.getMonthName() );
                    $year.html( cal.getYear() );
                }

            }

            //保存提交日历设置
            $('#edit_save').click(function(){
                var check = true;
                var datestr = '';
                var timestr = '';
                var start = '';
                var end = '';
                var timeArr = '';
                var type = '';
                var str = '';
                var name = '';
                var timeBrr = '';
                if(!$('.dataarr').val()){
                    layer.msg("请选择日期", {icon: 2,time: 1000});
                    check = false;
                    return false;
                }

                if(!$('.check_real').hasClass('check')){
                    layer.msg("未勾选时间段", {icon: 2,time: 1000});
                    check = false;
                    return false;
                }
                $('.dataarr').each(function(){
                    datestr += $(this).val()+"&";
                });

                $('.check_real').each(function(k){
                    if($(this).hasClass('check')){
                        var name = '';
                        type = $(this).attr('data');
                        timeArr['type'] = type;
                        var obj = $(this).parent().parent().siblings().find('input');
                        $.each(obj,function(n,value) {
                            if($(this).val()){
                                timeArr += type+'*'+$(this).val()+'!';
                            }else{
                                if(type == 'morning'){
                                    name = '上午';
                                }else if(type == 'noon'){
                                    name = '中午';
                                }else if(type == 'afternoon'){
                                    name = '下午';
                                }else if(type == 'night'){
                                    name = '晚上';
                                }else{
                                    name='';
                                }

                            }

                        });
                        if(name){
                            layer.msg(name+'的时间段未填写完整', {icon: 2,time: 1000});
                            check = false;
                            return false;
                        }
                        timeBrr = timeArr;
                        timeArr = '';

                        $('.'+type).val(timeBrr);
                    }

                });

                $('.check_real').each(function(){
                    if($(this).hasClass('check')){

                        timestr += $('.'+$(this).attr('data')).val()+'@';
                    }
                });
                if(!$('.labid').val()){
                    layer.msg("请选择实验室", {icon: 2,time: 1000});
                    check = false;
                    return false;
                }
                var dateid = {};
                $('.fc-row .check').children('div').children().each(function(i){
                            dateid[i] = $(this).attr('data-id');
                });
                if(dateid.length <= 0){
                    layer.msg('请选择日期', {icon: 2,time: 2000});
                    check = false;
                    return false;
                }
                if(check) {
                    $.ajax({
                        type: "POST",
                        url: "{{route('msc.admin.laboratory.postOperatingLabCleander')}}",
                        data: {date: datestr, timestr: timestr, lid: $('.labid').val(), dateid: dateid},
                        success: function (msg) {
                            if (msg.status) {

                                layer.msg(msg.info, {icon: 1, time: 1000});
                                getEvent($('.labid').val());
                                load();
                                $('#savedate').empty();

                            } else {
                                layer.msg(msg.info, {icon: 2, time: 1000});
                                return false;
                            }
                        }
                    });
                }
            });
        });

    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="col-sm-5">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <select name="" id="ban_select" class="select">
                        <option value="-1">请选择楼栋</option>
                        @if(!empty($location))
                            @foreach($location as $k=>$v)

                                <option value="{{@$v->id}}">{{@$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="ibox-content">
                    <div class="treeview">

                    </div>
                </div>
            </div>
        </div>
        <div id="savedate">

        </div>
        <div class="col-sm-7">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <div class="left">
                        <span class="left">已选实验室：</span>
                        <h5 class="left labname">临床技能室（3-13）</h5>
                    </div>
                    <div class="left" style="margin-left: 20px">
                        <span class="left">容量：</span>
                        <h5 class="left labtotal">30人</h5>
                    </div>
                    <input type="hidden" name="" class="labid" value="">
                </div>
                <div class="ibox-content">
                    <section class="main">
                        <div class="custom-calendar-wrap">
                            <div id="custom-inner" class="custom-inner">
                                <div class="custom-header clearfix">
                                    <nav>
                                        <span id="custom-prev" class="custom-prev">上月</span>
                                        <span id="custom-next" class="custom-next">下月</span>
                                    </nav>
                                    <h2 id="custom-month" class="custom-month"></h2>
                                    <h3 id="custom-year" class="custom-year"></h3>
                                </div>
                                <div id="calendar" class="fc-calendar-container"></div>
                            </div>
                        </div>
                    </section>
                    <div class="add_time_list overflow morning">
                        {{--<input type="hidden" value="" id="savedate"/>--}}

                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline" data="morning"></div>
                            </label>
                            <span> 上午</span>
                        </div>
                        <div class="col-sm-10">
                            <div class="overflow form-group input ">
                                <div class="col-sm-8">
                                    <input type="text"  class="form-control time-set" name="time-begein1" placeholder="08：00" value="" />
                                    <lable>至</lable>
                                    <input type="text"  class="form-control time-set" name="time-end1"   placeholder="09：00" value="" />
                                </div>
                                <div class="col-sm-4">
                                    <a class="add_time_button">添加时间段<span></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow noon">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline" data="noon"></div>
                            </label> 中午
                        </div>
                        <div class="col-sm-10">
                            <div class="overflow form-group">
                                <div class="col-sm-8">
                                    <input type="text"  class="form-control time-set" name="time-begein2" placeholder="08：00" value="" />
                                    <lable>至</lable>
                                    <input type="text"  class="form-control time-set" name="time-end2"   placeholder="09：00" value="" />
                                </div>
                                <div class="col-sm-4">
                                    <a class="add_time_button">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow afternoon">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline" data="afternoon"></div>
                            </label>下午
                        </div>
                        <div class="col-sm-10">
                            <div class="overflow form-group">
                                <div class="col-sm-8">
                                    <input type="text"  class="form-control time-set" name="time-begein3" placeholder="08：00" value="" />
                                    <lable>至</lable>
                                    <input type="text"  class="form-control time-set" name="time-end3"   placeholder="09：00" value="" />
                                </div>
                                <div class="col-sm-4">
                                    <a class="add_time_button">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow night">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline"  data="night"></div>
                            </label> 晚上
                        </div>
                        <div class="col-sm-10">
                            <div class="overflow form-group">
                                <div class="col-sm-8">
                                    <input type="text"  class="form-control time-set" name="time-begein4" placeholder="08：00" value="" />
                                    <lable>至</lable>
                                    <input type="text"  class="form-control time-set" name="time-end4"   placeholder="09：00" value="" />
                                </div>
                                <div class="col-sm-4">
                                    <a class="add_time_button">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="" class="morning">
                    <input type="hidden" name="" class="noon">
                    <input type="hidden" name="" class="afternoon">
                    <input type="hidden" name="" class="night">
                    <div class="hr-line-dashed"></div>

                    <div class="form-group overflow">
                        <div class=" right">
                            <button class="btn btn-primary"  type="button" id="edit_save" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                            &nbsp;&nbsp;&nbsp;
                            <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop

@section('layer_content')

@stop