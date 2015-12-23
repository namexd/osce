@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('msc/common/css/bootstrapValidator.css')}}">
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>

<style rel="stylesheet">
    .select2-container--default .select2-selection--single{ height: 32px;
        border: 1px solid #ccc;}
    .select2-container--default .select2-selection--single .select2-selection__arrow{top:3px;}

</style>
@stop
@section('only_head_js')
    <script type="text/javascript" src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('msc/wechat/openlab/js/openlab_search_apply.js')}}"></script>
 <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
 <Script type="text/javascript">

     add_class_list= [];//记录选择学生
     add_group_list= [];
     $(document).ready(function(){
         $("#class_list").select2({});
         $("#group_list").select2({});
         $('#course_name').select2({});
         $(".radio_label").click(function(){
             if($(this).children("input").checked=="true"){
                 $(this).children(".radio_icon").removeClass("check");
             }else{
                 $(".radio_icon").removeClass("check");
                 $(this).children(".radio_icon").addClass("check");
             }
             if($(this).children("input").val()=="1"){//选择为班级组
                $("#add_class").show();
                 $("#add_group").hide();
                $('#frmTeacher').find('.gg').remove();
             }else{//选择为学生组
                 $("#add_group").show();
                 $("#add_class").hide();
                 $('#frmTeacher').find('.ss').remove();

             }
                get_list();//将左边弹出
         });
         /*mao 2015-11-26
          *表单验证 老师
          */
         $('#frmTeacher').bootstrapValidator({
             message: 'This value is not valid',
             feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                 valid: 'glyphicon glyphicon-ok',
                 invalid: 'glyphicon glyphicon-remove',
                 validating: 'glyphicon glyphicon-refresh'
             },
             fields: {/*验证*/
                 course_name: {/*键名username和input name值对应*/
                     message: 'The username is not valid',
                     validators: {
                         notEmpty: {/*非空提示*/
                             message: '请选择课程'
                         },
                     }
                 },
                 type1: {/*键名username和input name值对应*/
                     message: 'The username is not valid',
                     validators: {
                         notEmpty: {/*非空提示*/
                             message: '请选择班级或者学生组'
                         },
                     }
                 },


             }
         });

         
     })


     

 </Script>

@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    @if($apply_type == 1)
    紧急预约
    @else
        预约
    @endif

    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="w_90"><p class="font16 clo3 mart_5">请确认您的预约信息</p></div>
<div class="add_main mart_5">
    <div class="form-group">
        <label for="">教室名称</label>
        <div class="txt">
            {{@$ClassroomPlanInfo->get_lab->name}}
        </div>
    </div>
    <div class="form-group">
        <label for="">使用时段</label>
        <div class="txt">
            {{@$ClassroomPlanInfo['begintime']}}-{{$ClassroomPlanInfo['endtime']}}
        </div>
    </div>
    <div class="form-group">
        <label for="">预约人</label>
        <div class="txt">
            {{@$username}}
        </div>
    </div>
</div>


<div id="info_list">
    <div id="apply_info" class="w_90" style="">
        <form name="form"   id="frmTeacher" action="{{action('\Modules\Msc\Http\Controllers\WeChat\OpenLaboratoryController@postAddLab')}}" method="post" >
        @if($user_type == 1)
            <p class="mart_5">课程名称</p>
            <div class="course_name form-group ">
                <select class="select1" name="course_name"  id="course_name" placeholder="输入学生组名" style="width:100%;">
                    @if(!empty($Courses) && count($Courses)>0)
                        @foreach($Courses as $Course)
                        <option value="{{@$Course->id}}">{{@$Course->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <p class="mart_5">请选择上课学生</p>
            <div class="course_student_type form-group ">
                <div class="radio_box" ng-init="user.sex2 = 1">
                    <label class="left radio_label" for="radio_3">
                        <div class="left radio_icon"></div>
                        <span class="left">班级</span>
                        <input type="radio" id="type_class" name="type1" value="1"/>
                    </label>
                    <label class="left radio_label" for="radio_4" style="margin-left:50px">
                        <div class="left radio_icon"></div>
                        <span class="left">学生组</span>
                        <input type="radio" id="type_group" name="type1" value="2"/>
                    </label>
                </div>
            </div>
            
            <p class="">申请理由</p>
           <div class="Reason">
                <textarea id="Reason_detail" name="detail" type="" placeholder="请输入理由"/></textarea>
            </div>
           @endif
            <input type="hidden" name="p_id" value="{{@$pid}}">
            <input type="hidden" name="apply_date" value="{{@$apply_date}}">
            <input type="hidden" name="apply_type" value="{{@$apply_type}}">
            <input type="hidden" name="user_type" value="{{@$user_type}}">
            <input type="hidden" name="c_id" value="{{@$ClassroomPlanInfo->resources_lab_id}}">
            <input type="hidden"  name="timestamp" value="{{@$ClassroomPlanInfo['get_plan'][0]['currentdate']}} {{@$ClassroomPlanInfo['get_plan'][0]['begintime']}}~{{@$ClassroomPlanInfo['get_plan'][0]['currentdate']}} {{@$ClassroomPlanInfo['get_plan'][0]['endtime']}}">
            <input class="btn2 mart_10 marb_10" type="submit"  value="提交申请" />
            
        </form>
    </div><!-- 类别列表-->
    <div id="leibie_list">
        <div class="leibie_left">
        </div>
        <div class="leibie_box" >
            <p class="font16 check_tit">请添加上课学生</p>

            <div id="add_class" style="display: none">
                <div id="class_selected" class="marb_8">
                    <ul>

                    </ul>
                </div>
                <div class="w_96">
                    <select  name="class_list[]"  id="class_list" placeholder="输入班级名" style="width:100%;">
                        @if(!empty($studentClass) && count($studentClass)>0)
                            @foreach($studentClass as $class)>
                            <option value="{{@$class->id}}">{{@$class->name}}</option>
                            @endforeach
                        @endif

                    </select>
                </div>
            </div>
            <div id="add_group" style="display: none">
                <div id="group_selected" class="marb_8">
                    <ul>
                    </ul>
                </div>
                <select class="" name="student_group[]"  id="group_list" placeholder="输入学生组名" style="width:100%;">
                    @if(!empty($studentClass) && count($studentClass)>0)
                        @foreach($groups as $group)
                        <option value="{{@$group->id}}">{{@$group->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="w_96">
                <button id="comfirm_student" type="button" class="btn1">确定</button>
            </div>
        </div>
    </div>
        <!-- 类别列表-->
    <div id="leibie_list">
        <div class="leibie_left">
        </div>
        <div class="leibie_box" >
            <p class="font16 check_tit">请添加上课学生</p>

            <div id="add_class" style="display: none">
                <div id="class_selected" class="marb_8">
                    <ul>

                    </ul>
                </div>
                <div class="w_96">
                    <select  name="class_list[]"  id="class_list" placeholder="输入班级名" style="width:100%;">
                        @if(!empty($studentClass) && count($studentClass)>0)
                            @foreach($studentClass as $class)>
                            <option value="{{@$class->id}}">{{@$class->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div id="add_group" style="display: none">

                <div id="group_selected" class="marb_8">
                    <ul>
                    </ul>
                </div>
                <select class="" name="student_group[]"  id="group_list" placeholder="输入学生组名" style="width:100%;">
                    @if(!empty($groups) && count($groups)>0)
                        @foreach($groups as $group)
                        <option value="{{@$group->id}}">{{@$group->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="w_96">
                <button id="comfirm_student" type="button" class="btn1">确定</button>
            </div>

        </div>

    </div>

    <div class="wait mart_10" style="display: none;">
        <img src="{{asset('msc/wechat/common/img/waiting.png')}}" width="30%"/>
        <p>等待审核中</p>
    </div>
</div>






@stop