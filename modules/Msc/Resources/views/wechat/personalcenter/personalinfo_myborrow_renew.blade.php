@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/renow.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

    <script>

        $(document).ready(function(){

        })



    </script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     	我的续借
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<form id="info_list" class="mart_5" action="{{ url('/msc/wechat/resources-manager/add-borrow-apply') }}" method="post" >
    <div class="add_main">
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                {{ $BorrowingInfo['resourcesTool']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备编号</label>
            <div class="txt">
                {{ $BorrowingInfo['resourcesToolItem']['code'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">预约时段</label>
            <div class="txt">
                {{ $BorrowingInfo['begindate'] }}-{{ $BorrowingInfo['enddate'] }}
            </div>
        </div>
    </div>

    <div id="renow_info" class="w_94">
        <p>选择需要续借时段</p>
        <div class="time_select">
            <p><label for="">开始日期:</label><input placeholder="开始日期" type="date" name="begindate" class="form-control" id="start"></p>
            <div class="clear"></div>
            <p><label for="">结束日期:</label><input placeholder="结束日期" type="date" name="enddate" class="form-control mart_10" id="end"></p>
        </div>
        <div class="clear"></div>

        <p>续借理由</p>
        <div class="Reason">
            <textarea id="Reason_detail" name="detail" placeholder="请输入续借理由"></textarea>
        </div>

        <input type="hidden" name="resources_tool_id" value="{{ $BorrowingInfo['resourcesTool']['id'] }}"/>
        <input type="hidden" name="pid"  value="{{ $BorrowingInfo['id'] }}"/>
        <input type="submit" class="btn1" value="确定"/>
    </div>

</form>



<script type="">
    $(document).ready(function(){
        $(".cancle").click(function(){
            $.alert({
                title: '提示：',
                content: '是否取消设备预约？',
                confirmButton: '　　　是　　　 ' ,
                cancelButton: '　　　否　　　',
                confirmButtonClass: 'btn-info',
                cancelButtonClass: 'btn-danger',
                confirm:function(){

                }
            })

        });

    })
</script>
@stop