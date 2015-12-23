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
                var id = $this.attr("auditid");
                var url="{{ route('msc.personalCenter.getCancelLaboratory')}}";
                $.confirm({
                    title:'提示',
                    content: '是否取消预约实验室？',
                    confirmButton: '　　　是　　　 ' ,
                    cancelButton: '　　　否　　　',
                    confirmButtonClass: 'btn-info',
                    cancelButtonClass: 'btn-danger',
                    confirm:function(){
                        $.ajax({
                            type:"get",
                            async:true,
                            url:url+"?id="+id,
                            success:function($data){
                                console.log($data);
                                if($data.code == 1){
                                    $this.parent(".submit_box").parent(".form-group").parent(".add_main").remove();
                                }
                            },
                            error:function(){
                                console.log("error");
                            }
                        })
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
        我的实验室预约
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
            @foreach($list as $item)
                <div class="add_main">
                    <div class="form-group">
                        <label for="">教室名称</label>
                        <div class="txt">
                            {{ $item['lab']['name'] }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">教室编号</label>
                        <div class="txt">
                            {{ $item['lab']['code'] }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">状态</label>
                      <div class="txt">
                          @if($item['status']=="0")
                                <span>待审核</span>
                              @else
                                <span style="color: #21b9bb">已通过</span>
                          @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">预约时段</label>
                        <div class="txt">
                            {{ $item['apply_date'] }} {{ substr($item['calendar']['begintime'],0,5) }}-{{ substr($item['calendar']['endtime'],0,5) }}
                        </div>
                        <div class="submit_box">
                            @if($item['status']=="0")
                                <button class="btn2 cancel"  type="button" auditid="{{ $item['id'] }}">取消</button>
                            @else
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
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