@extends('msc::wechat.layouts.admin')
@section('only_head_css')

<link href="{{asset('msc/wechat/resourceborrow/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>

@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
@stop
@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    设备外借申请
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<div id="info_list">
    <div id="Equipment_info" class="w_94" style="">
        <form name="form"   id="frmTeacher" action="{{action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@postAddBorrowApply')}}" method="post" >
            <p  class="mart_3">需要外借的设备</p>
            <div>
                <select name="resources_tool_id"  id="teacher_dept" placeholder="设备名称" style="width:100%;"></select>
            </div>
            <p  class="mart_8">外借时段</p>
            <div class="time_select">
                <p><label for="">开始日期:</label><input id="star_time" name="begindate" type="date" placeholder="起时间"/></p>
                <div class="clear"></div>
                <p><label for="">结束日期:</label><input id="end_time" name="enddate"  type="date" placeholder="终时间"/></p>
            </div>
            <p class="mart_8">外借理由</p>
            <div class="Reason">
                <textarea id="Reason_detail" name="detail" type="" placeholder="请输入外借理由"/></textarea>
            </div>
            <input class="btn2 mart_10 marb_10" type="submit"  value="提交申请" />
        </form>
    </div>
    <div class="wait mart_20" style="display: none;">
        <img src="{{asset('msc/wechat/common/img/waiting.png')}}" width="30%"/>
        <p>等待审核中</p>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#teacher_dept").select2({
            ajax: {
                url: "{{action('\Modules\Msc\Http\Controllers\WeChat\ResourcesManagerController@getResourcesNameList')}}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.rows,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
        function formatRepo (repo) {
            if (repo.loading) return repo.text;
            return  "<div class='select2-result-repository clearfix'>" +repo.name +"</div>";
        }
        function formatRepoSelection (repo) {
            $('#code').val( repo.code);
            $('#resources_id').val( repo.id);
            return repo.name;
        }
    });
</script>
@stop