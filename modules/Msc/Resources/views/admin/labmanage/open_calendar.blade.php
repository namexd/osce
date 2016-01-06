@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{ url('/msc/admin/css/calendar/clndr.css') }}">
    <style>
        /*may-add*/
        .add_time_list .time-set{ width: 45%; float: left;line-height: 34px;}
        .add_time_list .fa-trash-o{font-size:24px;line-height: 34px;}
        .add_time_list lable{ width: 10%; float: left; text-align: center;line-height: 34px;}
        .add_time_list .add_time_button{line-height: 34px;}
    </style>
@stop
@section('only_js')
    <script src="{{ url('/msc/admin/js/calendar/underscore-min.js') }}"></script>
    <script src= "{{ url('/msc/admin/js/calendar/moment-2.8.3.js') }}"></script>
    <script src="{{ url('/msc/admin/js/calendar/clndr.min.js') }}"></script>
    <script src= "{{ url('/msc/admin/js/calendar/site.js') }}"></script>

    <script>
        $(function(){
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
            // may_add
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
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="col-sm-5">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <select name="" id="" class="select">
                        <option value="-1">请选择楼栋</option>
                        <option value="11">11</option>
                        <option value="22">22</option>
                    </select>
                </div>
                <div class="ibox-content">
                    <div class="treeview">
                        <div class="list-group" style="margin-bottom: 0;">
                            <div class="list-group-item list-group-parent">
                                -1楼
                                <i class="fa fa-angle-right right"></i>
                            </div>
                            <div class="lab_num">
                                <div class="list-group-item list-group-child">临床1教</div>
                                <div class="list-group-item list-group-child">临床2教</div>
                            </div>
                        </div>
                        <div class="list-group" style="margin-bottom: 0;">
                            <div class="list-group-item list-group-parent">
                                -1楼
                                <i class="fa fa-angle-right right"></i>
                            </div>
                            <div class="lab_num">
                                <div class="list-group-item list-group-child">临床1教</div>
                                <div class="list-group-item list-group-child">临床2教</div>
                            </div>
                        </div>
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
                    <input type="button" class="btn btn_pl btn-success right" data-toggle="modal" data-target="#myModal" value="添加设备" id="add_device">
                </div>
                <div class="ibox-content">
                    <div class="cal1">
                    </div>
                    <div class="add_time_list overflow">
                        <input type="hidden" value="存储日期"/>
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