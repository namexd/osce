@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .list-group-item{cursor:pointer;}
        span.indent{margin-left:10px;margin-right:10px}
        span.icon{margin-right:5px}
        .node-treeview11{color:#428bca;}
        .node-treeview11:hover{background-color:#F5F5F5;}
        b{font-weight: normal;}
        .treeview .checked {background-color: #408aff; color: #fff;}
    </style>
@stop

@section('only_js')

    <script>
        $(function(){
            var url="{{ url('/msc/admin/dept/select-dept')}}";
            gethistory(url);
            addfater();

            $(document).ajaxSuccess(function(event, request, settings) {
                listclick();//dom之后添加事件
                toggle();//
                editall();//更改当前栏目内容
                deleteall();//删除科室
                addChild();//添加子科室

            });
        })

        function gethistory(url){
            $.ajax({
                url:url, /*${ctx}/*/
                type:"get",
                dataType:"json",
                contentType : 'application/json',
                cache:false,
                success: function(result) {

                    $(result.data.total).each(function(){

                        if(this.child!=""){
                            $(".treeview ul").append(
                                    '<li class="list-group-item parent" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'">'
                                    + '<input type="hidden" class="description" value=" '+this.description+'"/>'
                                    +'<span class="icon"><i class="glyphicon  glyphicon-plus"><input type="hidden" class="toggel" value="0"/></i></span>'
                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+this.name+'</b>'
                                    +'</li>'
                            );//第一层添加
                            $(this.child).each(function() {
                                if(this.child!=""){
                                    $(".treeview ul").append(
                                            '<li class="list-group-item children1" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'"style="display: none;">'
                                            + '<span class="indent"></span>'
                                            + '<input type="hidden" class="description" value=" '+this.description+'"/>'
                                            + '<span class="icon"><i class="glyphicon glyphicon-plus"></i></span>'
                                            + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                            +'<b>'+this.name+'</b>'
                                            + '</li>'
                                    );//第二层添加
                                    $(this.child).each(function() {
                                        $(".treeview ul").append(
                                                '<li class="list-group-item children2" id="'+this.id+'"pid="'+this.pid+'" level="'+ this.level+'"style="display: none;">'
                                                + '<span class="indent"></span>'
                                                + '<span class="indent"></span>'
                                                + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                                                + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                                +'<b>'+this.name+'</b>'
                                                + '</li>'
                                        );//第三层添加
                                    })
                                }else{
                                    $(".treeview ul").append(
                                            '<li class="list-group-item children1" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'"style="display: none;">'
                                            + '<span class="indent"></span>'
                                            + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                            +'<b>'+this.name+'</b>'
                                            +'</li>'
                                    );//第二层添加
                                }

                            })
                        }else{
                            $(".treeview ul").append(
                                    '<li class="list-group-item parent" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'">'
                                    + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+this.name+'</b>'
                                    +'</li>'
                            );//第一层添加
                        }
                    });
                }
            });
        }
        function   listclick(){//dom之后添加事件
            $(".list-group-item").unbind().click(function(){
                $("#submit").hide();
                $("#edit_save").show();
                var listId = $(this).attr("id");//获取点击时该栏目的ID
                var thispid=$(this).attr("pid");
                var level=$(this).attr("level");
                var thisneme=$(this).text();
                console.log(thispid);
                $("#hidden_this_id").val(listId);
                $(".add-name").val(thisneme);
                $(".add-describe").val($(this).children(".description").val());
                if(level=="1"){
                    $(".add-parent").val("");//上级科室
                }else if(level=="2"){
                    var parent_name=$(this).prevAll(".parent").first().text()
                    $(".add-parent").val(parent_name);//上级科室
                }else{
                    var parent_name=$(this).prevAll(".children1").first().text()
                    $(".add-parent").val(parent_name);//上级科室
                }
                $(this).addClass("checked").siblings().removeClass("checked");//表单切换
                addChild(listId,level,thisneme);//添加子科室功能
            });
        }
        function  toggle(){//dom之后添加事件
            $(".glyphicon").unbind().click(function(){
                var fatherid= $(this).parent().parent().attr("id");
                var fatherlevel= $(this).parent().parent().attr("level");

                if(fatherlevel=="1"){
                    $(this).toggleClass("glyphicon-minus");
                    $(this).toggleClass("glyphicon-plus");
                    if($(this).children(".toggel").val()=="0"){
                        $(this).children(".toggel").val("1");
                        $(this).parent().parent().siblings(".children1").each(function(){
                            if($(this).attr("pid")==fatherid){
                                $(this).show();
                            }
                        })
                        return false;
                    }else{

                        $(this).parent().parent().nextUntil(".parent").hide();
                        $(this).children(".toggel").val("0");
                        $(this).parent().parent().nextUntil(".parent").each(function(){
                            if($(this).attr("level")=="2"){
                                $(this).children(".icon").children(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
                            }
                        });
                        return false;
                    }

                }else if(fatherlevel=="2"){
                    $(this).toggleClass("glyphicon-minus");
                    $(this).toggleClass("glyphicon-plus");
                    $(this).parent().parent().siblings(".children2").each(function(){
                        if($(this).attr("pid")==fatherid){
                            $(this).toggle();
                        }
                    })
                }
            })
        }
        function  addChild(listId,level,thisneme){
            var name=$(".add-name").val();
            $("#new-add-child").unbind().click(function(){
                if(level>=3){
                    layer.msg("无法再添加子科室", {icon: 2,time: 1000});
                    return false;
                }
                $("#edit_save").hide();
                $("#submit").show();
                $(".add-name").val("");
                $(".add-describe").val("");
                $(".add-parent").val(thisneme);
                level++;
                addChildgroup(level,listId);

            })

        }
        function addChildgroup(level,listId){
            $("#submit").unbind().click(function(){
                var name=$(".add-name").val();
                validate (name);//验证科室名称
                if(mark){
                    return false;
                }
                var describe=$(".add-describe").val();
                var qj={name:name,pid:listId,level:level,description:describe}
                $.ajax({
                    url:"{{ route('msc.Dept.AddDept') }}", /*${ctx}/*/
                    type: "post",
                    dataType: "json",
                    cache: false,
                    data:qj,
                    success: function (result) {

                        if(result.data.total.level=="2"){
                            $("#"+listId).after(
                                    '<li class="list-group-item children1" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                                    + '<span class="indent"></span>'
                                    + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'

                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+result.data.total.name+'</b>'
                                    +'</li>'
                            )
                        }else if(result.data.total.level=="3"){
                            $("#"+listId).after(
                                    '<li class="list-group-item children2" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                                    + '<span class="indent"></span>'
                                    + '<span class="indent"></span>'
                                    + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'

                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+result.data.total.name+'</b>'
                                    +'</li>'
                            )
                        }
                        if($("#"+listId+" .glyphicon-plus").size()=="0"&&$("#"+listId+" .glyphicon-minus").size()=="0"){
                            $("#"+listId+" .description").before(
                                    '<span class="icon"><i class="glyphicon glyphicon-minus"><input type="hidden" class="toggel" value="1"/></i></span>'
                            );
                            $("#"+listId+" .toggel").val("1");
                        }else{
                            $("#"+listId).nextUntil(".parent").show();
                        }
                        toggle();
                        $("#"+result.data.total.id).addClass("checked").siblings().removeClass("checked");//表单切换
                    }
                })
            })
        }
        function editall(){
            $("#edit_save").click(function(){
                var thisid=$("#hidden_this_id").val();
                var name=$(".add-name").val();
                var describe=$(".add-describe").val();
                var qj={name:name,id:thisid,description:describe}
                $.ajax({
                    url:"{{ route('msc.Dept.UpdateDept') }}", /*${ctx}/*/
                    type: "post",
                    dataType: "json",
                    cache: false,
                    data:qj,
                    success: function (result) {
                        if(result.message=="更新成功"){
                            $("#"+thisid+" b").text(name);
                            $("#"+thisid).children(".description").val(describe);
                            layer.msg(result.message, {icon: 1,time: 1000});
                        } else{
                            layer.msg(result.message, {icon: 1,time: 1000});
                        }
                    }
                })
            })
        }
        function deleteall(){
            $("#delete").unbind().click(function(){
                var thisid=$("#hidden_this_id").val();
                var qj={id:thisid};
                layer.confirm('您确定要删除该科室？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.ajax({
                        url:"{{ route('msc.Dept.DelDept') }}", /*${ctx}/*/
                        type: "post",
                        dataType: "json",
                        cache: false,
                        data:qj,
                        success: function (result) {
                            if(result.message=="删除成功"){
                                $(result.data.rows).each(function(){
                                    $("#"+this).remove();
                                });
                                layer.msg(result.message, {icon: 1,time: 1000});
                            } else{
                                layer.msg(result.message, {icon: 1,time: 1000});
                            }
                        }
                    })
                });

            })
        }
        function addfater(){
            $("#new-add-father").unbind().click(function(){
                $("#edit_save").hide();
                $("#submit").show();
                $(".add-parent").val("");
                $(".add-name").val("");
                $(".add-describe").val("");

                $("#submit").unbind().click(function(){
                    var name=$(".add-name").val();
                    validate (name);//添加验证
                    if(mark){
                        alert(202);
                        return false;
                    }
                    var describe=$(".add-describe").val();
                    var qj={name:name,pid:"0",level:1,description:describe}

                    $.ajax({
                        url:"{{ route('msc.Dept.AddDept') }}", /*${ctx}/*/
                        type: "post",
                        dataType: "json",
                        cache: false,
                        data:qj,
                        success: function (result) {
                            if(result.message=="添加成功"){
                                $(".treeview ul").append(
                                        '<li class="list-group-item parent" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                                        + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'
                                        +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                        +'<b>'+result.data.total.name+'</b>'
                                        +'</li>'
                                );//第一层添加
                                layer.msg(result.message, {icon: 1,time: 1000});
                                $("#"+result.data.total.id).addClass("checked").siblings().removeClass("checked");//表单切换
                                var level=result.data.total.level;
                                var listId=result.data.total.id;
                                addChildgroup(level,listId);
                                deleteall();
                            } else{
                                layer.msg(result.message, {icon: 2,time: 1000});
                            }


                        }
                    })
                });

            })
        }
        //        添加科室验证
        function validate (name){
            mark = false;
            $(".list-group").find(".list-group-item").each(function(){
                if($(this).text()==name){
                    mark = true;
                    layer.msg("该科室已存在", {icon: 2,time: 1000});
                    return false;
                }
            })

        }

    </script>

@stop

@section('content')
    <input type="hidden" id="parameter" value="" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="col-sm-5">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <h5>科室列表</h5>
                    <input type="button" class="btn  btn_pl btn-success right"  id="new-add-father" value="新增科室">
                </div>
                <div class="ibox-content">
                    <div class="treeview">
                        <ul class="list-group">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="ibox">
                <div class="ibox-title overflow">
                    <h5>科室信息</h5>
                    <input type="button" class="btn btn_pl btn-success right"  id="new-add-child" value="新增子科室">
                    <button class="btn btn_pl btn-white right button_margin marr_15" id="delete">删除该科室</button>
                    <input type="hidden" value="" id="hidden_this_id"/>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="add_department">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>科室名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control name add-name" name="name" value="" placeholder="请输入科室名称" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>上级科室</label>
                            <div class="col-sm-9">
                                <input type="text" disabled  class="form-control name add-parent" name="up_name" value="" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">职称描述</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control describe add-describe" name="describe" placeholder="请输入职称描述" />
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class=" right">
                                <button class="btn btn-primary"  type="button" id="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;认</button>
                                <button class="btn btn-primary"  type="button" id="edit_save" style="display:none" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop

@section('layer_content')

@stop