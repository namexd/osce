@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
    <style>
        #thelist2 li{
            width: 50%;
        }
        .add_main .submit_box{
            top: 100px;
            right: 20px;
        }
    </style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/personalcenter/js/current_reser.js')}}"></script>
    <script>
        $(document).ready(function(){
            $(".cancel").click(function(){
                var $this = $(this);
                var id = $this.attr('auditid');
                var url="{{ route('msc.personalCenter.cancelOpenDeviceApply')}}";
                $.confirm({
                    title:'提示',
                    content: '是否取消预约设备？',
                    confirmButton: '　　　是　　　 ' ,
                    cancelButton: '　　　否　　　',
                    confirmButtonClass: 'btn-info',
                    cancelButtonClass: 'btn-danger',
                    confirm:function(){
                        location.href = url+"?id="+id;
                    }
                })

            });

            //翻页
            var now_index=0;
            $("#thelist2 li").unbind("click").click(function(){
                    $(this).addClass("check").siblings().removeClass("check");
                    now_index=$(this).index();
                    $("#info_list>div").eq(now_index).show().siblings("div").hide();
                    if(now_index==1){
                       //判断是历史列表时执行
                        $(".detail_list li").remove();
                        var now_page=1;
                        var qj={page:now_page};//设置页码
                        var url = "{{ route('msc.personalCenter.userOpenDeviceHistroyData') }}";

                        gethistory(qj,url);
                        //判定到底底部
                        $(window).scroll(function(e){

                            //执行翻页
                            var totalpages = $("#totalpages").text();
                            if(away_top >= (page_height - window_height)&&now_page<totalpages&&now_index==1){
                                now_page++;
                                var qj={page:now_page};//设置页码
                                gethistory(qj,url);
                            }
                        })
                    }
                });







        })



    </script>

@stop
@section('content')

    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        我的设备预约
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
    <div id="wrapper2">
        <ul id="thelist2">
            <li class="check"> <span>当前预约信息</span></li>
            <li><span>历史预约信息</span></li>
        </ul>
    </div>

    <div id="info_list" class="mart_5">
        <div id="now_borrow">
                @forelse($list as $item)
                <input type="hidden" value="{{$item["id"]}}">
                <div class="add_main">
                    <div class="form-group">
                        <label for="">设备名称</label>
                        <div class="txt">
                            {{$item->resourcesLabDevices->name}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">设备编号</label>
                        <div class="txt">
                            {{$item->resourcesLabDevices->code}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">预约时段</label>
                        <div class="txt">
                            {{date('m.d',strtotime($item['original_begin_datetime']))}}-{{date('m.d',strtotime($item['original_end_datetime']))}}
                        </div>
                        <div class="submit_box">
                            <button class="btn2 cancel"  type="button" auditid="{{$item["id"]}}">取消
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                @endforelse
        </div>

        <div class="main_list"  style="display: none;">
            <div class="title_nav">
                <div class=" title">模型</div>
                <div class=" title">编号</div>
                <div class=" title">使用时段</div>
            </div>
            <div class="detail_list">
                <ul>
                </ul>
            </div>
        </div>
    </div>
@stop