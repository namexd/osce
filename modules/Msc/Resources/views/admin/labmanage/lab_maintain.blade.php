@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/labmanage/labmanage.js')}}"></script>
    <script>
        $(function(){
            //编辑验证
            function edit_form(){
                $("#edit_from").bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {/*验证*/
                        hospital: {
                            message: 'The hospital is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '所属分院不能为空'
                                }
                            }
                        },
                        building: {
                            message: 'The building is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '教学楼不能为空'
                                }
                            }
                        },
                        floor: {
                            message: 'The floor is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '楼层不能为空'
                                }
                            }
                        },
                        name: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '实验室名称不能为空'
                                }
                            }
                        },
                        code: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '房号不能为空'
                                }
                            }
                        },
                        total: {/*键名username和input name值对应*/
                            validators: {
                                notEmpty: {
                                    /*非空提示*/
                                    message: '实验室容量不能为空'
                                },
                                regexp: {
                                    regexp: /^(?:[1-9]\d?|[1234]\d{2}|500)$/,
                                    message: '请正确填写实验室容量'
                                }
                            }
                        },
                        manager_user_id:{
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '管理员不能为空'
                                }
                            }
                        },
                        open_type:{
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '实验室性质不能为空'
                                }
                            }
                        },
                        status: {
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '状态不能为空'
                                }
                            }
                        }
                    }
                });
            }
//            新增验证
            function add_form(){
                $('#add_from').bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {/*验证*/
                        hospital: {
                            message: 'The hospital is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '所属分院不能为空'
                                }
                            }
                        },
                        building: {
                            message: 'The building is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '教学楼不能为空'
                                }
                            }
                        },
                        floor: {
                            message: 'The floor is not valid',
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '楼层不能为空'
                                }
                            }
                        },
                        name: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '实验室名称不能为空'
                                }
                            }
                        },
                        code: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '房号不能为空'
                                }
                            }
                        },
                        total: {/*键名username和input name值对应*/
                            validators: {
                                notEmpty: {
                                    /*非空提示*/
                                    message: '实验室容量不能为空'
                                },
                                regexp: {
                                    regexp: /^(?:[1-9]\d?|[1234]\d{2}|500)$/,
                                    message: '请正确填写实验室容量'
                                }
                            }
                        },
                        manager_user_id:{
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '管理员不能为空'
                                }
                            }
                        },
                        open_type:{
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '实验室性质不能为空'
                                }
                            }
                        },
                        status: {
                            validators: {
                                regexp: {
                                    regexp: /^(?!-1).*$/,
                                    message: '状态不能为空'
                                }
                            }
                        }
                    }
                });
            }

            //            新增
            var add_hospital=$(".add_hospital").html();
            var add_master=$(".add_master").html();
            $("#add_lab").click(function(){
                var $addUrl=$("#addUrl").val();
                $("#edit_from").hide();
                $("#add_from").remove();
                $("#form_box").append('<form class="form-horizontal" id="add_from" novalidate="novalidate" action="'+$addUrl+'" method="post"> ' +
                        '<div class="modal-header"> ' +
                        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                        '<h4 class="modal-title" id="myModalLabel">新增实验室</h4> ' +
                        '</div> ' +
                        '<div class="modal-body"> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldschool edit_hospital school" name="hospital"> ' +
                        '<option value="-1">请选择所属分院</option> ' +add_hospital+
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldlocal local" name="building"> ' +
                        '<option value="-1">请选择教学楼</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldfloor floor" name="floor"> ' +
                        '<option value="-1">请选择楼层</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="name" name="name" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">简称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="short_name" name="short_name" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">英文全称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="enname" name="enname" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">英文缩写</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="short_enname" name="short_enname" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>房号</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="code" name="code" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>容量</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control describe add-describe" id="total" name="total" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">管理员</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b teacher edit_master" name="manager_user_id"> ' +
                        '<option value="-1">点击选择</option> ' +add_master+
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">实验室性质</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b opentype" name="open_type"> ' +
                        '<option value="-1">点击选择</option> ' +
                        '<option value="1">实验室</option> ' +
                        '<option value="2">准备间</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category"   class="form-control m-b sta" name="status"> ' +
                        '<option value="-1">请选择状态</option> ' +
                        '<option value="1">正常</option> ' +
                        '<option value="0">停用</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="hr-line-dashed"></div> ' +
                        '<div class="form-group"> ' +
                        '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                        '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                        '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                        '</div> ' +
                        '</div> ' +
                        '</div> ' +
                        '</form>');
                $('#add_from').delegate('.school','change',function(){
                    var id = $(this).val();
                    var opstr = '<option value="-1">请选择教学楼</option>';
                    $.ajax({
                        type: "POST",
                        url: "{{route('msc.admin.laboratory.getLocal')}}",
                        data: {id:id},
                        success: function(msg){
                            if(msg){
                                $(msg).each(function(i,k){
                                    opstr += '<option value="'+k.id+'">'+k.name+'</option>';
                                });
                                $('.local').html(opstr);
                            }
                        }
                    });
                });
                $('#add_from').delegate('.local','change',function(){
                    var id = $(this).val();
                    var opstr = '<option value="-1">请选择楼层</option>';
                    $.ajax({
                        type: "POST",
                        url: "{{route('msc.admin.laboratory.getFloor')}}",
                        data: {id:id},
                        success: function(msg){
                            if(msg){

                                $.each($(msg),function(i,n){
                                    opstr += '<option value="'+n+'">'+n+'楼</option>';
                                });
                                $('.floor').html(opstr);
                            }
                        }
                    });
                });
                $('.oldlocal').change(function(){
                    var id = $(this).val();
                    var opstr = '<option value="-1">请选择楼层</option>';
                    $.ajax({
                        type: "POST",
                        url: "{{route('msc.admin.laboratory.getFloor')}}",
                        data: {id:id},
                        success: function(msg){
                            if(msg){

                                $.each($(msg),function(i,n){
                                    opstr += '<option value="'+n+'">'+n+'楼</option>';
                                });
                                $('.floor').html(opstr);
                            }
                        }
                    });
                });
                add_form();
            });
//            编辑
            var edit_hospital=$(".edit_hospital").html();
            var edit_master=$(".edit_master").html();
            $('.update').click(function () {
                var $editUrl=$("#editUrl").val();
                $("#add_from").hide();
                $("#edit_from").remove();
                $("#form_box").append('<form class="form-horizontal" id="edit_from" novalidate="novalidate" action="'+$editUrl+'" method="post"> " ' +
                        '<div class="modal-header"> ' +
                        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                        '<h4 class="modal-title" id="myModalLabel">编辑实验室</h4> ' +
                        '</div> ' +
                        '<div class="modal-body"> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldschool edit_hospital" name="hospital"> ' +edit_hospital+
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldlocal local" name="building"> ' +
                        '<option value="-1">请选择教学楼</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b oldfloor floor" name="floor"> ' +
                        '<option value="-1">请选择楼层</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="name" name="name" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">简称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="short_name" name="short_name" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">英文全称</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="enname" name="enname" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">英文缩写</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="short_enname" name="short_enname" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>房号</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control name add-name" id="code" name="code" value="" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>容量</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<input type="text" class="form-control describe add-describe" id="total" name="total" /> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">管理员</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b teacher edit_master" name="manager_user_id"> ' +edit_master+
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label">实验室性质</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category" class="form-control m-b opentype" name="open_type"> ' +
                        '<option value="1">实验室</option> ' +
                        '<option value="2">准备间</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                        '<div class="col-sm-9"> ' +
                        '<select id="select_Category"   class="form-control m-b sta" name="status"> ' +
                        '<option value="1">正常</option> ' +
                        '<option value="0">停用</option> ' +
                        '</select> ' +
                        '</div> ' +
                        '</div> ' +
                        '<div class="hr-line-dashed"></div> ' +
                        '<div class="form-group"> ' +
                        '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                        '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                        '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                        '</div> ' +
                        '</div> ' +
                        '</div> ' +
                        '</form>');
                edit_form();
                var updateobj = $(this);
                $('#code').val($(this).parent().parent().find('.code').html());
                $('input[name=floor]').val($(this).parent().parent().find('.floors').attr('data-b'));
                $('#name').val($(this).parent().parent().find('.name').html());
                $('#short_name').val($(this).parent().parent().find('.short_name').val());
                $('#enname').val($(this).parent().parent().find('.enname').val());
                $('#short_enname').val($(this).parent().parent().find('.short_enname').val());
                $('#total').val($(this).parent().parent().find('.total').val());
                $('.oldschool option').each(function(){
                    if($(this).val() == $(updateobj).parent().parent().find('.lname').attr('data')){
                        $(this).attr('selected','selected');
                    }
                });
                var id = $(this).attr("data");
                $.ajax({
                    type: "POST",
                    url: "{{route('msc.admin.laboratory.getFloor')}}",
                    data: {id:id,type:1},
                    success: function(msg){
                        var opstr = '';
                        if(msg){
                            $.each($(msg),function(i,n){
                                if(n == $(updateobj).parent().parent().find('.floors').html()){
                                    opstr += '<option value="'+n+'" selected="selected">'+n+'楼</option>';
                                }else{
                                    opstr += '<option value="'+n+'">'+n+'楼</option>';
                                }
                            });
                            $('.oldfloor').html(opstr);
                        }
                    }
                });
                $('.oldschool').change(function(){
                    var id = $(this).val();
                    var opstr = '<option value="-1">请选择教学楼</option>';
                    $.ajax({
                        type: "POST",
                        url: "{{route('msc.admin.laboratory.getLocal')}}",
                        data: {id:id},
                        success: function(msg){
                            if(msg){
                                $(msg).each(function(i,k){

                                    opstr += '<option value="'+k.id+'">'+k.name+'</option>';
                                });
                                $('.local').html(opstr);
                            }
                        }
                    });
                });
                $.ajax({
                    type: "POST",
                    url: "{{route('msc.admin.laboratory.getLocal')}}",
                    data: {id:$(updateobj).parent().parent().find('.lname').attr('data'),type:1},
                    success: function(msg){
                        console.log(msg);
                        var opstr = '';
                        if(msg){
                            $.each($(msg),function(i,n){
                                if(n.id == $(updateobj).parent().parent().find('.lname').attr('data-local')){
                                    opstr += '<option value="'+ n.id+'" selected="selected">'+ n.name+'</option>';
                                }else{
                                    opstr += '<option value="'+ n.id+'">'+ n.name+'</option>';
                                }

                            });
                            $('.oldlocal').html(opstr);
                        }
                    }
                });
                var status = $(this).parent().parent().find('.status').attr('data');
                $('.sta option').each(function(){
                    if($(this).val() == status){
                        $(this).attr('selected','selected');
                    }
                });
                var teach = $(this).parent().parent().find('.tname').attr('data');
                $('.teacher option').each(function(){
                    if($(this).val() == teach){
                        $(this).attr('selected','selected');
                    }
                });
                var open_type = $(this).parent().parent().find('.open_type').attr('data');
                if(open_type){
                    $('.opentype option').each(function(){
                        if($(this).val() == open_type){
                            $(this).attr('selected','selected');
                        }
                    });
                }
                $('#edit_from').append('<input type="hidden" name="id" value="'+id+'">');
            });
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'lab_maintain'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-3">
                <form action="" method="get">
                    <div class="input-group">
                        <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{@$keyword}}">
                        <input type="hidden" name="status" class="input-sm form-control" value="{{@$status}}">
                        <input type="hidden" name="open_type" class="input-sm form-control" value="{{@$open_type}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-xs-6 col-md-9 user_btn">
                <button class="btn btn_pl btn-success right">
                    <a href=""  class="state1 edit addlab" data-toggle="modal" data-target="#myModal" style="text-decoration: none" id="add_lab">
                        <span style="color: #fff;">新增实验室</span>
                    </a>
                </button>
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <form action="" class="container-fluid" id="list_form">
                    <table class="table table-striped" id="table-striped">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>实验室名称</th>
                            <th>房号</th>
                            <th>教学楼</th>
                            <th>楼层</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        类型
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}">全部</a>
                                        </li>

                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=1">实验室</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status={{@$status}}&open_type=2">准备间</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>管理员</th>
                            <th>
                                <div class="btn-group Examine">
                                    <button data-toggle="dropdown" class="btn btn-white3 dropdown-toggle">
                                        状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&open_type={{@$open_type}}&status=">全部</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status=1&open_type={{@$open_type}}">正常</a>
                                        </li>
                                        <li>
                                            <a href="/msc/admin/laboratory/index?keyword={{@$keyword}}&status=0&open_type={{@$open_type}}">停用</a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($datalist))
                            @foreach($datalist as $k=>$v)
                                <tr>
                                    <td>{{@$k+1}}</td>
                                    <td class="name">{{@$v['name']}}</td>
                                    <td class="code">{{@$v['code']}}</td>
                                    <td class="lname" data="{{@$v->floors->school_id}}" data-local="{{@$v->location_id}}">{{@$v->floors->name}}</td>
                                    <td class="floors">{{@$v['floor']}}</td>
                                    <td class="open_type" data="{{@$v->opentype}}">{{@$v['open_type']}}</td>
                                    <td class="tname" data="{{@$v->user->id}}">{{@$v->user->name}}</td>
                                    <td class="status" data="{{@$v['status']}}">@if($v['status'] == 1)正常@else<span class="state2">停用</span>@endif</td>
                                    <input type="hidden" class="short_name" value="{{@$v->short_name}}">
                                    <input type="hidden" class="enname" value="{{@$v->enname}}">
                                    <input type="hidden" class="short_enname" value="{{@$v->short_enname}}">
                                    <input type="hidden" class="total" value="{{@$v->total}}">
                                    <td>
                                        <a href=""  data="{{$v['id']}}"  class="state1 edit update edit_lab" data-toggle="modal" data-target="#myModal" style="text-decoration: none">
                                            <span>编辑</span>
                                        </a>
                                        @if($v->status == 1)
                                            <a  data="{{$v['id']}}" data-type="0" class="state2 modal-control stop">停用</a>
                                        @else
                                            <a  data="{{$v['id']}}" data-type="1" class="state2 modal-control stop">启用</a>
                                        @endif
                                        <a data="{{$v['id']}}" class="state2 edit_role modal-control delete">删除</a>
                                        <input type="hidden" class="setid" value="1"/>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <?php echo $datalist->render();?>
        </div>
    </div>
@stop

@section('layer_content')
    <div id="form_box">
        {{--编辑--}}
        <input type="hidden" value="{{route("msc.admin.laboratory.getEditLabInsert")}}" id="editUrl">
        <form class="form-horizontal" id="edit_from" novalidate="novalidate" action="{{route('msc.admin.laboratory.getAddLabInsert')}}" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">编辑实验室</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b oldschool edit_hospital" name="hospital">
                            @if(!empty($school))
                                @foreach($school as $ss)
                                    <option value="{{$ss->id}}">{{$ss->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b oldlocal local" name="building">
                            <option value="-1">请选择教学楼</option>

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b oldfloor floor" name="floor">
                            <option value="-1">请选择楼层</option>

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" id="name" name="name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">简称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" id="short_name" name="short_name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">英文全称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" id="enname" name="enname" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">英文缩写</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" id="short_enname" name="short_enname" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>房号</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" id="code" name="code" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>容量</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control describe add-describe" id="total" name="total" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">管理员</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b teacher edit_master" name="manager_user_id">
                            @if(!empty($teacher))
                                @foreach($teacher as $tch)
                                    @if($tch->aboutUser)
                                        <option value="{{$tch->aboutUser->id}}">{{$tch->aboutUser->name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">实验室性质</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b opentype" name="open_type">
                            <option value="-1">点击选择</option>
                            <option value="1">实验室</option>
                            <option value="2">准备间</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                    <div class="col-sm-9">
                        <select id="select_Category"   class="form-control m-b sta" name="status">
                            <option value="-1">请选择状态</option>
                            <option value="1">正常</option>
                            <option value="0">停用</option>
                        </select>
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

        {{--新增--}}
        <input type="hidden" value="{{route('msc.admin.laboratory.getAddLabInsert')}}" id="addUrl">
        <form class="form-horizontal" id="add_from" novalidate="novalidate" action="{{route('msc.admin.laboratory.getAddLabInsert')}}" method="post" style="display:none">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">新增实验室</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b school add_hospital" name="hospital">
                            @if(!empty($school))
                                @foreach($school as $ss)
                                    <option value="{{$ss->id}}">{{$ss->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b local" name="building">
                            <option value="-1">请选择教学楼</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b floor" name="floor">
                            <option value="-1">请选择楼层</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">简称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="short_name" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">英文全称</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="enname" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">英文缩写</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="short_enname" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>房号</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control name add-name" name="code" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>容量</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control describe add-describe" name="total" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">管理员</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b add_master" name="manager_user_id">
                            @if(!empty($teacher))
                                @foreach($teacher as $tch)
                                    @if($tch->aboutUser)
                                        <option value="{{$tch->aboutUser->id}}">{{$tch->aboutUser->name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">实验室性质</label>
                    <div class="col-sm-9">
                        <select id="select_Category" class="form-control m-b" name="open_type">
                            <option value="-1">点击选择</option>
                            <option value="1">实验室</option>
                            <option value="2">准备间</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>
                    <div class="col-sm-9">
                        <select id="select_Category"   class="form-control m-b" name="status">
                            <option value="-1">请选择状态</option>
                            <option value="1">正常</option>
                            <option value="0">停用</option>
                        </select>
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
    </div>

@stop