@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        label{margin-bottom: 0;}
        .treeview .lab_num{background-color: #f5f5f5;}
    </style>
@stop

@section('only_js')

    <script>
        $(function(){
            $(document).ajaxSuccess(function(event, request, settings) {
                //楼栋选项卡切换
                ban();
                //实验室数据加载
                labdata();
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
                });
            }

//            新增、编辑切换
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
                            $(result).each(function(){
                                if(this.lab.length>0){
                                    $treeview.append( "<div class='list-group' style='margin-bottom: 0' id='"+this.floor+"'>" +
                                            "<div class='list-group-item list-group-parent'>"
                                            +this.floor+"楼"
                                            +"</div>"
                                            +"<div class='lab_num'></div>"
                                            +"</div>"
                                    );
                                }

                                if(this.lab!=""){
                                    $(this.lab).each(function(){
                                        $(".treeview #"+ this.floor +" .lab_num").append("<div class='list-group-item list-group-child  labdetail'  data='"+this.total+"' lab_id='"+this.id+"'>"+this.name+"</div>")
                                    });
                                    $(".treeview #"+ this.floor +" .list-group-parent").append("<i class='fa fa-angle-right right'></i>");
                                }

                            })

                    }
                })
            });
//              人数数量
            $('.ibox-content').delegate('.labdetail','click',function(){
                var total = $(this).attr('data');
                if(total == 'null'){
                    total = 0;
                }
                var labname = $(this).html();
                $('.labname').html(labname);
                $('.labtotal').html(total+'人');
                $('#add_device').removeAttr('disabled');
                $('#lab_id').val($(this).attr('lab_id'));
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
            $("#add_device_form").delegate(".check_one","click",function(){
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

            //实验室数据显示
            function labdata(){
                $('.treeview .list-group-child').click(
                        function(){
                            var lab_id = $(this).attr('lab_id');
                            updateLabDeviceList(lab_id);
                        }
                )
            }

            //更新和当前实验室相关的列表数据
            function updateLabDeviceList(lab_id){
                var url = "{{ route('msc.admin.LadMaintain.LaboratoryDeviceList')}}";
                $.ajax({
                    type:"get",
                    url:url+'?lab_id='+lab_id,
                    async:true,
                    success:function(res){

                        var str = '';
                        if(res.code == 1){
                            var data = res.data.rows.LadDeviceList.data;
                            for(var i=0;i<data.length;i++){
                                str += '<tr>' +
                                        '<td>'+data[i].id+'</td>' +
                                        '<td class="device_name">'+data[i].device_info.name+'</td>' +
                                        '<td class="device_type">'+data[i].device_info.devices_cate_info.name+'</td>' +
                                        '<td class="total" id="DeviceNum_'+data[i].id+'">'+data[i].total+'</td>' +
                                        '<td class="opera">' +
                                        '<a class="state1 edit edit_res"  data-toggle="modal" data-target="#myModal"  style="text-decoration: none" id="edit">' +
                                        '<span class="edit_num" labDeviceId="'+data[i].id+'">编辑数量</span>' +
                                        '</a>' +
                                        '<a class="state2 delete" labDeviceId="'+data[i].id+'"><span>删除</span></a>' +
                                        '</td>' +
                                        '</tr>';
                            }
                        }
                        $('#table-striped tbody').html(str);
                    }
                });
            }

//            设备添加回显数据
            $('#add_device').click(function(){
//                点击新增按钮显示当前实验室设备弹出层
                $("#add_device_form").show();
                $(".check_all").children(".check_icon").removeClass("check");
                $("#edit_form").hide();
                add();
            });
//            添加中  关键字搜索
            $('#search').click(function(){
                add();
                return false;
            });
            //编辑数量
            $('#table-striped').delegate('.edit_res','click',function(){
                $("#add_device_form").hide();
                $("#edit_form").show();
            });

            //编辑的时候把数据提取到表单
            $('#table-striped').delegate('.edit_num','click',function(){
//                alert($(this).attr('labDeviceId'));
                if($(this).attr("labDeviceId")){
                    $('input[name=name]').val($(this).parent().parent().parent().find('.device_name').html());

                    $('input[name=type]').val($(this).parent().parent().parent().find('.device_type').html());
                    $('input[name=total]').val($(this).parent().parent().parent().find('.total').html());
                }

                var id = $(this).attr("labDeviceId");
                $('input[name="id"]').remove();
                $('#edit_form').append('<input type="hidden" name="id" value="'+id+'">');

            })




            //删除和当前实验室相关的 设备信息
            $('#table-striped').delegate('.delete','click',function(){
                var $this = $(this);
                var this_id = $(this).attr('labDeviceId');
                var url = "{{ route('msc.admin.LadMaintain.LadDevicesDeletion') }}"+"?id="+this_id;
                layer.confirm('您确定要删除该设备？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.ajax({
                        type:"get",
                        url:url,
                        async:true,
                        success:function(res){
                            if(res.code == 1){
                                $this.parents('tr').remove();
                                layer.msg("删除成功", {icon: 1,time: 1000});
                            }else{
                                layer.msg(res.message, {icon: 2,time: 1000});
                            }
                        }
                    });
                });
            });


            //设备添加方法

            function add(cate_id){
                var url  = "{{route('msc.admin.LadMaintain.LaboratoryListData')}}";
                url += '?keyword='+$('#keyword').val()+'&lab_id='+$('#lab_id').val()
                if(cate_id){
                    url += '&devices_cate_id='+cate_id;
                }
                $.ajax({
                    type:"get",
                    url:url,
                    async:true,
                    success:function(result){
                        var html = '<li>' +
                                '<a href="javascript:void(0)" cate_id = "0">全部</a>'+
                                ' </li>';
                        var list ='';
                        $(result.data.rows.deviceType).each(function(){
                            html+='<li>' +
                                    '<a href="javascript:void(0)" cate_id = "'+this.id+'">'+this.name+'</a>'+
                                    ' </li>'
                        })
                        $('#device-type').html(html);
                        $(result.data.rows.list).each(function(){
                            list+='<tr>' +
                                    '<td>' +
                                    '<label class="check_label checkbox_input check_one"> ' +
                                    '<div class="check_real check_icon display_inline">' +
                                    '</div> <input type="hidden" name="" value="'+this.id+'">' +
                                    '</label>' +
                                    '</td>' +
                                    ' <td>'+this.id+'</td>' +
                                    ' <td> <input type="number" class="deviceNum" value="1"></td>' +
                                    ' <td>'+this.name+'</td> ' +
                                    '<td>'+this.catename+'</td> ' +
                                    '</tr> '
                        })
                        $('#addition tbody').html(list);
                    }
                })

            }
            //根据类别筛选资源列表
            $('#device-type').delegate('a','click',function(){
                add($(this).attr('cate_id'))
            })
            //保存编辑数量
            $('#saveEdit').click(function(){
                var url = "{{route('msc.admin.LadMaintain.DevicesTotalEdit')}}";
                var labDeviceId = $('#edit_form').find('input[name="id"]').val();
                var total = $('#edit_form').find('input[name="total"]').val();
                $.ajax({
                    type:"get",
                    url:url,
                    data:{lab_device_id:labDeviceId,total:total},
                    async:true,
                    success:function(result){
                        if(result.code == 1){
                            $('#DeviceNum_'+labDeviceId).html(total);
                            layer.msg("编辑成功", {icon: 1,time: 1000});
                        }else{
                            layer.msg("编辑失败", {icon: 2,time: 1000});
                        }
                    }
                })
            });
            //编辑数量验证不能为负
            $("#edit_form").delegate(".plus","change",function(){
                if($(this).val()<=0){
                    $(this).val("1");
                }
            });

            //添加实验室相关设备
            $('#addDevice').click(function(){

                var DeviceIdNumArr = [];

                $(".check_one").children(".check").next("input").each(function(){
                    DeviceIdNumArr.push( $(this).val()+','+$(this).parents('tr').find('.deviceNum').val());
                })
                var url = "{{ route('msc.admin.LadMaintain.DevicesAdd') }}";

                $.ajax({
                    type:"post",
                    url:url,
                    data:{lab_id:$('#lab_id').val(),device_id_num:DeviceIdNumArr},
                    async:true,
                    success:function(result){
                        if(result.code == 1){
                            updateLabDeviceList($('#lab_id').val());
                            layer.msg("添加成功", {icon: 1,time: 1000});
                        }else{
                            layer.msg("添加失败", {icon: 2,time: 1000});
                        }

                    }
                })
            })

           //触发 check 选中
            $('#addition').delegate('.deviceNum','change',function(){
                var num = $(this).val();
                if(num>0){
                    $(this).parents("tr").find('.check_real').addClass('check');
                }else{
                    $(this).val(1);
                    return false;
                }
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
                        <h5 class="left labname">无</h5>
                    </div>
                    <div class="left" style="margin-left: 20px">
                        <p class="left">容量：</p>
                        <h5 class="left  labtotal " >0人</h5>
                    </div>
                    <input type="button" class="btn btn_pl btn-success right" data-toggle="modal" data-target="#myModal" disabled="disabled" value="新增设备" id="add_device">
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

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('layer_content')
    {{--新增--}}
    <form class="form-horizontal" id="add_device_form" novalidate="novalidate" action="" method="post">
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
                                <button class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
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
                            <ul class="dropdown-menu" id="device-type">
                            </ul>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <input type="hidden" id="lab_id">
                    <button class="btn btn-primary"  type="submit" id="addDevice" data-dismiss="modal" aria-hidden="true">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
    {{--编辑--}}
    <form class="form-horizontal" id="edit_form" novalidate="novalidate" action="" method="post">
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
                    <input type="number" class="form-control describe add-describe plus" name="total">
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary" id="saveEdit"  type="submit" data-dismiss="modal" aria-hidden="true">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop