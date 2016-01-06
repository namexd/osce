@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        label{margin-bottom: 0;}
    </style>
@stop

@section('only_js')

    <script>
        $(function(){
            $(document).ajaxSuccess(function(event, request, settings) {
                //楼栋选项卡切换
                ban();
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
            }

//            新增、编辑切换
            $("#add_device").click(function(){
                $("#add_device_form").show();
                $("#edit_form").hide();
            });
            $("#edit").click(function(){
                $("#add_device_form").hide();
                $("#edit_form").show();
            });
//            楼栋数据绑定
            $("#ban_select").change(function(){

                var $treeview=$(".treeview");
                $treeview.empty();
                var $thisId=$(this).val();
                var url="/msc/admin/ladMaintain/floor-lab?lid="+$thisId;
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
                                    $(".treeview #"+ this.floor +" .lab_num").append("<div class='list-group-item list-group-child'>"+this.name+"</div>")
                                });
                                $(".treeview #"+ this.floor +" .list-group-parent").append("<i class='fa fa-angle-right right'></i>");
                            }

                        })
                    }
                })
            });
//            删除
            $(".delete").click(function(){
                var url="";
                layer.confirm('您确定要删除该设备？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    window.location.href=url;
                });
            });
//            新增弹出层选项框
            $(".check_all").click(function(){
                if($(this).children(".check_icon").hasClass("check")){
                    $(this).children(".check_icon").removeClass("check");
                    $(".check_one").children(".check_icon").removeClass("check");
                }else{
                    $(this).children(".check_icon").addClass("check");
                    $(".check_one").children(".check_icon").addClass("check");
                }
            });
            $(".check_one").click(function(){
               if($(this).children(".check_icon").hasClass("check")){
                   $(this).children(".check_icon").removeClass("check");
                   $(".check_all").children(".check_icon").removeClass("check");
               }else{
                   $(this).children(".check_icon").addClass("check");
                   if($(".check_one").size() == $(".check_one").children(".check").size()){
                        $(".check_all").children(".check_icon").addClass("check");
                   }
               }
            });

            $('.treeview').delegate('.list-group-child','click',function(){
//                alert(11);
                var lab_id = $(this).attr('lab_id');//lad_id待确定位置

                var url = "{{ route('msc.admin.LadMaintain.LabIdGetLaboratoryDeviceList')}}";
                $.ajax({
                    type:"get",
                    url:url+'?lab_id='+lab_id,
                    async:true,
                    success:function(res){
                        var str = '';
                        if(res.code == 1){
                            var data = res.data.rows.LadDeviceList.data;
                            console.log(data);
                            for(var i=0;i<data.length;i++){
                                str += '<tr>' +
                                        '<td>'+data[i].id+'</td>' +
                                        '<td>'+data[i].device_info.name+'</td>' +
                                        '<td>'+data[i].device_info.devices_info.name+'</td>' +
                                        '<td>'+data[i].total+'</td>' +
                                        '<td>' +
                                        '<a class="state1 edit"  data-toggle="modal" data-target="#myModal"  style="text-decoration: none" id="edit">' +
                                        '<span>编辑数量</span></a><a class="state2 delete">删除</a>' +
                                        '</td>' +
                                        '</tr>';
                            }
                        }
                        $('#table-striped tbody').html(str);
                    }
                });

            })

            $('.right').click(function(){
//                alert('11111');
                var url  = "{{route('msc.admin.LadMaintain.LaboratoryListData')}}";
                $.ajax({
                    type:"get",
                    url:url,
                    async:true,
                    success:function(result){
                        var html = '';
                        var list ='';
                        console.log(result);
                        console.log(result.data.rows.list);
                        $(result.data.rows.deviceType).each(function(){

                                     html+='<li>' +
                                         '<a href="">'+this.name+'</a>'+
                                         ' </li>'

                        })
                        $('.dropdown-menu').html(html);

                        $(result.data.rows.list).each(function(){

                                 list+='<tr>' +
                                    '<td>' +
                                    '<label class="check_label checkbox_input check_one"> ' +
                                    '<div class="check_real check_icon display_inline">' +
                                    '</div> <input type="hidden" name="" value="">' +
                                    '</label>' +
                                    '</td>' +
                                    ' <td>1</td>' +
                                    ' <td> <input type="number"></td>' +
                                    ' <td>'+this.name+'</td> ' +
                                    '<td>'+this.catename+'</td> ' +
                                    '</tr> '
                            console.log(this.name);
                        })
                        $('#addition tbody').html(list);
                    }
                })
            })

          $('.edit').click(function(){
              if($(this).attr("")){
                  $('input[name=name]').val($(this).parent().parent().find('.device').html());
                  $('input[name=type]').val($(this).parent().parent().find('.deviceType').html());
                  $('input[name=total]').val($(this).parent().parent().find('.total').html());
              }
              $('#edit_form').attr('action','{{route("")}}');
              var id = $(this).attr("data");
              $('#edit_form').append('<input type="hidden" name="id" value="'+id+'">');
          })

        })
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
                            <td class="midbody_id">1</td>
                            <td  class="device">听诊器</td>
                            <td class="deviceType">耗材</td>
                            <td class="total">30</td>
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
    <form class="form-horizontal" id="add_device_form" novalidate="novalidate" action="{{route('msc.admin.LadMaintain.DevicesAdd')}}" method="post">
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
            <table class="table table-striped" id="addition">
                <thead>
                <tr>
                    <th>
                        <label class="check_label checkbox_input check_all">
                            <div class="check_real check_icon display_inline"></div>
                            <input type="hidden" name="" value="">
                        </label>
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

                            </ul>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                {{--<tr>--}}
                    {{--<td>--}}
                        {{--<label class="check_label checkbox_input check_one">--}}
                            {{--<div class="check_real check_icon display_inline"></div>--}}
                            {{--<input type="hidden" name="" value="">--}}
                        {{--</label>--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--1--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--<input type="number">--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--听诊器--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--耗材--}}
                    {{--</td>--}}
                {{--</tr>--}}
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
                    <input type="text" class="form-control name add-name" name="name" value="腹腔镜" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">资源类型</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control describe add-describe" name="type" value="耗材" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">数量</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control describe add-describe" name="total">
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