@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/wechat/resourcemanage/css/returnmanagement.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
<script type="text/javascript" src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
<Script type="text/javascript">

    $(document).ready(function(){
        $("#get_detail").click(function(){
            $("#the_more").slideToggle(300);
            $("#get_detail .i_right").toggleClass("rotate90");
        })
    })

    //var str='';
</Script>
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        确认归还信息
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <form action="{{ url('/msc/wechat/resources-manager/teacher-confirm-back') }}" method="post">
        <div class="add_main mart_5">
            <div class="form-group">
                <label for="">借用者</label>
                <div class="txt">
                    {{ @$BorrowingInfo['user']['name'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">设备名称</label>
                <div class="txt">
                    {{ @$BorrowingInfo['resourcesTool']['name'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">编号</label>
                <div class="txt">
                    {{ @$BorrowingInfo['resourcesToolItem']['code'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">外借时段</label>
                <div class="txt">
                    {{ @$BorrowingInfo['begindate'] }}-{{ @$BorrowingInfo['enddate'] }}
                </div>
            </div>
        </div>
        <div class="check_info mart_5  marb_10">
            <div class="title" id="get_detail">
                录入设备信息 <i class="fa fa-angle-right more_detail i_right"></i>
            </div>
            <div id="the_more" style="display:none;">
                <div class="phone_box">
                    <ul class="img_box">
                        <div class="add_img">
                            <span id="upload_bnt">
                                <input type="file" name="images" id="file0" multiple="multiple" />
                            </span>
                        </div>
                    </ul>
                </div>
                <i class="fa fa-angle-right leibie"></i>
                <div class="add_main ">
                    <div class="form-group">
                        <label for="">类别</label>
                        <select name="Grade" id="Grade" ng-model="user.Grade" required class="form-control normal_select">
                            <option value="1" >正常</option>
                            <option value="2" >损坏</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">其他描述</label>
                        <textarea class="form-control xuan_type"  name="bad_description" style=""  placeholder="请输入描述"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="w_94 submit_box">
            <input type="hidden" name="BorrowingId" value="{{ @$BorrowingInfo['id'] }}">
            <input type="submit" class="btn2" value="确定"/>
        </div>

    </form>
<script type="text/javascript">
    $(function(){
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
                        $('.add_img').before('<li><img src="'+(url+data.data.path)+'" width="100%" ><i class="fa fa-remove font14 del_img"></i><input type="hidden" name="images[]" value="'+data.data.path+'"></li>');
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
    })

</script>
@stop