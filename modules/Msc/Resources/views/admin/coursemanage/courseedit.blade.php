@extends('msc::admin.layouts.admin')
@section('only_css')
    <style type="text/css">
        .cancel{
            background-color: #fff;
            border: 1px solid #ccc;
        }
        .layui-layer-btn{
            background: #fff!important;
            border-top: none!important;
            text-align: center!important;
        }
        .layui-layer-btn a{
            padding: 3px 30px!important;
        }
    </style>
@stop

@section('only_js')
    <script>
        $(function(){
//           课程变更确定
            $("#change-Course").click(function(){
                var $begin=$("#time-se").find("option:selected").text().split(" ")[0]+" "+$("#time-se").find("option:selected").text().split(" ")[1].split("-")[0];
                var $end=$("#time-se").find("option:selected").text().split(" ")[0]+" "+$("#time-se").find("option:selected").text().split(" ")[1].split("-")[1];
                $("#begindate").val($begin);
                $("#enddate").val($end);
               layer.alert(
                  "是否进行课程信息变更？", {title:["课程变更","font-size:16px;color:#408aff"]},function(){
                            $("#course-form").submit();
                       }
               );
            });
            //ajax获取教室的空闲时间
           /* $.ajax({
                url:"{{url('/msc/admin/resources-manager/classroom-list')}}",
                type:"get",
                dataType:"json",
                data:{keywords:"测试"},
                success: function(result) {
                    console.log(result);
                    var clength=result.data.rows.length;
                    var str="";
                    for(var i=0;i<clength;i++){
                        str+='<option value="'+result.data.rows[i].id+'">'+result.data.rows[i].name+'</option>';
                    }
                    $("#croom-se").append(str);
                }
            });*/

//          改变教室下拉列表ajax异步加载空闲时间
            var $planId=$("#plan-id").val();//全局变量$planId
            $("#croom-se").change(function(){
                var $roomSelected=$("#croom-se").find("option:selected").val();
                var $roomIndex=$("#croom-se").find("option:selected").index();

                getClassTime($roomSelected,$planId,$roomIndex);
            })

            function getClassTime(r,p,index){
                $.ajax({
                    url:"{{url('/msc/admin/courses/classroom-time')}}",
                    type:"get",
                    dataType:"json",
                    data:{
                        id:r,
                        plan_id:p
                    },
                    success: function(result) {
                        var str="";
                         for(var i=0;i<result.data.rows.length;i++){
                             str+='<option value="">'+result.data.rows[i]+'</option>';
                         }
                        $("#time-se").find("option").remove();
                        if(index==0){
                            $("#time-se").append($current).append(str);
                        }else{
                            $("#time-se").append(str);
                        }

                    }
                });
            }
            //缓存页面加载时的空闲时间
            var $current=$("<option>"+$('#time-se option:eq(0)').text()+"</option>");//默认系统时间
            var $firstCroom=$("#croom-se option:eq(0)").val();//默认教室
            getClassTime($firstCroom,$planId,0);//页面加载时获取默认教室的空闲时间
        })
    </script>
@stop

@section('content')

    <div>
        <div class="ibox-title">
            <h5><a href="{{route('msc.courses.coursesEdit',['id'=>$data->id])}}">课程编辑</a></h5>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal" id="course-form" action="{{url('/msc/admin/courses/courses-edit')}}" method="post">
                <input type="hidden" name="id" value="{{$data->id}}" id="plan-id"/>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">课程名称</label>
                    <div class="col-sm-10">
                        <p class="form-control-static" >{{$data->course_id==0? '紧急约课':(is_null($data->course)? '-':$data->course->name)}}</p>
                        <input type="hidden" name="course_id" value="{{$data->course_id}}" />
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">教室</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="croom-se" name="resources_lab_id">
                            @forelse($classroomRelations as $classroomRelation)
                                <option value="{{$classroomRelation->classroom->id}}" {{ $classroomRelation->classroom->id==$data->classroomCourses->resources_lab_id? 'selected="selected"':'' }}>{{$classroomRelation->classroom->name}}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">时间</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="time-se">
                            <option value="">{{$data->currentdate}} {{$data->begintime}}-{{$data->endtime}}</option>
                        </select>
                        <input type="hidden" name="begindate" value="" id="begindate"/>
                        <input type="hidden" name="enddate" value="" id="enddate"/>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">小组</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($groups)==0? '-':implode(',',$groups)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">老师</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($teachers)==0? '-':implode(',',$teachers)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($mobiles)==0? '-':implode(',',$mobiles)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                @if($data->type==3)
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">使用原因</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">暂时找不到</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                @endif

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn cancel" type="button" value="取消">
                        <input class="btn btn-success" type="button" value="课程变更" id="change-Course">
                    </div>
                </div>
            </form>

        </div>
    </div>
@stop

