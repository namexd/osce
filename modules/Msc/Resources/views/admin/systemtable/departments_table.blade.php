@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .list-group-item{cursor:pointer;}
        span.indent{margin-left:10px;margin-right:10px}
        span.icon{margin-right:5px}
        .node-treeview11{color:#428bca;}
        .node-treeview11:hover{background-color:#F5F5F5;}
    </style>
@stop

@section('only_js')

    <script>
        $(function(){
            var url="{{ url('/msc/admin/dept/select-dept') }}";
            gethistory(url);

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
                                    '<li class="list-group-item" listId="'+this.id+'">'
                                    +'<span class="icon"><i class="glyphicon glyphicon-plus glyphicon-plus"></i></span>'
                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +this.name
                                    +'</li>'
                            );//第一层添加
                            $(this.child).each(function() {

                                if(this.child!=""){
                                    $(".treeview ul").append(
                                            '<li class="list-group-item children1" listId="'+this.id+'"pid="'+this.pid+'"style="display: none;">'
                                            + '<span class="indent"></span>'
                                            + '<span class="icon"><i class="glyphicon  glyphicon-plus"></i></span>'
                                            + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                            + this.name
                                            + '</li>'
                                    );//第二层添加
                                    $(this.child).each(function() {
                                        $(".treeview ul").append(
                                                '<li class="list-group-item children1" listId="'+this.id+'"pid="'+this.pid+'"style="display: none;">'
                                                + '<span class="indent"></span>'
                                                + '<span class="indent"></span>'
                                                + '<span class="icon"><i class="glyphicon"></i></span>'
                                                + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                                + this.name
                                                + '</li>'
                                        );//第三层添加
                                    })
                                }else{
                                    $(".treeview ul").append(
                                            '<li class="list-group-item children1" listId="'+this.id+'"pid="'+this.pid+'"style="display: none;">'
                                            + '<span class="indent"></span>'
                                            +'<span class="icon"><i class="glyphicon"></i></span>'
                                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                            +this.name
                                            +'</li>'
                                    );//第二层添加
                                }

                            })
                        }else{
                            $(".treeview ul").append(
                                    '<li class="list-group-item" listId="'+this.id+'">'
                                    +'<span class="icon"><i class="glyphicon glyphicon-plus"></i></span>'
                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +this.name
                                    +'</li>'
                            );//第一层添加
                        }
                    });

                    getChild();//展开关闭功能
                },

            });
        }
        function   getChild(){
            $(".glyphicon-plus").click(function(){
                var thisid= $(this).parent().parent().attr("listId");//获取点击时的父ID
                $(this).parent().parent().nextAll(".children1").each(function(){
                    var thispid=$(this).attr("pid");//获取需要展开的子ID
                    if(thispid==thisid){
                        $(this).toggle();
                    }
                })
                $(this).toggleClass("glyphicon-minus");

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
                    <h5>事件</h5>
                    <input type="button" class="btn btn-w-m btn_pl btn-success right"  id="new-add-father" value="新增科室">
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

                    <input type="button" class="btn btn-w-m btn_pl btn-success right" name="" id="new-add" value="新增科室">
                </div>
                <div class="ibox-content">
                    <form method="get" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>科室名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control name add-name" name="name" value="" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="dot">*</span>上级科室</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control name add-name" name="name" value="" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">职称描述</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control describe add-describe" name="describe" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class=" right">
                                <button class="btn btn-primary"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>

                                <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
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