@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/resourcemanage/reourcemanage.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('msc/common/css/bootstrapValidator.css')}}">
<style>
    .bv-form .help-block {
        margin-bottom: 6px;
        margin-left: 95px;
    }
    .has-feedback label~.form-control-feedback {top: 0;}
    .multi-control{
        position: absolute;
        right: 12px;
        top: 18px;
        color: #C7D0D8;
    }
</style>
@stop
@section('only_head_js')
<script type="text/javascript" src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
<script type="text/javascript" src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>
<script type="text/javascript" src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    新增资源
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>


<form id="sourceForm" action="{{ action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddResources') }}" method="post">
    <div class="phone_box  marb_15 padb_8">
        <!--<div class="center phone_btn">-->
            <!--<i class="fa  fa-camera font26"></i>-->
        <!--</div>-->
        <div class="phone_box">
            <ul class="img_box">
                <div class="add_img">
                    <span id="upload_bnt">
                        <input type="file" name="images" id="file0" multiple="multiple" />
                    </span>
                </div>
            </ul>
        </div>

        <!-- <ul class="img_box">
            <img src="" alt=""/>
            <div class="add_img">
                <input type="file" name="images" id="file0" multiple="multiple" />
            </div>
        </ul> -->
    </div>
    <div class="add_main">

        <div class="form-group">
            <label for="">类别</label>
            <input type="button" class="form-control xuan_type"  name="cate_id_sm" style="" value="请选择类别" required="">
            <input type="hidden" id="hide-type" name="resources_type"/>
            <input type="hidden" id="hide-type-id" name="cate_id" />
            <i class="fa fa-angle-right more"></i>
        </div>
        <div class="form-group">
            <label for="">名称</label>
            <input type="text" id="name" required name="name" placeholder="请输入名称" class="form-control">
        </div>
        <div class="form-group">
            <label for="">功能描述</label>
            <input type="text" name="detail" placeholder="请输入功能描述" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="">负责人</label>
            <input type="text" name="manager_name" placeholder="请输入负责人姓名" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="">负责电话</label>
            <input type="text" name="manager_mobile" placeholder="请输入电话" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="">地址</label>
            <input type="text" name="location" placeholder="请输入地址" required="" class="form-control">
        </div>
        <div class="form-group has-feedback">
            <label for="">编号</label>
            <input type="text"  name="code[]" placeholder="请输入编号" class="form-control">
            <i class="fa fa-plus fa-lg multi-control"></i>
        </div>
        <div class="hacker-hidden"></div>
        <input type="hidden" name="images_path[]" value="测试1">
        <input type="hidden" name="images_path[]" value="测试2">
    </div>
    <div class="w_94 submit_box">
        <input type="submit" ng-disabled="form.$invalid" class="btn1" value="保存"/>
    </div>

    <!-- 类别列表-->
    <div id="leibie_list">
        <div class="leibie_left">
        </div>
        <div class="leibie_box">
            <p class="font16 check_tit">请选择类别</p>
            <ul id="caterogy_list">
                <li class="more_li">
                    开放实验室<i class="fa fa-angle-right  font18"></i>
                    <ul style="display:none;">
                        <li class="lei_txt">腹腔镜</li>
                        <li class="lei_txt">静脉穿刺</li>
                    </ul>
                </li>
                <li class="more_li">
                    开放实验室<i class="fa fa-angle-right  font18"></i>
                    <ul style="display:none;">
                        <li class="lei_txt">腹腔镜</li>
                        <li class="lei_txt">静脉穿刺</li>
                    </ul>
                </li>
                <li class="lei_txt">开放设备 </li>
                <li class="lei_txt">模型</li>
            </ul>
        </div>
    </div>
</form>
<script>
$(function(){

    /*mao 2015-11-25
     *表单验证
     */
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '用户名不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 6,
                        max: 30,
                        message: '用户名长度必须在6到30之间'
                    }/*最后一个没有逗号*/
                }
            },
            detail: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '描述不能为空'
                    }
               }
            },
            manager_name: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '姓名不能为空'
                    },
                    stringLength: {
                        min:2,
                        message: '姓名长度必须大于2'
                    }
                }
            },
            manager_mobile: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
               }
            },
            location: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '地址不能为空'
                    }
               }
            }               
        }
    });

    /*新增编号*/
    $('.add_main').on('click','.fa-plus',function(){
        $(this).removeClass('fa-plus');
        $(this).addClass('fa-close');
        var html = '<div class="form-group has-feedback">'+
                     '<label for="">编号</label>'+
                     '<input type="text"  name="code[]" placeholder="请输入编号" class="form-control">'+
                     '<i class="fa fa-plus fa-lg multi-control"></i>'+
                   '</div>';
        $('.hacker-hidden').before(html);
    });

    /*删除新增*/
    $('.add_main').on('click','.fa-close',function(){
        console.log($(this).parent().html())
        $(this).parent().remove();
    });

    /*图片上传*/
    $("#upload_bnt").change(function(){
            var url = "http://{{ $_SERVER['HTTP_HOST'] }}";
            $.ajaxFileUpload
            ({
                url:'{{ url('commom/upload-image') }}',
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code == 1){
                        $('.add_img').before('<li><img src="'+(url+data.data.path)+'" width="100%"><i class="fa fa-remove font14 del_img"></i><input type="hidden" name="images_path[]" value="'+data.data.path+'"><>');
                    }
                },
                error: function (data, status, e)
                {
                    //console.log(data);
                }
            });
        }) ;
    $('.phone_box').delegate('i','click',function(){
        $(this).parents('li').remove();
    })

    /*选择类型*/
    $.ajax({
        type:"get",
        async:true,
        url:"{{action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@getCategroyList')}}",
        success:function(res){
            var html = '';
            if(res.code!=1){
                console.log(res.message);
            }else{
                var data = res.data.rows;
                for(var i in data){
                    html += '<li class="lei_txt" value="'+data[i].id+'">'+data[i].name+'</li>';
                }
                $('#caterogy_list').html(html);
            }
        }
    });
    //选择
    $(".xuan_type").click(function(){
        $("#leibie_list").animate({right:"0"});
    });
    $(".more_li").click(function(){
        $(this).children("ul").toggle();
    });
    $(".leibie_left").click(function(){
        $("#leibie_list").animate({right:"-100%"});
    });
    $("#caterogy_list").on('click','.lei_txt',function(){
        var txt= $(this).text();
        var id= $(this).attr('value');
        $("#leibie_list").animate({right:"-100%"});
        $(".xuan_type").val(txt);
        $("#hide-type-id").val(id);
        $("#hide-type").val("TOOLS");//默认至
    });


    })
    var str='';

    $("#file0").change(function(){
        $.ajaxFileUpload
        ({
            url:'{{ url('commom/upload-image') }}',
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                //alert(data.data.path);
                console.log(data);

            },
            error: function (data, status, e)
            {
                console.log(data);
                //alert("通信失败");
            }
        });
    }) ;
    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }
</script>
@stop