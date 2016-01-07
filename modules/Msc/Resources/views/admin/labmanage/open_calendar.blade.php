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
        .clndr-table .check{ background-color: #408aff; color: #fff;}
    </style>
@stop
@section('only_js')
    <script src="{{ asset('/msc/admin/js/calendar2/data.js') }}"></script>
    <script src= "{{ asset('/msc/admin/js/calendar2/jquery.calendario.js') }}"></script>
    <script src="{{ asset('/msc/admin/js/calendar2/modernizr.custom.js') }}"></script>
    <script>
        $(function(){
            $(document).ajaxSuccess(function(event, request, settings) {
                //楼栋选项卡切换
                ban();
            });
//            新增、编辑切换
            $("#add_device").click(function(){
                $("#add_device_form").show();
                $("#edit_form").hide();
            });
            $("#edit").click(function(){
                $("#add_device_form").hide();
                $("#edit_form").show();
            });
//            楼栋选项卡切换
            function ban(){
                $(".list-group-parent").click(function(){
                    $(this).toggleClass("checked").next(".lab_num").toggle("200");
                    $(this).children(".fa").toggleClass("deg");
                    if($(this).parent().next(".list-group").length=="1"){
                        $(this).next(".lab_num").children().last().addClass("border-bottom");
                    }

                });
                $(".list-group-child").click(function(){
                    $(".list-group-parent").removeClass("checked");
                    $(".list-group-child").removeClass("checked");
                    $(this).addClass("checked");
                });
            }            // may_add
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
            function deletetime(){ //删除时间段
                $(".fa-trash-o").click(function(){
                    $(this).parent().parent().remove();
                })
            }
            //选项框点击事件
            $(".check_label").click(function(){
                if($(this).children(".check_icon").hasClass("check")){
                    $(this).children(".check_icon").removeClass("check");
                }else{
                    $(this).children(".check_icon").addClass("check");

                }
            });

            //楼栋实验室数据绑定
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
                        console.log(result);
                        $(result).each(function(){
                            $treeview.append( "<div class='list-group' style='margin-bottom: 0' id='"+this.floor+"'>" +
                                    "<div class='list-group-item list-group-parent'>"
                                    +this.floor+"楼"
                                    +"</div>"
                                    +"<div class='lab_num'></div>"
                                    +"</div>"
                            );
                            if(this.lab!=""){
                                $(this.lab).each(function(){
                                    $(".treeview #"+ this.floor +" .lab_num").append("<div class='list-group-item list-group-child labdetail' data='"+this.total+"' data-labid='"+this.id+"'>"+this.name+"</div>")
                                });
                                $(".treeview #"+ this.floor +" .list-group-parent").append("<i class='fa fa-angle-right right'></i>");
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

            });

        })


    </script>
    <script type="text/javascript">
        $(function() {
            var savedate=[];
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
                    cal = $calendar.calendario( {
                        onDayClick : function( $el, $contentEl, dateProperties,savedate) {

                            $(this).addClass("check");
                            if(dateProperties.month<10){
                                dateProperties.month="0"+dateProperties.month;
                            }
                            if(dateProperties.day<10){
                                dateProperties.day="0"+dateProperties.day;
                            }
                            var savedate_one=dateProperties.year+"-"+dateProperties.month+"-"+dateProperties.day;
                            //var savedate=$("#savedate").val(savedate_one);

                            var make = false;
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
                            $('#savedate').append('<input type="hidden" name="savedate[]" value="'+savedate_one+'">');


                            if( $contentEl.length > 0 ) {
                                showEvents( $contentEl, dateProperties );
                            }
                        },
                        caldata : codropsEvents,
                        displayWeekAbbr : true
                    } ),
                    $month = $( '#custom-month' ).html( cal.getMonthName() ),
                    $year = $( '#custom-year' ).html( cal.getYear() );

            $( '#custom-next' ).on( 'click', function() {
                cal.gotoNextMonth( updateMonthYear );
            } );
            $( '#custom-prev' ).on( 'click', function() {
                cal.gotoPreviousMonth( updateMonthYear );
            } );

            function updateMonthYear() {
                $month.html( cal.getMonthName() );
                $year.html( cal.getYear() );
            }

            // just an example..
            function showEvents( $contentEl, dateProperties ) {
                console.log( dateProperties );

                hideEvents();

                var $events = $( '<div id="custom-content-reveal" class="custom-content-reveal"><h4>Events for ' + dateProperties.monthname + ' ' + dateProperties.day + ', ' + dateProperties.year + '</h4></div>' ),
                        $close = $( '<span class="custom-content-close"></span>' ).on( 'click', hideEvents );

                $events.append( $contentEl.html() , $close ).insertAfter( $wrapper );

                setTimeout( function() {
                    $events.css( 'top', '0%' );
                }, 25 );

            }
            function hideEvents() {

                var $events = $( '#custom-content-reveal' );
                if( $events.length > 0 ) {

                    $events.css( 'top', '100%' );
                    Modernizr.csstransitions ? $events.on( transEndEventName, function() { $( this ).remove(); } ) : $events.remove();

                }

            }

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
        <div class="col-sm-7">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <div class="left">
                        <span class="left">已选实验室：</span>
                        <h5 class="left">临床技能室（3-13）</h5>
                    </div>
                    <div class="left" style="margin-left: 20px">
                        <span class="left">容量：</span>
                        <h5 class="left">30人</h5>
                    </div>

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
                    <div class="add_time_list overflow">
                        {{--<input type="hidden" value="" id="savedate"/>--}}
                        <div id="savedate">

                        </div>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline"></div>
                                <input type="hidden" name="" value="">
                            </label>上午
                        </div>
                        <div class="col-sm-10">
                            <div class="overflow form-group">
                                <div class="col-sm-8">
                                    <input type="text"  class="form-control time-set" name="time-begein1" placeholder="08：00" value="" />
                                    <lable>至</lable>
                                    <input type="text"  class="form-control time-set" name="time-end1"   placeholder="09：00" value="" />
                                </div>
                                <div class="col-sm-4">
                                    <a class="add_time_button" id="morning">添加时间段<span></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline"></div>
                                <input type="hidden" name="" value="">
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
                                    <a class="add_time_button" id="noon">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline"></div>
                                <input type="hidden" name="" value="">
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
                                    <a class="add_time_button" id="afternoon">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="add_time_list overflow">
                        <input type="hidden" value="存储日期"/>
                        <div class="col-sm-2">
                            <label class="check_label checkbox_input">
                                <div class="check_real check_icon display_inline"></div>
                                <input type="hidden" name="" value="">
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
                                    <a class="add_time_button" id="night">添加时间段</a>
                                </div>
                            </div>

                        </div>
                    </div>
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