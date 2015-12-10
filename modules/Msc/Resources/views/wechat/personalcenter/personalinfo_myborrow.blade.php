@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/personalcenter/js/personalinfo_myborrow.js')}}"></script>

    <script>
        now_index="0";
        $(document).ready(function(){

            initcard();//表单切换

            $(".cancle").click(function(){
                var $this = $(this);
                var id = $this.attr('BorrowingId');
                $.confirm({
                    title: '提示：',
                    content: '是否取消此次外借申请？',
                    confirmButton: '　　　是　　　 ' ,
                    cancelButton: '　　　否　　　',
                    confirmButtonClass: 'btn-info',
                    cancelButtonClass: 'btn-danger',
                    confirm:function(){
                        $.ajax({
                            url:"{{ url('/msc/wechat/personal-center/cancel-borrowing') }}",
                            type:"post",
                            dataType:"json",
                            cache:false,
                            data:{id:id},
                            success: function(result) {
                                if(result.code == 1){
                                    $this.parents('.add_main').remove();
                                }else{
                                    alert(result.message);
                                }
                            },

                        });
                    }
                })

            });
            var now_page=1;
            var qj={page:now_page};//设置页码
            var url = "{{ url('/msc/wechat/personal-center/borrow-history-data') }}";
            var getdetail = "{{ url('/msc/wechat/resources-manager/borrow-history-detail') }}";
            gethistory(qj,url,getdetail);
            //判定到底底部
                $(window).scroll(function(e){
                    //判定是历史列表时执行翻页
                    if(away_top >= (page_height - window_height)&&now_page<totalpages&&now_index=="2"){
                        now_page++;
                        var qj={page:now_page};//设置页码
                        gethistory(qj,url,getdetail)
                        /*加载显示*/
                    }
                })

            function initcard(){//表单切换
                $("#thelist2 li").unbind("click").click(function(){
                    $(this).addClass("check").siblings().removeClass("check");
                    now_index=$(this).index();
                    $("#info_list>div").eq(now_index).show().siblings("div").hide();
                    //判定是历史列表时执行翻页
                });
            }

        })
        $(function(){
            $(".more_txt").click(function(){
                var h=parseInt($(".gn_txt").height());
                var height=parseInt($(".gn_txt span").height()+6);
                if(h==18){
                    $(".more_txt").css({transform:"rotate(90deg)"});
                    $(".gn_txt").animate({height:height},300);
                }else{
                    $(".more_txt").css({transform:"rotate(0deg)"});
                    $(".gn_txt").animate({height:"30px"},300);
                }
            })
        })


    </script>

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       我的外借
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
    <div id="wrapper2">
        <ul id="thelist2">
            <li class="check"> <span >当前外借信息</span></li>
            <li> <span>当前申请</span></li>
            <li> <span>历史外借信息</span></li>
        </ul>
    </div>

    <div id="info_list" class="mart_5">
        <div id="now_borrow">
            @foreach($nowBorrowingList as $val)

                <div class="add_main">
                    <div class="form-group">
                        <label for="">设备名称</label>
                        <div class="txt">
                            {{ $val['resourcesTool']['name'] }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">设备编号</label>
                        <div class="txt">
                            {{ $val['resourcesToolItem']['code'] }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">预约时段</label>
                        <div class="txt">
                            {{ @$val['begindate'] }}-{{ @$val['enddate'] }}
                        </div>
                        <div class="submit_box">
                            <a  class="btn2" href="{{ url('/msc/wechat/personal-center/renew?id='.$val['id']) }}">续借</a>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        <div id="borrow_attention" style="display: none;">
            @foreach($applyBorrowingList as $val)

            <div class="add_main">
                <div class="form-group">
                    <label for="">设备名称</label>
                    <div class="txt">
                        {{ $val['resourcesTool']['name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">设备编号</label>
                    <div class="txt">
                        {{ $val['resourcesToolItem']['code']}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        {{ @$val['begindate']}}-{{ @$val['enddate']}}
                    </div>
                    <div class="submit_box">
                        <button class="btn2 cancle" BorrowingId="{{ @$val['id'] }}" type="button">取消</button>
                    </div>

                </div>
                <div class="form-group">
                    <label for="">备注</label>
                    <div class="gn_txt">

                        <span class="font16">{{ @$val['detail']}}</span>

                        <div class="font18 clo9 more_txt">
                            <i class="fa fa-angle-right"></i>
                        </div>
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