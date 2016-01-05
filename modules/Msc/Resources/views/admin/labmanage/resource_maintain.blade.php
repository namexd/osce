@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}

        .treeview .checked {background-color: #408aff; color: #fff;}
        .list-group-item{cursor:pointer;}
        .select{width: 100%;border-color: #ccc;padding: 5px;}
        .lab_num{display: none;}
        .fa-angle-right{font-size: 30px;margin-top: -8px}
        .deg{transform:rotate(90deg);}
        .border-bottom{border-bottom:none!important;}
    </style>
@stop

@section('only_js')

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
                if($(this).parent().next(".list-group").size()=="1"){
                    $(this).next(".lab_num").children().last().addClass("border-bottom");
                }

            });

            $(".list-group-child").click(function(){
                $(".list-group-parent").removeClass("checked");
                $(".list-group-child").removeClass("checked");
                $(this).addClass("checked");
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
                        <p class="left">已选实验室：</p>
                        <h5 class="left">临床技能室（3-13）</h5>
                    </div>
                    <div class="left" style="margin-left: 20px">
                        <p class="left">容量：</p>
                        <h5 class="left">30人</h5>
                    </div>
                    <input type="button" class="btn btn_pl btn-success right" data-toggle="modal" data-target="#myModal" value="添加设备" id="add_device">
                </div>
                <div class="ibox-content">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>设备名称</th>
                            <th>设备类型</th>
                            <th>设备数量</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>听诊器</td>
                            <td>耗材</td>
                            <td>30</td>
                            <td>
                                <a class="state1 edit"  data-toggle="modal" data-target="#myModal"  style="text-decoration: none" id="edit"><span>编辑数量</span></a>
                                <a class="state2 delete">删除</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('layer_content')
    {{--新增--}}
    <form class="form-horizontal" id="add_device_form" novalidate="novalidate" action="{{route('msc.admin.profession.ProfessionAdd')}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">添加设备</h4>
        </div>
        <div class="modal-body">
            <div class="row" style="padding: 12px 0">
                <div class="col-xs-12 col-md-12">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="">
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped" id="">
                <thead>
                <tr>
                    <th>
                        <input type="checkbox">
                    </th>
                    <th>序号</th>
                    <th>数量</th>
                    <th>资源名称</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                资源类型
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="">听诊器</a>
                                </li>
                                <li>
                                    <a href="">假体模型</a>
                                </li>
                                <li>
                                    <a href="">外科腔镜训练系统</a>
                                </li>
                                <li>
                                    <a href="">腹腔镜</a>
                                </li>
                                <li>
                                    <a href="">投影仪</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="checkbox">
                    </td>
                    <td>
                        1
                    </td>
                    <td>
                        <input type="number">
                    </td>
                    <td>
                        听诊器
                    </td>
                    <td>
                        耗材
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
    {{--编辑--}}
    <form class="form-horizontal" id="edit_form" novalidate="novalidate" action="{{route('msc.admin.profession.ProfessionAdd')}}" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑数量</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">资源名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="腹腔镜" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">资源类型</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="name" value="耗材" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">数量</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control describe add-describe" name="num">
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop