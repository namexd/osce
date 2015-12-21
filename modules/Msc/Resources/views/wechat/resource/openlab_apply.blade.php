@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/returnmanagement.css')}}" rel="stylesheet" type="text/css" />
<style>
   .more-width{width: 25%!important;} 
   .less-width{width: 15%!important;}
   .jconfirm .jconfirm-box div.content{text-align: center;}
   .jconfirm.white .jconfirm-box .buttons{float:none;text-align: center;}
   .detail_list{padding: 0 0.125em 0;}
   .attention.more-width span{display: inline-block;}
   .attention.more-width .pass{width: 45%;float: left;}
   .attention.more-width .unpass{width: 55%;}
</style>
@stop
@section('only_head_js')
 <script>
     $(function(){

         /**
          *通过申请弹出框
          */
         $('.detail_list').on('click','.pass',function(){
             var id = $(this).parent().attr('value');
             $.confirm({
                 title: ' ',
                 content: '确定通过该申请？',
                 confirmButton: '　　　是　　　 ' ,
                 cancelButton: '　　　否　　　',
                 confirmButtonClass: 'btn-info',
                 cancelButtonClass: 'btn-danger',
                 confirm:function(){
                     //提交
                     $.ajax({
                         url:"{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postChangeOpenLabApplyStatus')}}",
                         type:"post",
                         dataType:"json",
                         cache:false,
                         data:{id:id,status:1},
                         success: function(res) {
                             if(res.code != 1){
                                 layer.alert((res.message).split(':')[1]);
                                 console.log(res.message);
                             }else{
                                 //成功的操作
                                 layer.alert('预约成功！');
                                 console.log('通过！')
                                 window.location.reload();
                             }
                         },

                     });
                 }
             });
         })

         /**
          *跳转页面
          */
         $('.detail_list').on('click','.unpass',function(){
             var id = $(this).parent().attr('value');
             location.href = "{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@getRefusedOpenLabApply')}}?id="+id;
         })

         /**
          *顶部滑动
          */
         var myScroll;
         function loaded() {
             myScroll = new iScroll('wrapper', {
                 momentum: true,
                 hScrollbar: false,
                 vScroll:false,
                 vScrollbar:false,
                 fadeScrollbar:false,
             });
         }

         //显示加载效果
         //$("#layer_loading").show();

         //第一页
         //var now_page="1";
         //初始化
         AjaxReq(1);

         /**
          *后台ajax请求数据
          *@param String  页码数
          */
         function AjaxReq(req){

             //显示加载效果
             $("#layer_loading").show();
             //请求
             $.ajax({
                 type:"get",
                 aysnc:true,
                 data:{page:req},
                 url:"{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@getOpenLabApplyListData')}}",
                 success:function(res){
                     if(res.code!=1){
                         console.log(res.message);
                     }else{
                         var html = '';
                         var data = res.data.rows;
                         for(var i in data){
                             var start = (data[i].original_begin_datetime).split(' ')[1];
                             var end = data[i].original_end_datetime;
                             html += '<li>'+
                                     '<a href="javascript:void(0)">'+
                                     '<div class="less-width"><span>'+data[i].name+'</span> </div>'+
                                     '<div class="more-width Time_slot"><p>'+(data[i].original_begin_datetime).split(' ')[0]+'</p><p>'+start.split(':')[0]+':'+start.split(':')[1]+'-'+end.split(':')[0]+':'+end.split(':')[1]+'</p></div>'+
                                     '<div class="less-width">'+data[i].applyer_name+'</div>'+
                                     '<div><span>'+data[i].detail+'</span></div>'+
                                     '<div class="attention more-width" value="'+data[i].id+'">'+
                                     '<span class="State1 pass">通过&nbsp;</span>'+
                                     '<span class="State1 unpass">不通过</span>'+
                                     '</div>'+
                                     '</a>'+
                                     '</li>';
                         }
                         $('.detail_list').find('ul').append(html);
                         $('#totalpages').attr({'data-current':res.data.page,'data-total':res.data.total});
                     }
                     //显示加载效果
                     $("#layer_loading").hide();
                 }
             });
         }


         /**
          *翻页效果
          */
         $(window).scroll(function(e){

             var page_height = $(document).height();
             //当前顶部到窗口顶部的距离
             var away_top = $(document).scrollTop();
             var window_height = $(window).height()
             //判断是否为底部
             if(away_top > 100){

                 //显示回到顶部
                 $("#go_top").show(400);
                 $('#go_top').click(function(){
                     $(document).scrollTop(0);
                 })
             }else{
                 $("#go_top").hide(200);
             }
             //判定到达底部
             var totalpages = $("#totalpages").attr('data-total');
             var now_page = $("#totalpages").attr('data-current');
             if(away_top >= (page_height - window_height)&&now_page<totalpages){
                 //页面+1
                 now_page = parseInt(now_page) + 1;
                 AjaxReq(now_page);
                 /*加载显示*/
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
        预约申请管理
    <a class="right header_btn" href="">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div id="info_list" class="mart_5">
    <div class="main_list" id="borrow_history">
        <div class="title_nav">
            <div class="title less-width">教室</div>
            <div class="title more-width">时间段</div>
            <div class="title less-width">申请人</div>
            <div class="title">理由</div>
            <div class="title option more-width">操作</div>
        </div>
        <div class="detail_list" data-total="1" data-current="0">
            <ul>
            </ul>
        </div>
    </div>
</div>



@stop